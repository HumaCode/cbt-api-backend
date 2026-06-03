<?php

namespace App\Repositories\Contracts;

use App\Models\Certificate;

interface CertificateRepositoryInterface
{
    public function findBySession(string $sessionId): ?Certificate;
    public function create(array $data): Certificate;
}
