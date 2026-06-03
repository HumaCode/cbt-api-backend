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
            $gradingType = $session->assessment->passing_grade_type ?? 'overall';

            if ($gradingType === 'per_category') {
                // Load answers with their questions and categories
                $answers = $session->answers()->with('question.category')->get();

                // Build a map: category_id => { answered: N, total: N, passing_grade: X }
                $categoryMap = [];
                foreach ($session->assessment->questions()->with('category')->get() as $question) {
                    $catId = $question->category_id;
                    if (!isset($categoryMap[$catId])) {
                        $categoryMap[$catId] = [
                            'name' => $question->category->name ?? 'Tanpa Kategori',
                            'passing_grade' => $question->category->passing_grade ?? null,
                            'answered_correct' => 0,
                            'total' => 0,
                        ];
                    }
                    $categoryMap[$catId]['total']++;
                }

                // Count correct answers per category
                foreach ($answers as $answer) {
                    $catId = $answer->question->category_id ?? null;
                    if ($catId && isset($categoryMap[$catId])) {
                        // Check if selected option is correct
                        if ($answer->selectedOption && $answer->selectedOption->is_correct) {
                            $categoryMap[$catId]['answered_correct']++;
                        }
                    }
                }

                // Check each category with a defined KKM
                foreach ($categoryMap as $catId => $cat) {
                    $kkm = $cat['passing_grade'];
                    if ($kkm === null) continue; // Skip categories without KKM

                    $total = $cat['total'];
                    if ($total === 0) continue;

                    $score = ($cat['answered_correct'] / $total) * 100;
                    if ($score < $kkm) {
                        throw ValidationException::withMessages([
                            'session' => 'Nilai kategori "' . $cat['name'] . '" (' . round($score, 1) . ') tidak mencapai KKM kategori (' . $kkm . ').'
                        ]);
                    }
                }
            } else {
                // Overall passing grade check
                $passingGrade = $session->assessment->passing_grade ?? 0.00;
                if ($session->total_score < $passingGrade) {
                    throw ValidationException::withMessages(['session' => 'Nilai ujian Anda tidak mencapai KKM / standar kelulusan (' . $passingGrade . ').']);
                }
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
