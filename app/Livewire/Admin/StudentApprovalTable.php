<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StudentApprovalTable extends Component
{
    use WithPagination;

    // The approval queue stays intentionally short per page because each row is action-heavy.
    private const PER_PAGE = 10;

    private const ACTION_APPROVE = 'approve';

    private const ACTION_REJECT = 'reject';

    public string $statusFilter = 'pending';

    public string $search = '';

    /**
     * @var array<int, string>
     */
    public array $notes = [];

    public ?array $feedback = null;

    public bool $showConfirmationModal = false;

    public ?int $pendingStudentId = null;

    public ?string $pendingAction = null;

    public ?string $pendingStudentName = null;

    public bool $showProfileModal = false;

    /**
     * Lightweight payload for the profile modal.
     *
     * @var array<string, mixed>|null
     */
    public ?array $profileStudent = null;

    protected $queryString = [
        'statusFilter' => ['except' => 'pending'],
        'search' => ['except' => ''],
    ];

    /**
     * Reset pagination when the admin changes the queue search term.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when switching between pending, approved, rejected, and all.
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Validate approval prerequisites, then open the shared confirmation modal.
     */
    public function promptApprove(int $studentId): void
    {
        $student = $this->findStudent($studentId);

        if (! $student) {
            $this->setFeedback('error', 'The selected student could not be found.');

            return;
        }

        if ($student->status === 'approved') {
            $this->setFeedback('info', 'This student is already approved.');

            return;
        }

        if (! $student->profile_completed) {
            $this->setFeedback('error', 'The student must complete the profile before approval.');

            return;
        }

        $this->openConfirmationModal($student, self::ACTION_APPROVE);
    }

    /**
     * Reject requires notes, so validate those before opening the confirm step.
     */
    public function promptReject(int $studentId): void
    {
        $student = $this->findStudent($studentId);

        if (! $student) {
            $this->setFeedback('error', 'The selected student could not be found.');

            return;
        }

        if ($student->status === 'approved') {
            $this->setFeedback('error', 'Approved students cannot be rejected from this screen.');

            return;
        }

        if ($student->status === 'rejected') {
            $this->setFeedback('info', 'This student has already been rejected.');

            return;
        }

        $note = trim($this->notes[$studentId] ?? '');

        if ($note === '') {
            $this->setFeedback('error', 'Add admin notes before rejecting a student.');

            return;
        }

        $this->openConfirmationModal($student, self::ACTION_REJECT);
    }

    /**
     * Clear the temporary modal state without touching the database.
     */
    public function closeConfirmationModal(): void
    {
        $this->showConfirmationModal = false;
        $this->pendingStudentId = null;
        $this->pendingAction = null;
        $this->pendingStudentName = null;
    }

    /**
     * Open the profile modal for a student in the queue.
     */
    public function openProfile(int $studentId): void
    {
        $student = User::query()
            ->where('role', 'student')
            ->find($studentId);

        if (! $student) {
            $this->setFeedback('error', 'The selected student could not be found.');

            return;
        }

        $this->profileStudent = [
            'id' => $student->id,
            'name' => $this->displayName($student),
            'email' => $student->email,
            'username' => $student->username,
            'status' => $student->status,
            'profile_completed' => (bool) $student->profile_completed,
            'student_code' => $student->student_code,
            'contact_number' => $student->contact_number,
            'gender' => $student->gender,
            'date_of_birth' => optional($student->date_of_birth)->format('M j, Y'),
            'school_attended' => $student->school_attended,
            'course' => $student->course,
            'number_of_hours' => $student->number_of_hours,
            'address' => $student->address ?? [],
            'created_at' => optional($student->created_at)->format('M j, Y g:i A'),
            'approved_at' => optional($student->approved_at)->format('M j, Y g:i A'),
            'admin_notes' => $student->admin_notes,
        ];

        $this->showProfileModal = true;
    }

    public function closeProfileModal(): void
    {
        $this->showProfileModal = false;
        $this->profileStudent = null;
    }

    /**
     * Dispatch the stored modal action once the admin confirms it.
     */
    public function confirmPendingAction(): void
    {
        $studentId = $this->pendingStudentId;
        $action = $this->pendingAction;

        if (! $studentId || ! in_array($action, [self::ACTION_APPROVE, self::ACTION_REJECT], true)) {
            $this->closeConfirmationModal();

            return;
        }

        if ($action === self::ACTION_APPROVE) {
            $this->approve($studentId);
        }

        if ($action === self::ACTION_REJECT) {
            $this->reject($studentId);
        }

        $this->closeConfirmationModal();
    }

    /**
     * Persist a student approval and write an audit log entry.
     */
    private function approve(int $studentId): void
    {
        $student = $this->findStudent($studentId);

        if (! $student) {
            $this->setFeedback('error', 'The selected student could not be found.');

            return;
        }

        if ($student->status === 'approved') {
            $this->setFeedback('info', 'This student is already approved.');

            return;
        }

        if (! $student->profile_completed) {
            $this->setFeedback('error', 'The student must complete the profile before approval.');

            return;
        }

        $admin = Auth::user();

        abort_if(! $admin, 403);

        // Notes are optional on approval, but if present they should travel with the decision.
        $note = trim($this->notes[$studentId] ?? '');
        $oldValues = $this->snapshot($student);

        DB::transaction(function () use ($student, $admin, $note, $oldValues): void {
            // Approval metadata is what unlocks the student dashboard and code generation.
            $student->forceFill([
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'admin_notes' => $note !== '' ? $note : $student->admin_notes,
            ])->save();

            AuditLog::query()->create([
                'user_id' => $admin->id,
                'action' => 'student_approved',
                'model_type' => 'User',
                'model_id' => $student->id,
                'old_values' => $oldValues,
                'new_values' => $this->snapshot($student->fresh()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        // Clear the inline textarea once the action succeeds so the row is visually reset.
        $this->notes[$studentId] = '';
        $this->setFeedback('success', 'Student approved successfully.');
    }

    /**
     * Persist a student rejection and store the required rejection reason.
     */
    private function reject(int $studentId): void
    {
        $student = $this->findStudent($studentId);

        if (! $student) {
            $this->setFeedback('error', 'The selected student could not be found.');

            return;
        }

        if ($student->status === 'approved') {
            $this->setFeedback('error', 'Approved students cannot be rejected from this screen.');

            return;
        }

        if ($student->status === 'rejected') {
            $this->setFeedback('info', 'This student has already been rejected.');

            return;
        }

        $note = trim($this->notes[$studentId] ?? '');

        if ($note === '') {
            $this->setFeedback('error', 'Add admin notes before rejecting a student.');

            return;
        }

        $admin = Auth::user();

        abort_if(! $admin, 403);

        // Rejections always preserve the admin note because it explains the denial.
        $oldValues = $this->snapshot($student);

        DB::transaction(function () use ($student, $admin, $note, $oldValues): void {
            $student->forceFill([
                'status' => 'rejected',
                'approved_by' => null,
                'approved_at' => null,
                'admin_notes' => $note,
            ])->save();

            AuditLog::query()->create([
                'user_id' => $admin->id,
                'action' => 'student_rejected',
                'model_type' => 'User',
                'model_id' => $student->id,
                'old_values' => $oldValues,
                'new_values' => $this->snapshot($student->fresh()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        // Clear the note box after a successful rejection to avoid stale state in the UI.
        $this->notes[$studentId] = '';
        $this->setFeedback('success', 'Student rejected successfully.');
    }

    /**
     * Load the current approval queue and the high-level status counters.
     */
    public function render()
    {
        $students = User::query()
            ->where('role', 'student')
            // The page defaults to pending but still supports browsing the rest of the lifecycle.
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->search !== '', function ($query): void {
                // Search covers the identifiers admins usually have available during review.
                $search = '%' . $this->search . '%';

                $query->where(function ($studentQuery) use ($search): void {
                    $studentQuery
                        ->where('first_name', 'like', $search)
                        ->orWhere('middle_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('username', 'like', $search)
                        ->orWhere('student_code', 'like', $search);
                });
            })
            ->orderByRaw("case status when 'pending' then 0 when 'rejected' then 1 when 'approved' then 2 else 3 end")
            ->latest()
            ->paginate(self::PER_PAGE);

        return view('livewire.admin.student-approval-table', [
            'students' => $students,
            'counts' => [
                'pending' => User::query()->where('role', 'student')->where('status', 'pending')->count(),
                'approved' => User::query()->where('role', 'student')->where('status', 'approved')->count(),
                'rejected' => User::query()->where('role', 'student')->where('status', 'rejected')->count(),
            ],
        ]);
    }

    /**
     * Restrict lookups to student accounts because admins are not managed from this queue.
     */
    private function findStudent(int $studentId): ?User
    {
        return User::query()
            ->where('role', 'student')
            ->find($studentId);
    }

    /**
     * Keep the audit payload focused on the approval fields this component mutates.
     *
     * @return array<string, mixed>
     */
    private function snapshot(User $student): array
    {
        return $student->only([
            'status',
            'student_code',
            'profile_completed',
            'approved_by',
            'approved_at',
            'admin_notes',
        ]);
    }

    /**
     * Store a lightweight flash-style payload for the Blade alert block.
     */
    private function setFeedback(string $type, string $message): void
    {
        $this->feedback = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /**
     * Populate the modal state from the chosen student and action type.
     */
    private function openConfirmationModal(User $student, string $action): void
    {
        $this->pendingStudentId = $student->id;
        $this->pendingAction = $action;
        $this->pendingStudentName = $this->displayName($student);
        $this->showConfirmationModal = true;
    }

    /**
     * Build a readable name while tolerating partially completed student profiles.
     */
    private function displayName(User $student): string
    {
        $name = trim(collect([
            $student->first_name,
            $student->middle_name,
            $student->last_name,
        ])->filter()->implode(' '));

        return $name !== '' ? $name : 'Student Profile Incomplete';
    }
}