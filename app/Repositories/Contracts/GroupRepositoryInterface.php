<?php

namespace App\Repositories\Contracts;

use App\Models\Group;

interface GroupRepositoryInterface
{
    /**
     * Get all groups with users count.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithCount(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Find a group by ID.
     *
     * @param string $id
     * @return Group|null
     */
    public function find(string $id): ?Group;

    /**
     * Create a new group.
     *
     * @param array $data
     * @return Group
     */
    public function create(array $data): Group;

    /**
     * Update an existing group.
     *
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete a group.
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool;
}
