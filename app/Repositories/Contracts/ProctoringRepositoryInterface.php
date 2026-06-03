<?php

namespace App\Repositories\Contracts;

use App\Models\AssessmentProctoringLog;
use Illuminate\Database\Eloquent\Collection;

interface ProctoringRepositoryInterface
{
    public function create(array $data): AssessmentProctoringLog;
    public function getLogsBySession(string $sessionId): Collection;
}
