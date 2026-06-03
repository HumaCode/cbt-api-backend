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
}
