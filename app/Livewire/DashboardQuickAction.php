<?php

namespace App\Livewire;

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
     * Controls the custom confirmation modal visibility.
     */
    public bool $showConfirmationModal = false;

    /**
     * Controls the success modal visibility after a successful attendance save.
     */
    public bool $showSuccessModal = false;

    /**
     * Stores the action that the user is currently confirming.
     */
    public ?string $pendingAction = null;

    /**
     * Holds the small success payload shown after an action is saved.
     *
     * @var array<string, mixed>
     */
    public array $successState = [];

    /**
     * Preview or recorded time shown in confirmation and success modals.
     */
    public ?string $actionTimeLabel = null;

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
     * Open the custom confirmation modal instead of relying on browser confirm().
     */
    public function openConfirmation(): void
    {
        // The button state already knows the next legal action for today.
        $action = $this->state['action'] ?? null;

        if (! is_string($action) || $action === '') {
            return;
        }

        // Opening the modal also refreshes the preview time and clears stale validation errors.
        $this->resetErrorBag('attendance');
        $this->pendingAction = $action;
        $this->actionTimeLabel = now()->format('g:i A');
        $this->showConfirmationModal = true;
        $this->showSuccessModal = false;
    }

    /**
     * Close the confirmation modal without saving anything.
     */
    public function closeConfirmation(): void
    {
        // Closing the modal should fully clear temporary confirmation state.
        $this->showConfirmationModal = false;
        $this->pendingAction = null;
        $this->actionTimeLabel = null;
        $this->resetErrorBag('attendance');
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
        $action = $this->pendingAction ?? $this->state['action'] ?? null;

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

        // Capture the completed action metadata before refreshing state.
        $completedActionState = $this->state;

        // Refresh the next-action state before showing the success modal.
        $this->state = $attendanceActionState->forUser($user);

        // Hide the confirmation state and show a styled success state instead.
        $this->showConfirmationModal = false;
        $this->showSuccessModal = true;
        $this->successState = $this->buildSuccessState($action, $completedActionState);
    }

    /**
     * Close the success modal and refresh the dashboard route so cards and logs
     * outside this Livewire component also update.
     */
    public function closeSuccess(): void
    {
        $this->showSuccessModal = false;
        $this->pendingAction = null;
        $this->actionTimeLabel = null;

        // A hard route refresh updates the cards and recent logs outside this component.
        $this->redirectRoute('home', navigate: false);
    }

    /**
     * Build the text shown inside the success modal.
     *
     * @return array<string, string>
     */
    private function buildSuccessState(string $action, array $completedActionState): array
    {
        // Prefer the richer text from AttendanceActionState, then fall back to a generic label.
        return [
            'title' => (string) ($completedActionState['successTitle'] ?? ucfirst(str_replace('_', ' ', $action)) . ' recorded'),
            'description' => (string) ($completedActionState['successDescription'] ?? 'Your attendance record has been updated successfully.'),
        ];
    }

    /**
     * Render the Livewire Blade view.
     */
    public function render()
    {
        return view('pages.student.dashboard.dashboard-quick-action');
    }
}