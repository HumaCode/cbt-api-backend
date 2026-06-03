<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Authenticate a user and return a token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->login(
                $request->input('login'),
                $request->input('password')
            );

            return ResponseHelper::success($response, 'Login berhasil.');
        } catch (ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), $e->errors(), 422);
        }
    }

    /**
     * Register a new candidate (Peserta).
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
        ];

        return ResponseHelper::success($data, 'Registrasi berhasil. Silakan login.', 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return ResponseHelper::success(null, 'Logout berhasil.');
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $response = $this->authService->refresh();

        return ResponseHelper::success($response, 'Token berhasil diperbarui.');
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user) {
            return ResponseHelper::error('Unauthorized', null, 401);
        }

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'telp' => $user->telp,
            'gender' => $user->gender,
            'roles' => $user->getRoleNames(),
        ];

        return ResponseHelper::success($data);
    }
}
