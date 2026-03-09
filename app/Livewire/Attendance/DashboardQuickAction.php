<?php

namespace App\Livewire\Attendance;

use App\Exceptions\AttendanceException;
use App\Services\AttendanceService;
use App\Support\AttendanceActionState;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Floating dashboard attendance button.
 *
 * This component has only one job: show the current next action and submit it.
 * The business rules stay in AttendanceService and AttendanceActionState.
 */
class DashboardQuickAction extends Component
{
    /**
     * UI-ready state used by the Blade view.
     *
     * Example fields:
     * - action
     * - label
     * - description
     * - icon
     * - tone
     * - isComplete
     *
     * @var array<string, mixed>
     */
    public array $state = [];

    /**
     * Load the current state when the component first appears.
     */
    public function mount(AttendanceActionState $attendanceActionState): void
    {
        // Dashboard attendance actions require an authenticated user.
        $user = Auth::user();

        abort_if(! $user, 403);

        // Build the button state from today's attendance record.
        $this->state = $attendanceActionState->forUser($user);
    }

    /**
     * Record the next allowed attendance action.
     *
     * The button itself decides nothing about validity. It simply asks the
     * support class for the current action, then asks the service to save it.
     */
    public function mark(AttendanceService $attendanceService, AttendanceActionState $attendanceActionState): void
    {
        $user = Auth::user();

        abort_if(! $user, 403);

        // Read the next action from the previously computed state array.
        $action = $this->state['action'] ?? null;

        // If the day is complete, there is nothing left to submit.
        if (! is_string($action) || $action === '') {
            return;
        }

        try {
            // Delegate actual attendance writing and validation to the service.
            $attendanceService->mark(
                userId: $user->id,
                action: $action,
                actorUserId: $user->id,
                ipAddress: request()->ip(),
                userAgent: request()->userAgent(),
            );
        } catch (AttendanceException $exception) {
            // Show a readable validation/business-rule error in the widget.
            $this->addError('attendance', $exception->getMessage());

            // Refresh state in case another browser tab or a previous action
            // changed today's record.
            $this->state = $attendanceActionState->forUser($user);

            return;
        }

        // Flash a success message for the next full-page render.
        session()->flash('dashboard_notice', ucfirst(str_replace('_', ' ', $action)) . ' recorded successfully.');

        // Redirect back to the dashboard so all summary cards and logs refresh.
        $this->redirectRoute('home', navigate: false);
    }

    /**
     * Render the Livewire Blade view.
     */
    public function render()
    {
        return view('livewire.attendance.dashboard-quick-action');
    }
}