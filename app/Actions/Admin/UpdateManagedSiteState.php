<?php

namespace App\Actions\Admin;

use App\Models\AuditLog;
use App\Models\Site;
use App\Models\User;
use App\Support\SiteLocationData;
use Illuminate\Support\Facades\DB;

class UpdateManagedSiteState
{
    public function __construct(private readonly SiteLocationData $siteLocationData)
    {
    }

    /**
     * Toggle whether a site can still be used in future attendance and log the change.
     */
    public function execute(Site $managedSite, bool $isActive, User $actor): Site
    {
        $oldValues = $this->siteLocationData->snapshot($managedSite);

        return DB::transaction(function () use ($managedSite, $isActive, $actor, $oldValues): Site {
            // Only the availability flag changes here so the dedicated action stays focused.
            $managedSite->forceFill(['is_active' => $isActive])->save();

            $site = $managedSite->fresh();

            AuditLog::query()->create([
                'user_id' => $actor->id,
                'action' => $isActive ? 'site_activated' : 'site_deactivated',
                'model_type' => 'Site',
                'model_id' => $managedSite->id,
                'old_values' => $oldValues,
                'new_values' => $this->siteLocationData->snapshot($site),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $site;
        });
    }
}