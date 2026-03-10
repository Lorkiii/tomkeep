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
            ->call('openConfirmation')
            ->assertSet('showConfirmationModal', true)
            ->assertSet('pendingAction', 'time_in');
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
            ->call('openConfirmation')
            ->call('mark')
            ->assertSet('showConfirmationModal', false)
            ->assertSet('showSuccessModal', true)
            ->assertSet('state.action', 'lunch_out');

        $this->assertDatabaseHas('daily_time_records', [
            'user_id' => $user->id,
        ]);
    }
}