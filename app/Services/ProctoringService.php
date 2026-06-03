<?php

namespace App\Services;

use App\Repositories\Contracts\ProctoringRepositoryInterface;
use App\Repositories\Contracts\AssessmentSessionRepositoryInterface;
use App\Models\AssessmentProctoringLog;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Collection;

class ProctoringService
{
    protected ProctoringRepositoryInterface $proctoringRepository;
    protected AssessmentSessionRepositoryInterface $sessionRepository;

    public function __construct(
        ProctoringRepositoryInterface $proctoringRepository,
        AssessmentSessionRepositoryInterface $sessionRepository
    ) {
        $this->proctoringRepository = $proctoringRepository;
        $this->sessionRepository = $sessionRepository;
    }

    public function createLog(string $sessionId, string $userId, array $logData): AssessmentProctoringLog
    {
        $session = $this->sessionRepository->find($sessionId);
        if (!$session) {
            throw ValidationException::withMessages(['session' => 'Sesi ujian tidak ditemukan.']);
        }

        // Restrict to the session owner
        if ($session->user_id !== $userId) {
            throw ValidationException::withMessages(['session' => 'Anda tidak memiliki hak akses ke sesi ujian ini.']);
        }

        if ($session->status !== 'in_progress') {
            throw ValidationException::withMessages(['session' => 'Sesi ujian sudah tidak aktif.']);
        }

        return $this->proctoringRepository->create([
            'session_id' => $sessionId,
            'event_type' => $logData['event_type'],
            'event_details' => $logData['event_details'] ?? null,
        ]);
    }

    public function getLogs(string $sessionId, string $userId): Collection
    {
        $session = $this->sessionRepository->find($sessionId);
        if (!$session) {
            throw ValidationException::withMessages(['session' => 'Sesi ujian tidak ditemukan.']);
        }

        // Restrict to session owner or Assessors/Super Admins
        $user = auth('api')->user();
        if ($session->user_id !== $userId && !$user->hasAnyRole(['Super Admin', 'Assessor'])) {
            throw ValidationException::withMessages(['session' => 'Anda tidak memiliki hak akses ke sesi ujian ini.']);
        }

        return $this->proctoringRepository->getLogsBySession($sessionId);
    }
}
