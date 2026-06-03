<?php

namespace App\Services;

use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Models\Assessment;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AssessmentService
{
    protected AssessmentRepositoryInterface $assessmentRepository;

    public function __construct(AssessmentRepositoryInterface $assessmentRepository)
    {
        $this->assessmentRepository = $assessmentRepository;
    }

    public function getAssessmentsPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->assessmentRepository->paginate($perPage, $filters);
    }

    public function getAllAssessments(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->assessmentRepository->all($filters);
    }


    public function getAssessmentById(string $id): ?Assessment
    {
        return $this->assessmentRepository->find($id);
    }

    public function createAssessment(array $data, ?array $groupIds = null, ?array $questions = null): Assessment
    {
        return DB::transaction(function () use ($data, $groupIds, $questions) {
            $assessment = $this->assessmentRepository->create($data);

            if (!empty($groupIds)) {
                $assessment->groups()->sync($groupIds);
            }

            if (!empty($questions)) {
                $syncData = [];
                foreach ($questions as $index => $questionId) {
                    $syncData[$questionId] = ['order_no' => $index + 1];
                }
                $assessment->questions()->sync($syncData);
            }

            return $assessment->load(['groups', 'questions']);
        });
    }

    public function updateAssessment(string $id, array $data, ?array $groupIds = null, ?array $questions = null): bool
    {
        return DB::transaction(function () use ($id, $data, $groupIds, $questions) {
            $assessment = $this->assessmentRepository->find($id);
            if (!$assessment) {
                return false;
            }

            $this->assessmentRepository->update($id, $data);

            if ($groupIds !== null) {
                $assessment->groups()->sync($groupIds);
            }

            if ($questions !== null) {
                $syncData = [];
                foreach ($questions as $index => $questionId) {
                    $syncData[$questionId] = ['order_no' => $index + 1];
                }
                $assessment->questions()->sync($syncData);
            }

            return true;
        });
    }

    public function deleteAssessment(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $assessment = $this->assessmentRepository->find($id);
            if (!$assessment) {
                return false;
            }

            $assessment->groups()->detach();
            $assessment->questions()->detach();

            return $this->assessmentRepository->delete($id);
        });
    }

    public function getAssessmentSessions(string $assessmentId): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\AssessmentSession::with(['user'])
            ->withCount(['answers', 'proctoringLogs'])
            ->where('assessment_id', $assessmentId)
            ->get();
    }

    public function getPublicMonitorData(string $assessmentId): ?array
    {
        $assessment = \App\Models\Assessment::with(['questions:id,category_id', 'questions.category:id,name'])->find($assessmentId);
        if (!$assessment) {
            return null;
        }

        $sessions = \App\Models\AssessmentSession::with([
            'user:id,name,email',
            'answers:id,session_id,question_id'
        ])
            ->withCount(['answers', 'proctoringLogs'])
            ->where('assessment_id', $assessmentId)
            ->get();

        return [
            'assessment' => [
                'id' => $assessment->id,
                'title' => $assessment->title,
                'passing_grade' => $assessment->passing_grade,
                'duration' => $assessment->duration,
                'start_date' => $assessment->start_date,
                'end_date' => $assessment->end_date,
                'questions_count' => $assessment->questions->count(),
                'questions' => $assessment->questions,
            ],
            'sessions' => $sessions
        ];
    }

    public function getItemAnalysisData(string $assessmentId): ?array
    {
        $assessment = \App\Models\Assessment::find($assessmentId);
        if (!$assessment) {
            return null;
        }

        // Get completed sessions
        $sessions = \App\Models\AssessmentSession::where('assessment_id', $assessmentId)
            ->whereIn('status', ['completed', 'force_submitted'])
            ->get();

        $sessionIds = $sessions->pluck('id');
        
        // Get answers with options relation
        $answers = \App\Models\SessionAnswer::with('selectedOption')
            ->whereIn('session_id', $sessionIds)
            ->get()
            ->groupBy('question_id');

        $questions = $assessment->questions()->with('category')->get();

        $analysis = [];
        foreach ($questions as $question) {
            $questionAnswers = $answers->get($question->id) ?? collect();
            $totalAttempts = $questionAnswers->count();
            
            $correctCount = 0;
            foreach ($questionAnswers as $answer) {
                if ($answer->selectedOption && $answer->selectedOption->is_correct) {
                    $correctCount++;
                }
            }

            $successRate = $totalAttempts > 0 ? round(($correctCount / $totalAttempts) * 100, 2) : null;

            $analysis[] = [
                'id' => $question->id,
                'content_text' => $question->content_text,
                'category_name' => $question->category->name ?? 'Tanpa Kategori',
                'type' => $question->type,
                'difficulty' => $question->difficulty,
                'total_attempts' => $totalAttempts,
                'correct_count' => $correctCount,
                'success_rate' => $successRate,
            ];
        }

        return $analysis;
    }

    public function getDashboardStats(): array
    {
        // Auto-run migration to add certificate_template if it doesn't exist
        if (!\Illuminate\Support\Facades\Schema::hasColumn('assessments', 'certificate_template')) {
            \Illuminate\Support\Facades\Schema::table('assessments', function ($table) {
                $table->string('certificate_template')->default('classic')->nullable()->after('certificate_release_mode');
            });
        }

        $categoriesCount = \App\Models\Category::count();
        $questionsCount = \App\Models\Question::count();
        $assessmentsCount = \App\Models\Assessment::count();

        $now = now();
        $activeAssessmentsCount = \App\Models\Assessment::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();

        $totalSessionsCount = \App\Models\AssessmentSession::count();
        
        $finishedSessions = \App\Models\AssessmentSession::with('assessment')
            ->whereIn('status', ['finished', 'force_submitted'])
            ->get();
        
        $finishedSessionsCount = $finishedSessions->count();
        
        $passedCount = 0;
        foreach ($finishedSessions as $session) {
            $isPassed = false;
            if ($session->assessment) {
                if (($session->assessment->passing_grade_type ?? 'overall') === 'overall') {
                    if ($session->total_score >= ($session->assessment->passing_grade ?? 0)) {
                        $isPassed = true;
                    }
                } else {
                    if ($session->total_score >= ($session->assessment->passing_grade ?? 0)) {
                        $isPassed = true;
                    }
                }
            }
            if ($isPassed) {
                $passedCount++;
            }
        }

        $passingRate = $finishedSessionsCount > 0 ? round(($passedCount / $finishedSessionsCount) * 100, 1) : 0;

        $violationsCount = \App\Models\AssessmentProctoringLog::count();

        $categories = \App\Models\Category::withCount('questions')->get();
        $questionDistribution = [];
        foreach ($categories as $cat) {
            $questionDistribution[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'count' => $cat->questions_count,
            ];
        }

        usort($questionDistribution, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        $questionDistribution = array_slice($questionDistribution, 0, 5);

        $recentSessionsRaw = \App\Models\AssessmentSession::with(['user', 'assessment'])
            ->latest()
            ->limit(5)
            ->get();

        $recentSessions = [];
        foreach ($recentSessionsRaw as $sess) {
            $recentSessions[] = [
                'id' => $sess->id,
                'candidate_name' => $sess->user->name ?? 'Peserta',
                'assessment_title' => $sess->assessment->title ?? 'Ujian',
                'status' => $sess->status,
                'score' => $sess->total_score,
                'created_at' => $sess->created_at->toIso8601String(),
            ];
        }

        // Calculate Group Performance
        $groupPerformance = [];
        $groups = \App\Models\Group::with(['users.assessmentSessions' => function($q) {
            $q->whereIn('status', ['finished', 'force_submitted'])->with('assessment');
        }])->get();

        foreach ($groups as $group) {
            $scores = [];
            $passedSessions = 0;
            $totalSessions = 0;

            foreach ($group->users as $user) {
                foreach ($user->assessmentSessions as $session) {
                    $totalSessions++;
                    $score = floatval($session->total_score);
                    $scores[] = $score;

                    $isPassed = false;
                    if ($session->assessment) {
                        $passingGrade = floatval($session->assessment->passing_grade ?? 0);
                        if ($score >= $passingGrade) {
                            $isPassed = true;
                        }
                    }
                    if ($isPassed) {
                        $passedSessions++;
                    }
                }
            }

            $avgScore = count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0;
            $groupPassingRate = $totalSessions > 0 ? round(($passedSessions / $totalSessions) * 100, 1) : 0;

            $groupPerformance[] = [
                'id' => $group->id,
                'name' => $group->name,
                'total_sessions' => $totalSessions,
                'average_score' => $avgScore,
                'passing_rate' => $groupPassingRate,
            ];
        }

        return [
            'categories_count' => $categoriesCount,
            'questions_count' => $questionsCount,
            'assessments_count' => $assessmentsCount,
            'active_assessments_count' => $activeAssessmentsCount,
            'total_sessions_count' => $totalSessionsCount,
            'finished_sessions_count' => $finishedSessionsCount,
            'passed_sessions_count' => $passedCount,
            'passing_rate' => $passingRate,
            'violations_count' => $violationsCount,
            'question_distribution' => $questionDistribution,
            'recent_sessions' => $recentSessions,
            'group_performance' => $groupPerformance,
        ];
    }
}

