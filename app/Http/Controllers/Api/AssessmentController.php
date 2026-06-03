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
            'passing_grade', 'passing_grade_type'
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
            'passing_grade', 'passing_grade_type'
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

        $sessions = \App\Models\AssessmentSession::with(['user'])
            ->withCount(['answers', 'proctoringLogs'])
            ->where('assessment_id', $id)
            ->get();

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
        $assessment = \App\Models\Assessment::with(['questions:id,category_id', 'questions.category:id,name'])->find($id);
        if (!$assessment) {
            return ResponseHelper::error('Ujian tidak ditemukan.', null, 404);
        }

        // Only return basic public details of the assessment
        $publicAssessment = [
            'id' => $assessment->id,
            'title' => $assessment->title,
            'passing_grade' => $assessment->passing_grade,
            'duration' => $assessment->duration,
            'start_date' => $assessment->start_date,
            'end_date' => $assessment->end_date,
            'questions_count' => $assessment->questions->count(),
            'questions' => $assessment->questions,
        ];

        $sessions = \App\Models\AssessmentSession::with([
            'user:id,name,email',
            'answers:id,session_id,question_id'
        ])
            ->withCount(['answers', 'proctoringLogs'])
            ->where('assessment_id', $id)
            ->get();

        return ResponseHelper::success([
            'assessment' => $publicAssessment,
            'sessions' => $sessions
        ], 'Data monitoring publik berhasil diambil.');
    }
}
