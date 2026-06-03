<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): ?Category;
    public function create(array $data): Category;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
}
