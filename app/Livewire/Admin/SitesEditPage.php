<?php

namespace App\Livewire\Admin;

use App\Models\Site;
use App\Support\AttendancePolicy;
use App\Support\SiteLocationData;
use Livewire\Component;

class SitesEditPage extends Component
{
    public Site $managedSite;

    /**
     * @var array{latitude: float|null, longitude: float|null}
     */
    public array $siteCoordinates = ['latitude' => null, 'longitude' => null];

    public int $wfhAnchorLimit = 20;

    public function mount(Site $managedSite, SiteLocationData $siteLocationData, AttendancePolicy $attendancePolicy): void
    {
        $this->managedSite = $managedSite;
        $this->siteCoordinates = $siteLocationData->coordinatesFor($managedSite);
        $this->wfhAnchorLimit = $attendancePolicy->wfhAnchorLimitMeters();
    }

    public function render()
    {
        $user = request()->user();

        abort_if(! $user, 403);

        return view('livewire.admin.pages.sites-edit', [
            'currentAdminUser' => $user->toArray(),
            'managedSite' => $this->managedSite,
            'siteCoordinates' => $this->siteCoordinates,
            'wfhAnchorLimit' => $this->wfhAnchorLimit,
        ]);
    }
}

