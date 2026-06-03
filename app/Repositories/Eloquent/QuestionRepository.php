<?php

namespace App\Repositories\Eloquent;

use App\Models\Question;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Question::with(['category', 'options.media', 'media']);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (!empty($filters['search'])) {
            $query->where('content_text', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate($perPage);
    }

    public function all(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Question::with(['category', 'options.media', 'media']);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (!empty($filters['search'])) {
            $query->where('content_text', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->get();
    }


    public function find(string $id): ?Question
    {
        return Question::with(['category', 'options.media', 'media'])->find($id);
    }

    public function create(array $data): Question
    {
        return Question::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $question = Question::find($id);
        if (!$question) {
            return false;
        }
        return $question->update($data);
    }

    public function delete(string $id): bool
    {
        $question = Question::find($id);
        if (!$question) {
            return false;
        }
        return $question->delete();
    }
}
