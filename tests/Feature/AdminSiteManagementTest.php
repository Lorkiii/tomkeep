<?php

namespace Tests\Feature;

use App\Models\Site;
use App\Models\User;
use App\Support\SiteLocationData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminSiteManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The index and edit pages should remain usable even after the geometry
     * abstraction starts handling server-specific location expressions.
     */
    public function test_admin_can_view_site_management_index_and_edit_page(): void
    {
        $admin = User::query()->create($this->adminPayload());
        $site = $this->createSiteRecord([
            'company_name' => 'Acme Internship Hub',
            'address' => [
                'street_address' => '100 Corporate Avenue',
                'barangay' => 'San Jose',
                'municipality' => 'Quezon City',
                'province' => 'Metro Manila',
            ],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.sites.index'))
            ->assertOk()
            ->assertSeeText('Site Management')
            ->assertSeeText('Acme Internship Hub');

        $this->actingAs($admin)
            ->get(route('admin.sites.edit', $site))
            ->assertOk()
            ->assertSeeText('Geofence Settings')
            ->assertSeeText('Acme Internship Hub');
    }

    public function test_admin_can_create_a_site_and_write_an_audit_log(): void
    {
        $admin = User::query()->create($this->adminPayload());

        $response = $this->actingAs($admin)->post(route('admin.sites.store'), [
            'company_name' => 'Northgate Solutions',
            'street_address' => '45 Enterprise Street',
            'barangay' => 'San Antonio',
            'municipality' => 'Pasig City',
            'province' => 'Metro Manila',
            'allowed_radius_m' => 180,
            'latitude' => 14.5764,
            'longitude' => 121.0851,
            'enforce_geofence' => '0',
            'is_active' => '1',
        ]);

        $site = Site::query()->firstOrFail();
        $coordinates = app(SiteLocationData::class)->coordinatesFor($site);

        $response->assertRedirect(route('admin.sites.edit', $site));

        // Successful coordinate readback proves the geometry write path matched
        // the current database server and remained parseable afterward.
        $this->assertSame('Northgate Solutions', $site->company_name);
        $this->assertSame(180, $site->allowed_radius_m);
        $this->assertFalse($site->enforce_geofence);
        $this->assertTrue($site->is_active);
        $this->assertSame('Pasig City', $site->address['municipality']);
        $this->assertEqualsWithDelta(14.5764, $coordinates['latitude'] ?? 0.0, 0.0001);
        $this->assertEqualsWithDelta(121.0851, $coordinates['longitude'] ?? 0.0, 0.0001);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'site_created',
            'user_id' => $admin->id,
            'model_type' => 'Site',
            'model_id' => $site->id,
        ]);
    }

    public function test_admin_can_update_and_deactivate_a_site(): void
    {
        $admin = User::query()->create($this->adminPayload());
        $site = $this->createSiteRecord([
            'company_name' => 'Southpoint Training Center',
            'allowed_radius_m' => 120,
            'latitude' => 14.5547,
            'longitude' => 121.0244,
            'enforce_geofence' => true,
            'is_active' => true,
        ]);

        $updateResponse = $this->actingAs($admin)->patch(route('admin.sites.update', $site), [
            'company_name' => 'Southpoint Internship Center',
            'street_address' => '88 Pioneer Street',
            'barangay' => 'Kapitolyo',
            'municipality' => 'Pasig City',
            'province' => 'Metro Manila',
            'allowed_radius_m' => 220,
            'latitude' => 14.5678,
            'longitude' => 121.0634,
            'enforce_geofence' => '0',
            'is_active' => '1',
        ]);

        $updateResponse->assertRedirect(route('admin.sites.edit', $site));

        $site->refresh();
        $coordinates = app(SiteLocationData::class)->coordinatesFor($site);

        // Updating coordinates exercises the same compatibility layer used on
        // creation, so both write paths stay covered by one focused test file.
        $this->assertSame('Southpoint Internship Center', $site->company_name);
        $this->assertSame(220, $site->allowed_radius_m);
        $this->assertSame('Kapitolyo', $site->address['barangay']);
        $this->assertFalse($site->enforce_geofence);
        $this->assertTrue($site->is_active);
        $this->assertEqualsWithDelta(14.5678, $coordinates['latitude'] ?? 0.0, 0.0001);
        $this->assertEqualsWithDelta(121.0634, $coordinates['longitude'] ?? 0.0, 0.0001);

        $statusResponse = $this->actingAs($admin)->patch(route('admin.sites.status', $site), [
            'is_active' => '0',
        ]);

        $statusResponse->assertRedirect(route('admin.sites.edit', $site));

        $site->refresh();

        $this->assertFalse($site->is_active);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'site_updated',
            'user_id' => $admin->id,
            'model_type' => 'Site',
            'model_id' => $site->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'site_deactivated',
            'user_id' => $admin->id,
            'model_type' => 'Site',
            'model_id' => $site->id,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function adminPayload(array $overrides = []): array
    {
        return array_merge([
            'username' => 'admin' . uniqid(),
            'first_name' => 'System',
            'middle_name' => null,
            'last_name' => 'Admin',
            'gender' => null,
            'contact_number' => '09123456789',
            'address' => [
                'province' => 'Metro Manila',
                'city' => 'Manila',
                'barangay' => 'Barangay 1',
                'street' => 'Admin Street',
            ],
            'course' => null,
            'date_of_birth' => '1995-01-15',
            'school_attended' => null,
            'number_of_hours' => 0,
            'profile_completed' => true,
            'email' => 'admin' . uniqid() . '@mail.com',
            'password' => 'secret123',
            'role' => 'admin',
            'status' => 'approved',
            'approved_at' => now(),
            'admin_notes' => null,
            'is_active' => false,
        ], $overrides);
    }

    /**
     * Create a site record using the same geometry helper as production code so tests stay realistic.
     *
     * @param array<string, mixed> $overrides
     */
    private function createSiteRecord(array $overrides = []): Site
    {
        $attributes = array_merge([
            'company_name' => 'Default Internship Site',
            'address' => [
                'street_address' => '1 Example Street',
                'barangay' => 'Barangay Uno',
                'municipality' => 'Makati City',
                'province' => 'Metro Manila',
            ],
            'allowed_radius_m' => 150,
            'latitude' => 14.5547,
            'longitude' => 121.0244,
            'enforce_geofence' => true,
            'is_active' => true,
        ], $overrides);

        $siteLocationData = app(SiteLocationData::class);

        // Test inserts deliberately reuse the production payload builder so any
        // geometry regression is caught here instead of drifting from runtime.
        $siteId = DB::table('sites')->insertGetId([
            ...$siteLocationData->payload($attributes),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Site::query()->findOrFail($siteId);
    }
}