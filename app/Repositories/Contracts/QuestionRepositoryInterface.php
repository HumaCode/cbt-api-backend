<?php

namespace App\Repositories\Contracts;

use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface QuestionRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function all(array $filters = []): \Illuminate\Database\Eloquent\Collection;
    public function find(string $id): ?Question;
    public function create(array $data): Question;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
}

