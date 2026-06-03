<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CertificateService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Get or Issue certificate for the given assessment session.
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function show(string $sessionId): JsonResponse
    {
        try {
            $userId = auth('api')->id();
            $certificate = $this->certificateService->getOrIssueCertificate($sessionId, $userId);
            return ResponseHelper::success($certificate, 'Sertifikat kelulusan berhasil diambil.');
        } catch (ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), $e->errors(), 422);
        }
    }
}
