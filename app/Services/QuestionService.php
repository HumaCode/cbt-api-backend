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

    public function createQuestion(array $data, ?array $options = null, ?array $attachments = null, ?array $optionAttachments = null): Question
    {
        return DB::transaction(function () use ($data, $options, $attachments, $optionAttachments) {
            // Set current authenticated user id as creator
            $data['created_by'] = auth('api')->id();

            $question = $this->questionRepository->create($data);

            // Create Opsi Jawaban (if provided and PG type)
            if ($data['type'] === 'pg' && !empty($options)) {
                foreach ($options as $index => $option) {
                    $newOption = $question->options()->create([
                        'option_text' => $option['option_text'] ?? '',
                        'is_correct' => $option['is_correct'] ?? false,
                        'weight' => $option['weight'] ?? 0.00,
                    ]);

                    // Upload Option Image (if provided)
                    if (!empty($optionAttachments) && isset($optionAttachments[$index])) {
                        $newOption->addMedia($optionAttachments[$index])->toMediaCollection('option_image');
                    }
                }
            }

            // Upload Attachments (if provided)
            if (!empty($attachments)) {
                foreach ($attachments as $file) {
                    $question->addMedia($file)->toMediaCollection('attachments');
                }
            }

            return $question->load(['category', 'options.media', 'media']);
        });
    }

    public function updateQuestion(string $id, array $data, ?array $options = null, ?array $attachments = null, ?array $optionAttachments = null): bool
    {
        return DB::transaction(function () use ($id, $data, $options, $attachments, $optionAttachments) {
            $question = $this->questionRepository->find($id);
            if (!$question) {
                return false;
            }

            // Update main question properties
            $this->questionRepository->update($id, $data);

            // Sync Opsi Jawaban (if provided and PG type)
            if ($question->type === 'pg' && $options !== null) {
                // Delete options not in the incoming list
                $incomingIds = collect($options)->pluck('id')->filter()->toArray();
                $question->options()->whereNotIn('id', $incomingIds)->get()->each(function ($opt) {
                    $opt->clearMediaCollection('option_image');
                    $opt->delete();
                });

                // Insert/Update options
                foreach ($options as $index => $option) {
                    $optData = [
                        'option_text' => $option['option_text'] ?? '',
                        'is_correct' => $option['is_correct'] ?? false,
                        'weight' => $option['weight'] ?? 0.00,
                    ];

                    if (!empty($option['id'])) {
                        $opt = $question->options()->find($option['id']);
                        if ($opt) {
                            $opt->update($optData);
                        } else {
                            $opt = $question->options()->create($optData);
                        }
                    } else {
                        $opt = $question->options()->create($optData);
                    }

                    // Clear Option Image (if requested)
                    if (!empty($option['clear_image'])) {
                        $opt->clearMediaCollection('option_image');
                    }

                    // Upload Option Image (if provided)
                    if (!empty($optionAttachments) && isset($optionAttachments[$index])) {
                        $opt->clearMediaCollection('option_image');
                        $opt->addMedia($optionAttachments[$index])->toMediaCollection('option_image');
                    }
                }
            }

            // Handle specific media deletions
            if (!empty($data['deleted_media_ids'])) {
                foreach ($data['deleted_media_ids'] as $mediaId) {
                    $mediaItem = $question->media()->find($mediaId);
                    if ($mediaItem) {
                        $mediaItem->delete();
                    }
                }
            }

            // Handle clear all media request
            if (!empty($data['clear_media'])) {
                $question->clearMediaCollection('attachments');
            }

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

            // Clean up options media and delete options
            $question->options->each(function ($opt) {
                $opt->clearMediaCollection('option_image');
                $opt->delete();
            });

            return $this->questionRepository->delete($id);
        });
    }
}
