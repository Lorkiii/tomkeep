<?php

namespace App\Livewire\Admin;

use App\Models\Site;
use Livewire\Component;
use Livewire\WithPagination;

class SiteManagementTable extends Component
{
    use WithPagination;

    // Keeping pagination modest prevents the admin table from feeling heavy on smaller screens.
    private const PER_PAGE = 10;

    public string $search = '';

    public string $statusFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    /**
     * Reset pagination whenever the search term changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination whenever the active/inactive filter changes.
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Build the filtered site list and the top-level cards used on the index page.
     */
    public function render()
    {
        $sites = Site::query()
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('is_active', $this->statusFilter === 'active'))
            ->when($this->search !== '', function ($query): void {
                $search = '%' . $this->search . '%';

                // Company name is the fastest high-signal lookup for admins scanning sites.
                $query->where('company_name', 'like', $search);
            })
            ->latest()
            ->paginate(self::PER_PAGE);
            
        return view('livewire.admin.site-management-table', [
            'sites' => $sites,
            'counts' => [
                'all' => Site::query()->count(),
                'active' => Site::query()->where('is_active', true)->count(),
                'inactive' => Site::query()->where('is_active', false)->count(),
                'averageRadius' => (int) round((float) (Site::query()->avg('allowed_radius_m') ?? 0)),
            ],
        ]);
    }
}