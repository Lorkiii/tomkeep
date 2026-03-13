<?php

namespace App\Actions\Admin;

use App\Models\AuditLog;
use App\Models\Site;
use App\Models\User;
use App\Support\SiteLocationData;
use Illuminate\Support\Facades\DB;

class CreateManagedSite
{
    public function __construct(private readonly SiteLocationData $siteLocationData)
    {
    }

    /**
     * Create a site record and log the full initial snapshot for traceability.
     *
     * @param array<string, mixed> $attributes
     */
    public function execute(array $attributes, User $actor): Site
    {
        return DB::transaction(function () use ($attributes, $actor): Site {
            // Query builder is used here so the geometry column can receive a raw point expression safely.
            $siteId = DB::table('sites')->insertGetId([
                ...$this->siteLocationData->payload($attributes),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $site = Site::query()->findOrFail($siteId);

            AuditLog::query()->create([
                'user_id' => $actor->id,
                'action' => 'site_created',
                'model_type' => 'Site',
                'model_id' => $site->id,
                'old_values' => null,
                'new_values' => $this->siteLocationData->snapshot($site),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $site;
        });
    }
}