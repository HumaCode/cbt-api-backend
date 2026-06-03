<?php

namespace App\Services;

use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class GroupService
{
    protected GroupRepositoryInterface $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Get list of groups.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGroups(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->groupRepository->allWithCount();
    }

    /**
     * Get group details by ID.
     *
     * @param string $id
     * @return Group|null
     */
    public function getGroupById(string $id): ?Group
    {
        return $this->groupRepository->find($id);
    }

    /**
     * Create a new group.
     *
     * @param array $data
     * @return Group
     */
    public function createGroup(array $data): Group
    {
        return $this->groupRepository->create($data);
    }

    /**
     * Update group details.
     *
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function updateGroup(string $id, array $data): bool
    {
        return $this->groupRepository->update($id, $data);
    }

    /**
     * Detach all relationships and delete group.
     *
     * @param string $id
     * @return bool
     */
    public function deleteGroup(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $group = $this->groupRepository->find($id);
            if (!$group) {
                return false;
            }

            $group->users()->detach();
            $group->assessments()->detach();
            
            return $this->groupRepository->delete($id);
        });
    }

    /**
     * Sync group users.
     *
     * @param string $id
     * @param array $userIds
     * @return Group|null
     */
    public function syncGroupMembers(string $id, array $userIds): ?Group
    {
        return DB::transaction(function () use ($id, $userIds) {
            $group = $this->groupRepository->find($id);
            if (!$group) {
                return null;
            }

            $group->users()->sync($userIds);
            return $group->load('users');
        });
    }
}
