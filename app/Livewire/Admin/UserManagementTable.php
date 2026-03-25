<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagementTable extends Component
{
    use WithPagination;

    // Keep the admin list compact enough to feel fast, especially on mobile.
    private const PER_PAGE = 10;

    public string $search = '';

    public string $roleFilter = 'all';

    public string $statusFilter = 'all';

    public string $profileFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => 'all'],
        'statusFilter' => ['except' => 'all'],
        'profileFilter' => ['except' => 'all'],
    ];

    public function mount(string $role = 'all'): void
    {
        if (in_array($role, ['all', 'admin', 'student'], true)) {
            $this->roleFilter = $role;
        }
    }

    /**
     * Return to the first page whenever the search term changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination so a narrower role filter does not land on an empty page.
     */
    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination after status changes for the same reason as other filters.
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Keep the current page valid when the profile-completion filter changes.
     */
    public function updatedProfileFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Build the filtered user list together with the summary counters shown above it.
     */
    public function render()
    {
        $users = User::query()
            // Each filter is optional so admins can combine them freely.
            ->when($this->roleFilter !== 'all', fn ($query) => $query->where('role', $this->roleFilter))
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->profileFilter !== 'all', fn ($query) => $query->where('profile_completed', $this->profileFilter === 'complete'))
            ->when($this->search !== '', function ($query): void {
                // Search spans the most common admin lookup fields in one grouped condition.
                $search = '%' . $this->search . '%';

                $query->where(function ($userQuery) use ($search): void {
                    $userQuery
                        ->where('first_name', 'like', $search)
                        ->orWhere('middle_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('username', 'like', $search)
                        ->orWhere('student_code', 'like', $search)
                        ->orWhere('course', 'like', $search);
                });
            })
            ->latest()
            ->paginate(self::PER_PAGE);

        // Summary cards intentionally stay unfiltered so the page always shows full-system totals.
        return view('livewire.admin.user-management-table', [
            'users' => $users,
            'counts' => [
                'all' => User::query()->count(),
                'students' => User::query()->where('role', 'student')->count(),
                'admins' => User::query()->where('role', 'admin')->count(),
                'pending' => User::query()->where('status', 'pending')->count(),
            ],
        ]);
    }
}