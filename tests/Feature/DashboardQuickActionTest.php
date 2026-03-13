<?php

namespace Tests\Feature;

use App\Livewire\DashboardQuickAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardQuickActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirmation_modal_can_be_opened_for_the_next_action(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne([
            'role' => 'student',
            'status' => 'approved',
            'number_of_hours' => 400,
        ]);

        $this->actingAs($user);

        Livewire::test(DashboardQuickAction::class)
            ->call('captureLocation', 14.5995, 120.9842)
            ->call('openConfirmation')
            ->assertSet('showConfirmationModal', true)
            ->assertSet('pendingAction', 'time_in')
            ->assertSet('pendingLatitude', 14.5995)
            ->assertSet('pendingLongitude', 120.9842);
    }

    public function test_confirming_an_action_creates_the_record_and_shows_success_modal(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne([
            'role' => 'student',
            'status' => 'approved',
            'number_of_hours' => 400,
        ]);

        $this->actingAs($user);

        Livewire::test(DashboardQuickAction::class)
            ->call('captureLocation', 14.5995, 120.9842)
            ->call('openConfirmation')
            ->call('mark')
            ->assertSet('showConfirmationModal', false)
            ->assertSet('showSuccessModal', true)
            ->assertSet('state.action', 'lunch_out')
            ->assertSet('capturedLatitude', null)
            ->assertSet('capturedLongitude', null);

        $this->assertDatabaseHas('daily_time_records', [
            'user_id' => $user->id,
            'time_in_latitude' => 14.5995,
            'time_in_longitude' => 120.9842,
        ]);
    }

    public function test_capturing_location_marks_the_component_ready_for_attendance_logging(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne([
            'role' => 'student',
            'status' => 'approved',
            'number_of_hours' => 400,
        ]);

        $this->actingAs($user);

        Livewire::test(DashboardQuickAction::class)
            ->call('captureLocation', 14.5995, 120.9842)
            ->assertSet('capturedLatitude', 14.5995)
            ->assertSet('capturedLongitude', 120.9842)
            ->assertSet('showLocationErrorModal', false)
            ->assertSeeText('WFH timeout rule');
    }

    public function test_location_error_modal_is_shown_when_no_coordinates_are_captured(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne([
            'role' => 'student',
            'status' => 'approved',
            'number_of_hours' => 400,
        ]);

        $this->actingAs($user);

        Livewire::test(DashboardQuickAction::class)
            ->call('openConfirmation')
            ->assertSet('showConfirmationModal', false)
            ->assertSet('showLocationErrorModal', true)
            ->assertSet('locationErrorMessage', 'Read your current GPS location first, then continue with this attendance action.');
    }
}