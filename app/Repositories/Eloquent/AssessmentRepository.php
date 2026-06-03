<?php

namespace App\Repositories\Eloquent;

use App\Models\Assessment;
use App\Repositories\Contracts\AssessmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AssessmentRepository implements AssessmentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Assessment::with(['groups', 'questions']);

        $userId = $filters['user_id'] ?? auth('api')->id();
        if ($userId) {
            $query->with(['sessions' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }]);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['active'])) {
            $now = now();
            if ($filters['active']) {
                $query->where('start_date', '<=', $now)
                      ->where('end_date', '>=', $now);
            }
        }

        if (!empty($filters['user_id'])) {
            $query->whereHas('groups', function ($q) use ($filters) {
                $q->whereHas('users', function ($uq) use ($filters) {
                    $uq->where('users.id', $filters['user_id']);
                });
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function all(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Assessment::with(['groups', 'questions']);

        $userId = $filters['user_id'] ?? auth('api')->id();
        if ($userId) {
            $query->with(['sessions' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }]);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['active'])) {
            $now = now();
            if ($filters['active']) {
                $query->where('start_date', '<=', $now)
                      ->where('end_date', '>=', $now);
            }
        }

        if (!empty($filters['user_id'])) {
            $query->whereHas('groups', function ($q) use ($filters) {
                $q->whereHas('users', function ($uq) use ($filters) {
                    $uq->where('users.id', $filters['user_id']);
                });
            });
        }

        return $query->latest()->get();
    }


    public function find(string $id): ?Assessment
    {
        return Assessment::with(['groups', 'questions'])->find($id);
    }

    public function create(array $data): Assessment
    {
        return Assessment::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $assessment = Assessment::find($id);
        if (!$assessment) {
            return false;
        }
        return $assessment->update($data);
    }

    public function delete(string $id): bool
    {
        $assessment = Assessment::find($id);
        if (!$assessment) {
            return false;
        }
        return $assessment->delete();
    }
}
