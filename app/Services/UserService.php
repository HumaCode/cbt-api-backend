<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get list of candidates (Peserta) with filters.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCandidates(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::role('Peserta');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['group_id'])) {
            $groupId = $filters['group_id'];
            $query->whereHas('groups', function ($q) use ($groupId) {
                $q->where('groups.id', $groupId);
            });
        }

        return $query->with('groups')->latest()->get();
    }

    /**
     * Create a new candidate user.
     *
     * @param array $data
     * @param array|null $groupIds
     * @return User
     */
    public function createCandidate(array $data, ?array $groupIds = null): User
    {
        return DB::transaction(function () use ($data, $groupIds) {
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'telp' => $data['telp'] ?? null,
                'gender' => $data['gender'] ?? null,
                'is_active' => '1',
            ]);

            $user->assignRole('Peserta');

            if (!empty($groupIds)) {
                $user->groups()->sync($groupIds);
            }

            return $user->load('groups');
        });
    }

    /**
     * Update an existing candidate user.
     *
     * @param string $id
     * @param array $data
     * @param array|null $groupIds
     * @return User|null
     */
    public function updateCandidate(string $id, array $data, ?array $groupIds = null): ?User
    {
        return DB::transaction(function () use ($id, $data, $groupIds) {
            $user = $this->userRepository->find($id);
            if (!$user) {
                return null;
            }

            $updateData = [
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'telp' => $data['telp'] ?? null,
                'gender' => $data['gender'] ?? null,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $this->userRepository->update($id, $updateData);

            if ($groupIds !== null) {
                $user->groups()->sync($groupIds);
            }

            return $user->load('groups');
        });
    }

    /**
     * Delete candidate user.
     *
     * @param string $id
     * @return bool
     */
    public function deleteCandidate(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = $this->userRepository->find($id);
            if (!$user) {
                return false;
            }

            $user->groups()->detach();
            return $this->userRepository->delete($id);
        });
    }

    /**
     * Import multiple candidates.
     *
     * @param array $rows
     * @return array
     */
    public function importCandidates(array $rows): array
    {
        return DB::transaction(function () use ($rows) {
            $importedCount = 0;
            $updatedCount = 0;

            foreach ($rows as $row) {
                $name = $row['nama_lengkap'] ?? $row['nama'] ?? $row['name'] ?? null;
                $username = $row['username'] ?? $row['user_name'] ?? null;
                $email = $row['email'] ?? $row['surel'] ?? null;
                $password = $row['password'] ?? $row['sandi'] ?? null;
                $telp = $row['no_telp'] ?? $row['telp'] ?? $row['phone'] ?? null;
                $genderInput = $row['jenis_kelamin'] ?? $row['gender'] ?? $row['jk'] ?? null;
                $groupsInput = $row['grup'] ?? $row['kelas'] ?? $row['groups'] ?? $row['group'] ?? null;

                if (empty($name) || empty($username) || empty($email)) {
                    continue; // Skip invalid rows
                }

                $gender = 'male';
                if (!empty($genderInput)) {
                    $gi = strtolower(trim($genderInput));
                    if ($gi === 'perempuan' || $gi === 'female' || $gi === 'p' || $gi === 'f') {
                        $gender = 'female';
                    }
                }

                $user = User::where('username', $username)->orWhere('email', $email)->first();

                if ($user) {
                    $user->update([
                        'name' => $name,
                        'telp' => $telp ?: $user->telp,
                        'gender' => $gender ?: $user->gender,
                    ]);
                    if (!empty($password)) {
                        $user->update(['password' => Hash::make($password)]);
                    }
                    $updatedCount++;
                } else {
                    $user = $this->userRepository->create([
                        'name' => $name,
                        'username' => $username,
                        'email' => $email,
                        'password' => Hash::make($password ?: '123456'),
                        'telp' => $telp,
                        'gender' => $gender,
                        'is_active' => '1',
                    ]);
                    $user->assignRole('Peserta');
                    $importedCount++;
                }

                if (!empty($groupsInput)) {
                    $groupNames = array_map('trim', explode(',', $groupsInput));
                    $groupIds = [];
                    foreach ($groupNames as $gName) {
                        if (empty($gName)) continue;
                        $group = \App\Models\Group::firstOrCreate([
                            'name' => $gName
                        ]);
                        $groupIds[] = $group->id;
                    }
                    $user->groups()->sync($groupIds);
                }
            }

            return [
                'imported_count' => $importedCount,
                'updated_count' => $updatedCount
            ];
        });
    }
}
