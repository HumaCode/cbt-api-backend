<?php

namespace App\Repositories\Eloquent;

use App\Models\Group;
use App\Repositories\Contracts\GroupRepositoryInterface;

class GroupRepository implements GroupRepositoryInterface
{
    public function allWithCount(): \Illuminate\Database\Eloquent\Collection
    {
        return Group::withCount('users')->latest()->get();
    }

    public function find(string $id): ?Group
    {
        return Group::find($id);
    }

    public function create(array $data): Group
    {
        return Group::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $group = Group::find($id);
        if (!$group) {
            return false;
        }
        return $group->update($data);
    }

    public function delete(string $id): bool
    {
        $group = Group::find($id);
        if (!$group) {
            return false;
        }
        return $group->delete();
    }
}
