<?php

namespace App\Repositories\Eloquent;

use App\Models\Certificate;
use App\Repositories\Contracts\CertificateRepositoryInterface;

class CertificateRepository implements CertificateRepositoryInterface
{
    public function findBySession(string $sessionId): ?Certificate
    {
        return Certificate::where('assessment_session_id', $sessionId)->first();
    }

    public function create(array $data): Certificate
    {
        return Certificate::create($data);
    }
}
