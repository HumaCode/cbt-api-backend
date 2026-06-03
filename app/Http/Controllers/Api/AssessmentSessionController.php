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

        $deleted = $this->sessionService->deleteSession($sessionId);
        if (!$deleted) {
            return ResponseHelper::error('Sesi tidak ditemukan.', null, 404);
        }

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

        $this->sessionService->deleteSessionsBulk($request->input('session_ids'));

        return ResponseHelper::success(null, 'Sesi-sesi ujian yang dipilih berhasil dihapus.');
    }

    /**
     * Unlock an assessment session.
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function unlock(string $sessionId): JsonResponse
    {
        $user = auth('api')->user();
        if (!$user || !$user->hasRole('Super Admin')) {
            return ResponseHelper::error('Akses ditolak. Hanya Super Admin yang dapat membuka kunci sesi.', null, 403);
        }

        $this->sessionService->unlock($sessionId);

        return ResponseHelper::success(null, 'Sesi ujian berhasil dibuka kunci.');
    }

    /**
     * Force submit an assessment session.
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function forceSubmit(string $sessionId): JsonResponse
    {
        $user = auth('api')->user();
        if (!$user || !$user->hasRole('Super Admin')) {
            return ResponseHelper::error('Akses ditolak. Hanya Super Admin yang dapat memaksa pengumpulan.', null, 403);
        }

        $this->sessionService->forceSubmit($sessionId);

        return ResponseHelper::success(null, 'Sesi ujian berhasil dikumpulkan secara paksa.');
    }

    /**
     * Display the specified assessment session.
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function show(string $sessionId): JsonResponse
    {
        $user = auth('api')->user();
        if (!$user) {
            return ResponseHelper::error('Unauthenticated.', null, 401);
        }

        try {
            $isSuperAdmin = $user->hasRole('Super Admin');
            $session = $this->sessionService->getSessionDetails($sessionId, $user->id, $isSuperAdmin);
            if (!$session) {
                return ResponseHelper::error('Sesi tidak ditemukan.', null, 404);
            }
            return ResponseHelper::success($session, 'Data sesi berhasil diambil.');
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
            return ResponseHelper::error($e->getMessage(), null, 403);
        }
    }

    /**
     * Toggle certificate release for an assessment session.
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function toggleCertificate(string $sessionId): JsonResponse
    {
        $user = auth('api')->user();
        if (!$user || !$user->hasRole('Super Admin')) {
            return ResponseHelper::error('Akses ditolak. Hanya Super Admin yang dapat mengelola rilis sertifikat.', null, 403);
        }

        $session = $this->sessionService->toggleCertificateRelease($sessionId);
        if (!$session) {
            return ResponseHelper::error('Sesi tidak ditemukan.', null, 404);
        }

        $status = $session->is_certificate_released ? 'dirilis' : 'dibatalkan rilisnya';
        return ResponseHelper::success($session, "Sertifikat berhasil {$status}.");
    }

    /**
     * Grade an essay answer manually.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $sessionId
     * @return JsonResponse
     */
    public function gradeEssay(\Illuminate\Http\Request $request, string $sessionId): JsonResponse
    {
        $user = auth('api')->user();
        if (!$user || !$user->hasRole('Super Admin')) {
            return ResponseHelper::error('Akses ditolak. Hanya Super Admin yang dapat memberikan penilaian.', null, 403);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'score' => 'required|numeric|min:0',
        ]);

        $answer = $this->sessionService->gradeEssayAnswer(
            $sessionId,
            $request->input('question_id'),
            (float) $request->input('score')
        );

        if (!$answer) {
            return ResponseHelper::error('Sesi tidak ditemukan.', null, 404);
        }

        return ResponseHelper::success($answer, 'Penilaian soal esai berhasil disimpan.');
    }
}

