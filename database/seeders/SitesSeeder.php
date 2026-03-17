<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Support\SiteLocationData;
use Illuminate\Database\Seeder;

class SitesSeeder extends Seeder
{
    public function run(): void
    {
        /** @var SiteLocationData $locationData */
        $locationData = app(SiteLocationData::class);

        $sites = [
            [
                'company_name' => 'Main OJT Site',
                'address' => ['full' => 'Ayala Avenue, Makati'],
                'allowed_radius_m' => 100,
                'enforce_geofence' => true,
                'wfh_anchor_enforced' => true,
                'wfh_anchor_limit_m' => 20,
                'is_active' => true,
                'latitude' => 14.554729,
                'longitude' => 121.024445,
            ],
            [
                'company_name' => 'Branch Office',
                'address' => ['full' => 'Ortigas Center, Pasig'],
                'allowed_radius_m' => 150,
                'enforce_geofence' => true,
                'wfh_anchor_enforced' => true,
                'wfh_anchor_limit_m' => 50,
                'is_active' => true,
                'latitude' => 14.586690,
                'longitude' => 121.060530,
            ],
        ];

        foreach ($sites as $site) {
            $existing = Site::query()->where('company_name', $site['company_name'])->first();

            if ($existing) {
                $existing->update($locationData->payload($site));
                continue;
            }

            Site::query()->create($locationData->payload($site));
        }
    }
}

