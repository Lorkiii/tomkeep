<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

/**
 * File-based user storage for OJT app (no database).
 * Stores users in storage/app/ojt_users.json.
 */
class OjtUserStorage
{
    private string $path;

    public function __construct()
    {
        $this->path = storage_path('app/ojt_users.json');
    }

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        if (!is_file($this->path)) {
            return [];
        }
        $json = file_get_contents($this->path);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    public function findById(string $id): ?array
    {
        foreach ($this->all() as $user) {
            if (($user['id'] ?? '') === $id) {
                return $user;
            }
        }
        return null;
    }

    public function findByUsername(string $username): ?array
    {
        foreach ($this->all() as $user) {
            if (strcasecmp($user['username'] ?? '', $username) === 0) {
                return $user;
            }
        }
        return null;
    }

    public function findByEmail(string $email): ?array
    {
        foreach ($this->all() as $user) {
            if (strcasecmp($user['email'] ?? '', $email) === 0) {
                return $user;
            }
        }
        return null;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed> created user with id
     */
    public function create(array $data): array
    {
        $users = $this->all();
        $id = $data['id'] ?? str_replace('-', '', (string) \Illuminate\Support\Str::uuid());
        $password = $data['password'] ?? '';
        $user = [
            'id' => $id,
            'username' => $data['username'] ?? '',
            'email' => $data['email'] ?? '',
            'password' => $password ? Hash::make($password) : '',
            'first_name' => $data['first_name'] ?? 'Pending',
            'middle_name' => $data['middle_name'] ?? '',
            'last_name' => $data['last_name'] ?? 'User',
            'gender' => $data['gender'] ?? '',
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address' => $data['address'] ?? ['province' => '', 'municipality' => '', 'street' => '', 'house_number' => '', 'postal_code' => ''],
            'required_hours' => (int) ($data['required_hours'] ?? 0),
            'contact_number' => $data['contact_number'] ?? '',
            'school_attended' => $data['school_attended'] ?? '',
            'activity_logs' => $data['activity_logs'] ?? [],
        ];
        $users[] = $user;
        $this->write($users);
        return $user;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(string $id, array $data): void
    {
        $users = $this->all();
        foreach ($users as $i => $user) {
            if (($user['id'] ?? '') === $id) {
                $users[$i] = array_merge($user, $data);
                // Never store plain password: hash if provided, else keep existing
                if (array_key_exists('password', $data)) {
                    $users[$i]['password'] = $data['password'] ? Hash::make($data['password']) : ($user['password'] ?? '');
                }
                $this->write($users);
                return;
            }
        }
    }

    /**
     * @param array<int, array<string, mixed>> $users
     */
    private function write(array $users): void
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->path, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
