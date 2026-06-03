<?php

namespace App\Repositories\Eloquent;

use App\Models\AssessmentProctoringLog;
use App\Repositories\Contracts\ProctoringRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProctoringRepository implements ProctoringRepositoryInterface
{
    public function create(array $data): AssessmentProctoringLog
    {
        return AssessmentProctoringLog::create($data);
    }

    public function getLogsBySession(string $sessionId): Collection
    {
        return AssessmentProctoringLog::where('session_id', $sessionId)
            ->oldest()
            ->get();
    }
}
