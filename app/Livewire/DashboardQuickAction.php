<?php

namespace App\Livewire;

use App\Exceptions\AttendanceException;
use App\Services\AttendanceService;
use App\Support\AttendanceActionState;
use App\Support\AttendancePolicy;
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
     * Controls the friendly modal shown when GPS could not be captured.
     */
    public bool $showLocationErrorModal = false;

    /**
     * Stores the action that the user is currently confirming.
     */
    public ?string $pendingAction = null;

    /**
     * GPS coordinates captured before the user confirms the attendance action.
     */
    public ?float $pendingLatitude = null;

    public ?float $pendingLongitude = null;

    /**
     * GPS coordinates captured explicitly before the user is allowed to log.
     */
    public ?float $capturedLatitude = null;

    public ?float $capturedLongitude = null;

    /**
     * Small status label shown after GPS is captured successfully.
     */
    public ?string $capturedLocationLabel = null;

    /**
     * Global WFH timeout rule shown in the student helper text.
     */
    public int $wfhAnchorLimitMeters = 0;

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
     * User-facing message shown when location access fails.
     */
    public string $locationErrorMessage = 'We could not read your current location. Please allow location access and try again.';

    /**
     * Load the current state when the component first appears.
     */
    public function mount(AttendanceActionState $attendanceActionState, AttendancePolicy $attendancePolicy): void
    {
        // Dashboard attendance actions require an authenticated user.
        $user = Auth::user();

        abort_if(! $user, 403);

        // Build the button state from today's attendance record.
        $this->state = $attendanceActionState->forUser($user);
        $this->wfhAnchorLimitMeters = $attendancePolicy->wfhAnchorLimitMeters();
    }

    /**
     * Store the GPS reading first so the student clearly completes the
     * location step before the actual attendance action becomes available.
     */
    public function captureLocation(?float $latitude = null, ?float $longitude = null): void
    {
        if ($latitude === null || $longitude === null) {
            $this->locationFailed();

            return;
        }

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            $this->locationFailed('The captured GPS coordinates are invalid. Please try again.');

            return;
        }

        $this->capturedLatitude = $latitude;
        $this->capturedLongitude = $longitude;
        $this->capturedLocationLabel = now()->format('g:i A');
        $this->showLocationErrorModal = false;
        $this->resetErrorBag('attendance');
    }

    /**
     * Open the custom confirmation modal only after GPS has been captured.
     */
    public function openConfirmation(?float $latitude = null, ?float $longitude = null): void
    {
        // Keep backward compatibility in case a caller still passes the GPS
        // coordinates directly while the UI is transitioning to the two-step flow.
        if ($latitude !== null && $longitude !== null) {
            $this->captureLocation($latitude, $longitude);
        }

        // The button state already knows the next legal action for today.
        $action = $this->state['action'] ?? null;

        if (! is_string($action) || $action === '') {
            return;
        }

        if ($this->capturedLatitude === null || $this->capturedLongitude === null) {
            $this->locationFailed('Read your current GPS location first, then continue with this attendance action.');

            return;
        }

        // Opening the modal also refreshes the preview time and clears stale validation errors.
        $this->resetErrorBag('attendance');
        $this->pendingAction = $action;
        $this->pendingLatitude = $this->capturedLatitude;
        $this->pendingLongitude = $this->capturedLongitude;
        $this->actionTimeLabel = now()->format('g:i A');
        $this->showConfirmationModal = true;
        $this->showSuccessModal = false;
        $this->showLocationErrorModal = false;
    }
    /**
     * Close the confirmation modal without saving anything.
     */
    public function closeConfirmation(): void
    {
        // Closing the modal should fully clear temporary confirmation state.
        $this->showConfirmationModal = false;
        $this->pendingAction = null;
        $this->pendingLatitude = null;
        $this->pendingLongitude = null;
        $this->actionTimeLabel = null;
        $this->resetErrorBag('attendance');
    }
    /**
     * Show a friendly modal when the browser cannot provide GPS coordinates.
     */
    public function locationFailed(?string $message = null): void
    {
        $this->showConfirmationModal = false;
        $this->showSuccessModal = false;
        $this->pendingAction = null;
        $this->pendingLatitude = null;
        $this->pendingLongitude = null;
        $this->actionTimeLabel = null;
        $this->locationErrorMessage = $message ?: 'We could not read your current location. Please allow location access and try again.';
        $this->showLocationErrorModal = true;
    }

    public function closeLocationError(): void
    {
        $this->showLocationErrorModal = false;
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
                latitude: $this->pendingLatitude,
                longitude: $this->pendingLongitude,
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
        $this->capturedLatitude = null;
        $this->capturedLongitude = null;
        $this->capturedLocationLabel = null;
    }

    /**
     * Close the success modal and refresh the dashboard route so cards and logs
     * outside this Livewire component also update.
     */
    public function closeSuccess(): void
    {
        $this->showSuccessModal = false;
        $this->showLocationErrorModal = false;
        $this->pendingAction = null;
        $this->pendingLatitude = null;
        $this->pendingLongitude = null;
        $this->actionTimeLabel = null;
        $this->capturedLatitude = null;
        $this->capturedLongitude = null;
        $this->capturedLocationLabel = null;

        // A hard route refresh updates the cards and recent logs outside this component.
        $this->redirectRoute('home', navigate: false);
    }

    /**
     * Build the text shown inside the success modal.
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