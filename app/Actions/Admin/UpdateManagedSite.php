<?php

namespace App\Actions\Admin;

use App\Models\AuditLog;
use App\Models\Site;
use App\Models\User;
use App\Support\SiteLocationData;
use Illuminate\Support\Facades\DB;

class UpdateManagedSite
{
    public function __construct(private readonly SiteLocationData $siteLocationData)
    {
    }

    /**
     * Persist site edits together with the before/after audit payload.
     *
     * @param array<string, mixed> $attributes
     */
    public function execute(Site $managedSite, array $attributes, User $actor): Site
    {
        $oldValues = $this->siteLocationData->snapshot($managedSite);

        return DB::transaction(function () use ($managedSite, $attributes, $actor, $oldValues): Site {
            // Updating through the table keeps the geometry write consistent with creation logic.
            DB::table('sites')
                ->where('id', $managedSite->id)
                ->update([
                    ...$this->siteLocationData->payload($attributes),
                    'updated_at' => now(),
                ]);

            $site = $managedSite->fresh();

            AuditLog::query()->create([
                'user_id' => $actor->id,
                'action' => 'site_updated',
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