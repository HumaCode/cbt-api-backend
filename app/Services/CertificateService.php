<?php

namespace App\Services;

use App\Repositories\Contracts\CertificateRepositoryInterface;
use App\Repositories\Contracts\AssessmentSessionRepositoryInterface;
use App\Models\Certificate;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class CertificateService
{
    protected CertificateRepositoryInterface $certificateRepository;
    protected AssessmentSessionRepositoryInterface $sessionRepository;

    public function __construct(
        CertificateRepositoryInterface $certificateRepository,
        AssessmentSessionRepositoryInterface $sessionRepository
    ) {
        $this->certificateRepository = $certificateRepository;
        $this->sessionRepository = $sessionRepository;
    }

    public function getOrIssueCertificate(string $sessionId, string $userId): Certificate
    {
        return DB::transaction(function () use ($sessionId, $userId) {
            $session = $this->sessionRepository->find($sessionId);
            if (!$session) {
                throw ValidationException::withMessages(['session' => 'Sesi ujian tidak ditemukan.']);
            }

            // Check authorization
            $user = auth('api')->user();
            if ($session->user_id !== $userId && !$user->hasAnyRole(['Super Admin', 'Assessor'])) {
                throw ValidationException::withMessages(['session' => 'Anda tidak memiliki hak akses ke sesi ini.']);
            }

            // Check if status is completed
            if (!in_array($session->status, ['completed', 'force_submitted'])) {
                throw ValidationException::withMessages(['session' => 'Sesi ujian belum diselesaikan.']);
            }

            // Check if passed passing grade
            $passingGrade = $session->assessment->passing_grade ?? 0.00;
            if ($session->total_score < $passingGrade) {
                throw ValidationException::withMessages(['session' => 'Nilai ujian Anda tidak mencapai KKM / standar kelulusan (' . $passingGrade . ').']);
            }

            // Check if certificate already exists
            $existing = $this->certificateRepository->findBySession($sessionId);
            if ($existing) {
                return $existing->load('assessmentSession.user', 'assessmentSession.assessment');
            }

            // Generate unique certificate number
            $certNumber = 'CERT/' . now()->format('Ymd') . '/' . strtoupper(bin2hex(random_bytes(4)));

            return $this->certificateRepository->create([
                'assessment_session_id' => $sessionId,
                'user_id' => $session->user_id,
                'certificate_number' => $certNumber,
                'issue_date' => now(),
                'file_path' => 'certificates/' . $certNumber . '.pdf',
            ])->load('assessmentSession.user', 'assessmentSession.assessment');
        });
    }
}
