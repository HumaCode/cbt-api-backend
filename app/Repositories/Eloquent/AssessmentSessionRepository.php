<?php

namespace App\Repositories\Eloquent;

use App\Models\AssessmentSession;
use App\Models\SessionAnswer;
use App\Models\QuestionOption;
use App\Repositories\Contracts\AssessmentSessionRepositoryInterface;

class AssessmentSessionRepository implements AssessmentSessionRepositoryInterface
{
    public function find(string $id): ?AssessmentSession
    {
        // Eager load assessment beserta questions dan options-nya (dengan media), serta answers yang sudah disimpan
        return AssessmentSession::with([
            'assessment.questions.media',
            'assessment.questions.options.media',
            'answers'
        ])->find($id);
    }
    public function create(array $data): AssessmentSession
    {
        $session = AssessmentSession::create($data);
        // Panggil find() untuk memuat relasi yang lengkap
        return $this->find($session->id);
    }
    public function update(string $id, array $data): bool
    {
        $session = AssessmentSession::find($id);
        if (!$session) {
            return false;
        }
        return $session->update($data);
    }
    public function getUserActiveSession(string $userId, string $assessmentId): ?AssessmentSession
    {
        $session = AssessmentSession::where('user_id', $userId)
            ->where('assessment_id', $assessmentId)
            ->where('status', 'in_progress')
            ->first();
        // Jika sesi aktif ditemukan, panggil find() agar memuat relasi lengkap
        return $session ? $this->find($session->id) : null;
    }

    public function getAttemptsCount(string $userId, string $assessmentId): int
    {
        return AssessmentSession::where('user_id', $userId)
            ->where('assessment_id', $assessmentId)
            ->count();
    }

    public function saveAnswer(string $sessionId, array $answerData): SessionAnswer
    {
        // Calculate is_correct and score if PG
        $isCorrect = false;
        $scoreEarned = 0.00;

        if (!empty($answerData['selected_option_id'])) {
            $option = QuestionOption::find($answerData['selected_option_id']);
            if ($option) {
                $isCorrect = $option->is_correct;
                $scoreEarned = $option->weight;
            }
        }

        return SessionAnswer::updateOrCreate(
            [
                'session_id' => $sessionId,
                'question_id' => $answerData['question_id']
            ],
            [
                'selected_option_id' => $answerData['selected_option_id'] ?? null,
                'answer_text' => $answerData['answer_text'] ?? null,
                'is_correct' => $isCorrect,
                'score_earned' => $scoreEarned,
            ]
        );
    }

    public function calculateTotalScore(string $sessionId): float
    {
        return (float) SessionAnswer::where('session_id', $sessionId)
            ->sum('score_earned');
    }
}
