<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\UpdateAssessmentRequest;
use App\Services\AssessmentService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    protected AssessmentService $assessmentService;

    public function __construct(AssessmentService $assessmentService)
    {
        $this->assessmentService = $assessmentService;
    }

    /**
     * Display a listing of the assessments.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'active']);
        
        $user = auth('api')->user();
        if ($user && !$user->hasRole('Super Admin')) {
            $filters['user_id'] = $user->id;
        }
        
        if ($request->input('per_page') === 'all' || !$request->has('per_page')) {
            $assessments = $this->assessmentService->getAllAssessments($filters);
            return ResponseHelper::success($assessments, 'Daftar ujian berhasil diambil.');
        }

        $perPage = $request->input('per_page', 15);
        $assessments = $this->assessmentService->getAssessmentsPaginated((int)$perPage, $filters);
        return ResponseHelper::success($assessments, 'Daftar ujian berhasil diambil.');
    }


    /**
     * Store a newly created assessment in storage.
     *
     * @param StoreAssessmentRequest $request
     * @return JsonResponse
     */
    public function store(StoreAssessmentRequest $request): JsonResponse
    {
        $data = $request->only([
            'title', 'start_date', 'end_date', 'duration_minutes', 
            'max_attempts', 'randomize_questions', 'randomize_options',
            'passing_grade', 'passing_grade_type', 'certificate_release_mode', 'certificate_template'
        ]);
        $groupIds = $request->input('group_ids');
        $questions = $request->input('questions');

        $assessment = $this->assessmentService->createAssessment($data, $groupIds, $questions);
        return ResponseHelper::success($assessment, 'Ujian berhasil dibuat.', 201);
    }

    /**
     * Display the specified assessment.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $assessment = $this->assessmentService->getAssessmentById($id);

        if (!$assessment) {
            return ResponseHelper::error('Ujian tidak ditemukan.', null, 404);
        }

        $user = auth('api')->user();
        if ($user && !$user->hasRole('Super Admin')) {
            $userGroupIds = $user->groups()->pluck('groups.id')->toArray();
            $targetGroupIds = $assessment->groups()->pluck('groups.id')->toArray();

            $isTarget = false;
            foreach ($userGroupIds as $ugId) {
                if (in_array($ugId, $targetGroupIds)) {
                    $isTarget = true;
                    break;
                }
            }

            if (!$isTarget) {
                return ResponseHelper::error('Anda tidak terdaftar sebagai peserta ujian ini.', null, 403);
            }
        }

        return ResponseHelper::success($assessment, 'Detail ujian berhasil diambil.');
    }

    /**
     * Update the specified assessment in storage.
     *
     * @param UpdateAssessmentRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateAssessmentRequest $request, string $id): JsonResponse
    {
        $data = $request->only([
            'title', 'start_date', 'end_date', 'duration_minutes', 
            'max_attempts', 'randomize_questions', 'randomize_options',
            'passing_grade', 'passing_grade_type', 'certificate_release_mode', 'certificate_template'
        ]);
        $groupIds = $request->input('group_ids');
        $questions = $request->input('questions');

        $updated = $this->assessmentService->updateAssessment($id, $data, $groupIds, $questions);

        if (!$updated) {
            return ResponseHelper::error('Ujian tidak ditemukan atau gagal diperbarui.', null, 404);
        }

        $assessment = $this->assessmentService->getAssessmentById($id);
        return ResponseHelper::success($assessment, 'Ujian berhasil diperbarui.');
    }

    /**
     * Remove the specified assessment from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->assessmentService->deleteAssessment($id);

        if (!$deleted) {
            return ResponseHelper::error('Ujian tidak ditemukan atau gagal dihapus.', null, 404);
        }

        return ResponseHelper::success(null, 'Ujian berhasil dihapus.');
    }

    /**
     * Get sessions for a specific assessment.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function sessions(string $id): JsonResponse
    {
        $assessment = $this->assessmentService->getAssessmentById($id);
        if (!$assessment) {
            return ResponseHelper::error('Ujian tidak ditemukan.', null, 404);
        }

        $sessions = $this->assessmentService->getAssessmentSessions($id);

        return ResponseHelper::success($sessions, 'Daftar sesi ujian peserta berhasil diambil.');
    }

    /**
     * Get public monitoring data for a specific assessment.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function publicMonitor(string $id): JsonResponse
    {
        $data = $this->assessmentService->getPublicMonitorData($id);
        if (!$data) {
            return ResponseHelper::error('Ujian tidak ditemukan.', null, 404);
        }

        return ResponseHelper::success($data, 'Data monitoring publik berhasil diambil.');
    }

    /**
     * Export sessions to CSV.
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|JsonResponse
     */
    public function exportSessions(string $id)
    {
        $user = auth('api')->user();
        if (!$user || !$user->hasRole('Super Admin')) {
            return ResponseHelper::error('Akses ditolak. Hanya Super Admin yang dapat mengekspor sesi.', null, 403);
        }

        $assessment = $this->assessmentService->getAssessmentById($id);
        if (!$assessment) {
            return ResponseHelper::error('Ujian tidak ditemukan.', null, 404);
        }

        $sessions = $this->assessmentService->getAssessmentSessions($id);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rekap_nilai_' . str_replace(' ', '_', strtolower($assessment->title)) . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($sessions, $assessment) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header Row
            fputcsv($file, [
                'No',
                'Nama Peserta',
                'Email',
                'Status Pengerjaan',
                'Jawaban Tersimpan',
                'Jumlah Pelanggaran',
                'Nilai Akhir',
                'Status KKM',
                'Tanggal Mulai',
                'Tanggal Selesai'
            ], ';');

            $no = 1;
            foreach ($sessions as $session) {
                $status = 'Belum Mulai';
                if ($session->status === 'completed') {
                    $status = 'Selesai';
                } elseif ($session->status === 'force_submitted') {
                    $status = 'Selesai (Otomatis)';
                } elseif ($session->status === 'in_progress') {
                    $status = 'Mengerjakan';
                }

                $kkmStatus = '-';
                if ($session->status === 'completed' || $session->status === 'force_submitted') {
                    $kkmStatus = $session->total_score >= $assessment->passing_grade ? 'Lulus KKM' : 'Tidak Lulus';
                }

                fputcsv($file, [
                    $no++,
                    $session->user?->name ?? 'Peserta',
                    $session->user?->email ?? '-',
                    $status,
                    $session->answers_count ?? 0,
                    $session->proctoring_logs_count ?? 0,
                    $session->total_score ?? '0.00',
                    $kkmStatus,
                    $session->start_time ? $session->start_time->format('Y-m-d H:i:s') : '-',
                    $session->end_time ? $session->end_time->format('Y-m-d H:i:s') : '-'
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get item analysis (statistics of questions) for a specific assessment.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function itemAnalysis(string $id): JsonResponse
    {
        $analysis = $this->assessmentService->getItemAnalysisData($id);
        if ($analysis === null) {
            return ResponseHelper::error('Ujian tidak ditemukan.', null, 404);
        }

        return ResponseHelper::success($analysis, 'Analisis kualitas butir soal berhasil dihitung.');
    }

    /**
     * Get global statistics for the admin dashboard.
     *
     * @return JsonResponse
     */
    public function dashboardStats(): JsonResponse
    {
        $stats = $this->assessmentService->getDashboardStats();
        return ResponseHelper::success($stats, 'Statistik dashboard berhasil diambil.');
    }
}

