<?php

namespace App\Repositories\Contracts;

use App\Models\Assessment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AssessmentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function find(string $id): ?Assessment;
    public function create(array $data): Assessment;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
}
