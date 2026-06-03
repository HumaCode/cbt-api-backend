<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitAnswerRequest;
use App\Services\AssessmentSessionService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AssessmentSessionController extends Controller
{
    protected AssessmentSessionService $sessionService;

    public function __construct(AssessmentSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * Start an assessment session for the authenticated user.
     *
     * @param string $assessmentId
     * @return JsonResponse
     */
    public function start(string $assessmentId): JsonResponse
    {
        try {
            $userId = auth('api')->id();
            $session = $this->sessionService->startSession($userId, $assessmentId);
            return ResponseHelper::success($session, 'Ujian berhasil dimulai.');
        } catch (ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), $e->errors(), 422);
        }
    }

    /**
     * Submit/Save an answer during the session.
     *
     * @param SubmitAnswerRequest $request
     * @param string $sessionId
     * @return JsonResponse
     */
    public function submitAnswer(SubmitAnswerRequest $request, string $sessionId): JsonResponse
    {
        try {
            $userId = auth('api')->id();
            $answer = $this->sessionService->submitAnswer($sessionId, $userId, $request->validated());
            return ResponseHelper::success($answer, 'Jawaban berhasil disimpan.');
        } catch (ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), $e->errors(), 422);
        }
    }

    /**
     * Start the timer for the session.
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function startTimer(string $sessionId): JsonResponse
    {
        try {
            $userId = auth('api')->id();
            $session = $this->sessionService->startTimer($sessionId, $userId);
            return ResponseHelper::success($session, 'Waktu ujian mulai berjalan.');
        } catch (ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), $e->errors(), 422);
        }
    }

    /**
     * Finish the assessment session.
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function finish(string $sessionId): JsonResponse
    {
        try {
            $userId = auth('api')->id();
            $session = $this->sessionService->finishSession($sessionId, $userId);
            return ResponseHelper::success($session, 'Ujian selesai dikerjakan.');
        } catch (ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), $e->errors(), 422);
        }
    }

    /**
     * Delete an assessment session.
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function destroy(string $sessionId): JsonResponse
    {
        $user = auth('api')->user();
        if (!$user || !$user->hasRole('Super Admin')) {
            return ResponseHelper::error('Akses ditolak. Hanya Super Admin yang dapat menghapus sesi.', null, 403);
        }

        $session = \App\Models\AssessmentSession::find($sessionId);
        if (!$session) {
            return ResponseHelper::error('Sesi tidak ditemukan.', null, 404);
        }

        $session->delete();

        return ResponseHelper::success(null, 'Sesi ujian berhasil dihapus.');
    }

    /**
     * Delete multiple assessment sessions in bulk.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function destroyBulk(\Illuminate\Http\Request $request): JsonResponse
    {
        $user = auth('api')->user();
        if (!$user || !$user->hasRole('Super Admin')) {
            return ResponseHelper::error('Akses ditolak. Hanya Super Admin yang dapat menghapus sesi.', null, 403);
        }

        $request->validate([
            'session_ids' => 'required|array',
            'session_ids.*' => 'exists:assessment_sessions,id',
        ]);

        $ids = $request->input('session_ids');
        \App\Models\AssessmentSession::whereIn('id', $ids)->delete();

        return ResponseHelper::success(null, 'Sesi-sesi ujian yang dipilih berhasil dihapus.');
    }
}
