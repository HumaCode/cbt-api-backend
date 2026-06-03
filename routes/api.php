<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AssessmentSessionController;
use App\Http\Controllers\Api\ProctoringController;
use App\Http\Controllers\Api\CertificateController;

Route::prefix('v1')->group(function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);

        Route::group(['middleware' => 'auth:api'], function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    // Public Monitoring Route
    Route::get('public/assessments/{assessment}/monitor', [AssessmentController::class, 'publicMonitor']);

    // Protected Routes
    Route::group(['middleware' => 'auth:api'], function () {
        Route::apiResource('categories', CategoryController::class);
        Route::post('questions/import', [QuestionController::class, 'import']);
        Route::apiResource('questions', QuestionController::class);
        Route::apiResource('assessments', AssessmentController::class);
        Route::get('assessments/{assessment}/sessions', [AssessmentController::class, 'sessions']);

        // Assessment Session execution routes
        Route::post('assessments/{assessment}/start', [AssessmentSessionController::class, 'start']);
        Route::post('sessions/{session}/start-timer', [AssessmentSessionController::class, 'startTimer']);
        Route::post('sessions/{session}/answers', [AssessmentSessionController::class, 'submitAnswer']);
        Route::post('sessions/{session}/finish', [AssessmentSessionController::class, 'finish']);

        // Proctoring routes
        Route::post('sessions/{session}/proctor-logs', [ProctoringController::class, 'store']);
        Route::get('sessions/{session}/proctor-logs', [ProctoringController::class, 'index']);

        // Certificate routes
        Route::get('sessions/{session}/certificate', [CertificateController::class, 'show']);
    });
});
