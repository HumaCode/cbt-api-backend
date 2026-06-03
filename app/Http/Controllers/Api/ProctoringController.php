<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProctoringLogRequest;
use App\Services\ProctoringService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProctoringController extends Controller
{
    protected ProctoringService $proctoringService;

    public function __construct(ProctoringService $proctoringService)
    {
        $this->proctoringService = $proctoringService;
    }

    /**
     * Store a new proctoring log for the session.
     *
     * @param StoreProctoringLogRequest $request
     * @param string $sessionId
     * @return JsonResponse
     */
    public function store(StoreProctoringLogRequest $request, string $sessionId): JsonResponse
    {
        try {
            $userId = auth('api')->id();
            $log = $this->proctoringService->createLog($sessionId, $userId, $request->validated());
            return ResponseHelper::success($log, 'Log pengawasan berhasil disimpan.', 201);
        } catch (ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), $e->errors(), 422);
        }
    }

    /**
     * Retrieve proctoring logs for a session.
     *
     * @param string $sessionId
     * @return JsonResponse
     */
    public function index(string $sessionId): JsonResponse
    {
        try {
            $userId = auth('api')->id();
            $logs = $this->proctoringService->getLogs($sessionId, $userId);
            return ResponseHelper::success($logs, 'Daftar log pengawasan berhasil diambil.');
        } catch (ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), $e->errors(), 422);
        }
    }
}
