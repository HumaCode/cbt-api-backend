<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Services\QuestionService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    protected QuestionService $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    /**
     * Display a listing of the questions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category_id', 'type', 'difficulty', 'search']);
        $perPage = $request->input('per_page', 15);

        $questions = $this->questionService->getQuestionsPaginated((int)$perPage, $filters);
        return ResponseHelper::success($questions, 'Daftar soal berhasil diambil.');
    }

    /**
     * Store a newly created question in storage.
     *
     * @param StoreQuestionRequest $request
     * @return JsonResponse
     */
    public function store(StoreQuestionRequest $request): JsonResponse
    {
        $data = $request->only(['category_id', 'type', 'difficulty', 'content_text']);
        $options = $request->input('options');
        $attachments = $request->file('attachments');
        $optionAttachments = $request->file('option_attachments');

        $question = $this->questionService->createQuestion($data, $options, $attachments, $optionAttachments);
        return ResponseHelper::success($question, 'Soal berhasil dibuat.', 201);
    }

    /**
     * Display the specified question.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $question = $this->questionService->getQuestionById($id);

        if (!$question) {
            return ResponseHelper::error('Soal tidak ditemukan.', null, 404);
        }

        return ResponseHelper::success($question, 'Detail soal berhasil diambil.');
    }

    /**
     * Update the specified question in storage.
     *
     * @param UpdateQuestionRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateQuestionRequest $request, string $id): JsonResponse
    {
        $data = $request->only(['category_id', 'type', 'difficulty', 'content_text', 'clear_media', 'deleted_media_ids']);
        $options = $request->input('options');
        $attachments = $request->file('attachments');
        $optionAttachments = $request->file('option_attachments');

        $updated = $this->questionService->updateQuestion($id, $data, $options, $attachments, $optionAttachments);

        if (!$updated) {
            return ResponseHelper::error('Soal tidak ditemukan atau gagal diperbarui.', null, 404);
        }

        $question = $this->questionService->getQuestionById($id);
        return ResponseHelper::success($question, 'Soal berhasil diperbarui.');
    }

    /**
     * Remove the specified question from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->questionService->deleteQuestion($id);

        if (!$deleted) {
            return ResponseHelper::error('Soal tidak ditemukan atau gagal dihapus.', null, 404);
        }

        return ResponseHelper::success(null, 'Soal berhasil dihapus.');
    }
}
