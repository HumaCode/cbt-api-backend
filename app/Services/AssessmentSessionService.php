<?php

namespace App\Services;

use App\Repositories\Contracts\AssessmentSessionRepositoryInterface;
use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Models\AssessmentSession;
use App\Models\SessionAnswer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AssessmentSessionService
{
    protected AssessmentSessionRepositoryInterface $sessionRepository;
    protected AssessmentRepositoryInterface $assessmentRepository;

    public function __construct(
        AssessmentSessionRepositoryInterface $sessionRepository,
        AssessmentRepositoryInterface $assessmentRepository
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->assessmentRepository = $assessmentRepository;
    }

    public function startSession(string $userId, string $assessmentId): AssessmentSession
    {
        return DB::transaction(function () use ($userId, $assessmentId) {
            $assessment = $this->assessmentRepository->find($assessmentId);
            if (!$assessment) {
                throw ValidationException::withMessages(['assessment' => 'Ujian tidak ditemukan.']);
            }

            // Check if active date range
            $now = now();
            if ($now->lt($assessment->start_date) || $now->gt($assessment->end_date)) {
                throw ValidationException::withMessages(['assessment' => 'Ujian tidak sedang berlangsung atau sudah ditutup.']);
            }

            // Check if user is in target groups
            $user = auth('api')->user();
            $userGroupIds = $user->groups()->pluck('groups.id')->toArray();
            $targetGroupIds = $assessment->groups()->pluck('groups.id')->toArray();

            $isTarget = false;
            foreach ($userGroupIds as $ugId) {
                if (in_array($ugId, $targetGroupIds)) {
                    $isTarget = true;
                    break;
                }
            }

            // Also check if Super Admin
            if (!$isTarget && !$user->hasRole('Super Admin')) {
                throw ValidationException::withMessages(['assessment' => 'Anda tidak terdaftar sebagai peserta ujian ini.']);
            }

            // Check for active session (resume)
            $activeSession = $this->sessionRepository->getUserActiveSession($userId, $assessmentId);
            if ($activeSession) {
                // Check if time expired
                if (now()->gt($activeSession->end_time)) {
                    $this->forceSubmitSession($activeSession);
                } else {
                    return $activeSession;
                }
            }

            // Check max attempts
            $attempts = $this->sessionRepository->getAttemptsCount($userId, $assessmentId);
            if ($attempts >= $assessment->max_attempts) {
                throw ValidationException::withMessages(['assessment' => 'Anda telah melebihi batas percobaan pengerjaan ujian ini.']);
            }

            // Create new session
            $duration = $assessment->duration_minutes;
            return $this->sessionRepository->create([
                'assessment_id' => $assessmentId,
                'user_id' => $userId,
                'start_time' => now(),
                'end_time' => now()->addMinutes($duration),
                'status' => 'in_progress',
                'total_score' => 0.00,
            ]);
        });
    }

    public function submitAnswer(string $sessionId, string $userId, array $answerData): SessionAnswer
    {
        return DB::transaction(function () use ($sessionId, $userId, $answerData) {
            $session = $this->sessionRepository->find($sessionId);
            if (!$session || $session->user_id !== $userId) {
                throw ValidationException::withMessages(['session' => 'Sesi ujian tidak valid.']);
            }

            if ($session->status !== 'in_progress') {
                throw ValidationException::withMessages(['session' => 'Sesi ujian sudah selesai atau tidak aktif.']);
            }

            // Check if time expired
            if (now()->gt($session->end_time)) {
                $this->forceSubmitSession($session);
                throw ValidationException::withMessages(['session' => 'Waktu ujian telah habis. Jawaban Anda disimpan otomatis.']);
            }

            // Save answer
            return $this->sessionRepository->saveAnswer($sessionId, $answerData);
        });
    }

    public function finishSession(string $sessionId, string $userId): AssessmentSession
    {
        return DB::transaction(function () use ($sessionId, $userId) {
            $session = $this->sessionRepository->find($sessionId);
            if (!$session || $session->user_id !== $userId) {
                throw ValidationException::withMessages(['session' => 'Sesi ujian tidak valid.']);
            }

            if ($session->status !== 'in_progress') {
                return $session;
            }

            // Calculate score
            $totalScore = $this->sessionRepository->calculateTotalScore($sessionId);

            $this->sessionRepository->update($sessionId, [
                'status' => 'completed',
                'end_time' => now(),
                'total_score' => $totalScore,
            ]);

            return $this->sessionRepository->find($sessionId);
        });
    }

    public function startTimer(string $sessionId, string $userId): AssessmentSession
    {
        return DB::transaction(function () use ($sessionId, $userId) {
            $session = $this->sessionRepository->find($sessionId);
            if (!$session || $session->user_id !== $userId) {
                throw ValidationException::withMessages(['session' => 'Sesi ujian tidak valid.']);
            }

            if ($session->status !== 'in_progress') {
                throw ValidationException::withMessages(['session' => 'Sesi ujian sudah selesai atau tidak aktif.']);
            }

            // Only start the timer if it hasn't been started yet
            if (!$session->is_timer_started) {
                $assessment = $session->assessment;
                $duration = $assessment->duration_minutes;

                $this->sessionRepository->update($sessionId, [
                    'start_time' => now(),
                    'end_time' => now()->addMinutes($duration),
                    'is_timer_started' => true,
                ]);

                // Fetch the updated session
                $session = $this->sessionRepository->find($sessionId);
            }

            return $session;
        });
    }

    protected function forceSubmitSession(AssessmentSession $session): void
    {
        $totalScore = $this->sessionRepository->calculateTotalScore($session->id);
        $this->sessionRepository->update($session->id, [
            'status' => 'force_submitted',
            'total_score' => $totalScore,
        ]);
    }

    public function forceSubmit(string $sessionId): void
    {
        \DB::transaction(function () use ($sessionId) {
            $session = $this->sessionRepository->find($sessionId);
            if ($session) {
                $this->forceSubmitSession($session);
            }
        });
    }

    public function unlock(string $sessionId): void
    {
        \DB::transaction(function () use ($sessionId) {
            $session = $this->sessionRepository->find($sessionId);
            if ($session) {
                $this->sessionRepository->update($sessionId, [
                    'status' => 'in_progress',
                    'is_timer_started' => false,
                ]);
            }
        });
    }

    public function deleteSession(string $sessionId): bool
    {
        return DB::transaction(function () use ($sessionId) {
            $session = AssessmentSession::find($sessionId);
            if (!$session) {
                return false;
            }
            $session->delete();
            return true;
        });
    }

    public function deleteSessionsBulk(array $sessionIds): void
    {
        DB::transaction(function () use ($sessionIds) {
            AssessmentSession::whereIn('id', $sessionIds)->delete();
        });
    }

    public function getSessionDetails(string $sessionId, string $userId, bool $isSuperAdmin): ?AssessmentSession
    {
        $session = AssessmentSession::with([
            'user:id,name,email',
            'assessment.questions.media',
            'assessment.questions.category',
            'assessment.questions.options.media',
            'answers',
        ])->find($sessionId);

        if (!$session) {
            return null;
        }

        if ($session->user_id !== $userId && !$isSuperAdmin) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Akses ditolak.');
        }

        return $session;
    }

    public function toggleCertificateRelease(string $sessionId): ?AssessmentSession
    {
        return DB::transaction(function () use ($sessionId) {
            $session = AssessmentSession::find($sessionId);
            if (!$session) {
                return null;
            }
            $session->is_certificate_released = !$session->is_certificate_released;
            $session->save();
            return $session;
        });
    }

    public function gradeEssayAnswer(string $sessionId, string $questionId, float $score): ?SessionAnswer
    {
        return DB::transaction(function () use ($sessionId, $questionId, $score) {
            $session = AssessmentSession::find($sessionId);
            if (!$session) {
                return null;
            }

            $answer = SessionAnswer::firstOrCreate([
                'session_id' => $sessionId,
                'question_id' => $questionId,
            ]);

            $answer->update([
                'score_earned' => $score,
                'is_correct' => $score > 0,
            ]);

            $totalScore = SessionAnswer::where('session_id', $sessionId)->sum('score_earned');
            $session->update([
                'total_score' => $totalScore,
            ]);

            return $answer;
        });
    }
}

