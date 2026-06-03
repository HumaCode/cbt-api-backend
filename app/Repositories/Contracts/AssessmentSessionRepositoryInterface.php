<?php

namespace App\Repositories\Contracts;

use App\Models\AssessmentSession;
use App\Models\SessionAnswer;

interface AssessmentSessionRepositoryInterface
{
    public function find(string $id): ?AssessmentSession;
    public function create(array $data): AssessmentSession;
    public function update(string $id, array $data): bool;
    public function getUserActiveSession(string $userId, string $assessmentId): ?AssessmentSession;
    public function getAttemptsCount(string $userId, string $assessmentId): int;
    
    // Answers handling
    public function saveAnswer(string $sessionId, array $answerData): SessionAnswer;
    public function calculateTotalScore(string $sessionId): float;
}
