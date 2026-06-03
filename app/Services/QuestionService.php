<?php

namespace App\Services;

use App\Repositories\Contracts\QuestionRepositoryInterface;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuestionService
{
    protected QuestionRepositoryInterface $questionRepository;

    public function __construct(QuestionRepositoryInterface $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    public function getQuestionsPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->questionRepository->paginate($perPage, $filters);
    }

    public function getQuestionById(string $id): ?Question
    {
        return $this->questionRepository->find($id);
    }

    public function createQuestion(array $data, ?array $options = null, ?array $attachments = null): Question
    {
        return DB::transaction(function () use ($data, $options, $attachments) {
            // Set current authenticated user id as creator
            $data['created_by'] = auth('api')->id();

            $question = $this->questionRepository->create($data);

            // Create Opsi Jawaban (if provided and PG type)
            if ($data['type'] === 'pg' && !empty($options)) {
                foreach ($options as $option) {
                    $question->options()->create([
                        'option_text' => $option['option_text'],
                        'is_correct' => $option['is_correct'] ?? false,
                        'weight' => $option['weight'] ?? 0.00,
                    ]);
                }
            }

            // Upload Attachments (if provided)
            if (!empty($attachments)) {
                foreach ($attachments as $file) {
                    $question->addMedia($file)->toMediaCollection('attachments');
                }
            }

            return $question->load(['category', 'options', 'media']);
        });
    }

    public function updateQuestion(string $id, array $data, ?array $options = null, ?array $attachments = null): bool
    {
        return DB::transaction(function () use ($id, $data, $options, $attachments) {
            $question = $this->questionRepository->find($id);
            if (!$question) {
                return false;
            }

            // Update main question properties
            $this->questionRepository->update($id, $data);

            // Sync Opsi Jawaban (if provided and PG type)
            if ($question->type === 'pg' && $options !== null) {
                // Delete existing options
                $question->options()->delete();

                // Insert new options
                foreach ($options as $option) {
                    $question->options()->create([
                        'option_text' => $option['option_text'],
                        'is_correct' => $option['is_correct'] ?? false,
                        'weight' => $option['weight'] ?? 0.00,
                    ]);
                }
            }

            // Handle new media attachments
            if (!empty($attachments)) {
                foreach ($attachments as $file) {
                    $question->addMedia($file)->toMediaCollection('attachments');
                }
            }

            return true;
        });
    }

    public function deleteQuestion(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $question = $this->questionRepository->find($id);
            if (!$question) {
                return false;
            }

            // Clean up Spatie media
            $question->clearMediaCollection('attachments');

            // Delete options first (handled by foreign keys cascade, but clean anyway)
            $question->options()->delete();

            return $this->questionRepository->delete($id);
        });
    }
}
