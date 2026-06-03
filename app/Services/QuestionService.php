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

    public function getAllQuestions(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->questionRepository->all($filters);
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

    public function importQuestions(string $categoryId, array $rows): int
    {
        return DB::transaction(function () use ($categoryId, $rows) {
            $userId = auth('api')->id();
            $importedCount = 0;

            foreach ($rows as $row) {
                // Find content text
                $contentText = $row['soal'] ?? $row['content_text'] ?? $row['pertanyaan'] ?? $row['question'] ?? null;
                if (empty($contentText)) {
                    continue;
                }

                // Parse type
                $rawType = strtolower(trim($row['type'] ?? $row['tipe'] ?? 'pg'));
                $type = 'pg';
                if (in_array($rawType, ['essay', 'uraian'])) {
                    $type = 'essay';
                } elseif (in_array($rawType, ['likert'])) {
                    $type = 'likert';
                }

                // Parse difficulty
                $rawDiff = strtolower(trim($row['difficulty'] ?? $row['kesulitan'] ?? 'medium'));
                $difficulty = 'medium';
                if (in_array($rawDiff, ['easy', 'mudah'])) {
                    $difficulty = 'easy';
                } elseif (in_array($rawDiff, ['hard', 'sulit', 'tinggi'])) {
                    $difficulty = 'hard';
                }

                // Create Question
                $question = $this->questionRepository->create([
                    'category_id' => $categoryId,
                    'type' => $type,
                    'difficulty' => $difficulty,
                    'content_text' => $contentText,
                    'created_by' => $userId,
                ]);

                // If PG, parse options
                if ($type === 'pg') {
                    // Extract option texts
                    $optA = $row['option_a'] ?? $row['pilihan_a'] ?? $row['a'] ?? null;
                    $optB = $row['option_b'] ?? $row['pilihan_b'] ?? $row['b'] ?? null;
                    $optC = $row['option_c'] ?? $row['pilihan_c'] ?? $row['c'] ?? null;
                    $optD = $row['option_d'] ?? $row['pilihan_d'] ?? $row['d'] ?? null;
                    $optE = $row['option_e'] ?? $row['pilihan_e'] ?? $row['e'] ?? null;

                    $optionsData = [];
                    if ($optA !== null) $optionsData['A'] = $optA;
                    if ($optB !== null) $optionsData['B'] = $optB;
                    if ($optC !== null) $optionsData['C'] = $optC;
                    if ($optD !== null) $optionsData['D'] = $optD;
                    if ($optE !== null) $optionsData['E'] = $optE;

                    // Extract correct key
                    $correctKey = strtoupper(trim($row['correct_option'] ?? $row['jawaban_benar'] ?? $row['kunci'] ?? $row['kunci_jawaban'] ?? ''));

                    // Extract weights
                    $weightA = isset($row['weight_a']) ? (float)$row['weight_a'] : (isset($row['bobot_a']) ? (float)$row['bobot_a'] : null);
                    $weightB = isset($row['weight_b']) ? (float)$row['weight_b'] : (isset($row['bobot_b']) ? (float)$row['bobot_b'] : null);
                    $weightC = isset($row['weight_c']) ? (float)$row['weight_c'] : (isset($row['bobot_c']) ? (float)$row['bobot_c'] : null);
                    $weightD = isset($row['weight_d']) ? (float)$row['weight_d'] : (isset($row['bobot_d']) ? (float)$row['bobot_d'] : null);
                    $weightE = isset($row['weight_e']) ? (float)$row['weight_e'] : (isset($row['bobot_e']) ? (float)$row['bobot_e'] : null);

                    $weightsData = [
                        'A' => $weightA,
                        'B' => $weightB,
                        'C' => $weightC,
                        'D' => $weightD,
                        'E' => $weightE,
                    ];

                    foreach ($optionsData as $key => $optText) {
                        $isCorrect = ($correctKey === $key);
                        
                        // Calculate weight
                        $weight = $weightsData[$key];
                        if ($weight === null) {
                            $weight = $isCorrect ? 5.00 : 0.00;
                        }

                        $question->options()->create([
                            'option_text' => $optText,
                            'is_correct' => $isCorrect,
                            'weight' => $weight,
                        ]);
                    }
                }

                $importedCount++;
            }

            return $importedCount;
        });
    }
}
