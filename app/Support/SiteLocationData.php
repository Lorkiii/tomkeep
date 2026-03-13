<?php

namespace App\Support;

use App\Models\Site;
use Illuminate\Support\Facades\DB;

/**
 * Centralizes how site coordinates are stored and read across database drivers.
 */
class SiteLocationData
{
    /**
     * Cache the resolved server profile so repeated create/update reads do not
     * keep querying VERSION() inside the same request lifecycle.
     *
     * @var array{driver: string, is_mysql: bool, is_mariadb: bool, version: string}|null
     */
    private ?array $serverProfile = null;

    /**
     * Build the database payload shared by create and update workflows.
     *
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    public function payload(array $attributes): array
    {
        $address = collect($attributes['address'] ?? [])
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();

        return [
            'company_name' => $attributes['company_name'],
            'address' => $address === [] ? null : json_encode($address, JSON_THROW_ON_ERROR),
            'allowed_radius_m' => (int) $attributes['allowed_radius_m'],
            'enforce_geofence' => (bool) ($attributes['enforce_geofence'] ?? true),
            'is_active' => (bool) ($attributes['is_active'] ?? true),
            'location' => $this->pointExpression(
                latitude: (float) $attributes['latitude'],
                longitude: (float) $attributes['longitude'],
            ),
        ];
    }

    /**
     * Read a site back into a normalized snapshot that is safe for audit logs and forms.
     *
     * @return array<string, mixed>
     */
    public function snapshot(Site $site): array
    {
        return [
            'company_name' => $site->company_name,
            'address' => $site->address,
            'allowed_radius_m' => (int) $site->allowed_radius_m,
            'enforce_geofence' => (bool) $site->enforce_geofence,
            'is_active' => (bool) $site->is_active,
            ...$this->coordinatesFor($site),
        ];
    }

    /**
     * Fetch coordinates in a driver-agnostic shape for edit forms and assertions.
     *
     * @return array{latitude: float|null, longitude: float|null}
     */
    public function coordinatesFor(Site $site): array
    {
        $server = $this->serverProfile();

        // MySQL and MariaDB can both read from the POINT column with ST_X/ST_Y
        // once the geometry has been written using the server-compatible path.
        if (in_array($server['driver'], ['mysql', 'mariadb'], true)) {
            $row = DB::table('sites')
                ->selectRaw('ST_Y(location) as latitude, ST_X(location) as longitude')
                ->where('id', $site->id)
                ->first();

            return [
                'latitude' => isset($row?->latitude) ? (float) $row->latitude : null,
                'longitude' => isset($row?->longitude) ? (float) $row->longitude : null,
            ];
        }

        $rawPoint = (string) (DB::table('sites')->where('id', $site->id)->value('location') ?? '');

        if (preg_match('/POINT\(([-0-9.]+)\s+([-0-9.]+)\)/', $rawPoint, $matches) !== 1) {
            return ['latitude' => null, 'longitude' => null];
        }

        return [
            'longitude' => (float) $matches[1],
            'latitude' => (float) $matches[2],
        ];
    }

    /**
     * Format a portable point expression without exposing raw SQL construction to callers.
     */
    private function pointExpression(float $latitude, float $longitude): mixed
    {
        $formattedLatitude = $this->formatCoordinate($latitude);
        $formattedLongitude = $this->formatCoordinate($longitude);
        $server = $this->serverProfile();

        // MySQL accepts ST_SRID(Point(...), 4326), so we keep the stricter SRID
        // assignment there for forward-compatible spatial behavior.
        if ($server['is_mysql']) {
            return DB::raw("ST_SRID(Point({$formattedLongitude}, {$formattedLatitude}), 4326)");
        }

        // MariaDB 10.4 rejects the two-argument ST_SRID setter, but it does
        // accept ST_GeomFromText with an SRID argument for POINT creation.
        if ($server['is_mariadb']) {
            return DB::raw("ST_GeomFromText('POINT({$formattedLongitude} {$formattedLatitude})', 4326)");
        }

        // The string fallback remains in place for non-spatial drivers so the
        // rest of the app can still store and parse a consistent POINT shape.
        return "POINT({$formattedLongitude} {$formattedLatitude})";
    }

    /**
     * Resolve the real server flavor behind Laravel's mysql-compatible driver.
     *
     * Laravel reports both MySQL and MariaDB through the mysql driver, so we
     * inspect VERSION() once and then branch geometry behavior from that.
     *
     * @return array{driver: string, is_mysql: bool, is_mariadb: bool, version: string}
     */
    private function serverProfile(): array
    {
        if ($this->serverProfile !== null) {
            return $this->serverProfile;
        }

        $driver = DB::connection()->getDriverName();
        $version = '';

        // VERSION() gives us the concrete server implementation even when the
        // configured Laravel driver is simply "mysql".
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $row = DB::selectOne('SELECT VERSION() AS version');
            $version = (string) ($row->version ?? '');
        }

        $isMariaDb = str_contains(strtolower($version), 'mariadb');
        $isMySql = $driver === 'mysql' && ! $isMariaDb;

        return $this->serverProfile = [
            'driver' => $driver,
            'is_mysql' => $isMySql,
            'is_mariadb' => $isMariaDb || $driver === 'mariadb',
            'version' => $version,
        ];
    }

    /**
     * Keep float formatting consistent so geometry SQL stays valid across locales.
     */
    private function formatCoordinate(float $value): string
    {
        return rtrim(rtrim(number_format($value, 7, '.', ''), '0'), '.');
    }
}
