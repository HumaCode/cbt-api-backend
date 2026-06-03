<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticate user by username or email.
     *
     * @param string $login
     * @param string $password
     * @return array
     * @throws ValidationException
     */
    public function login(string $login, string $password): array
    {
        // Check if $login is email or username
        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? $this->userRepository->findByEmail($login)
            : $this->userRepository->findByUsername($login);

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        if ($user->is_active !== '1') {
            throw ValidationException::withMessages([
                'login' => ['Akun Anda saat ini dinonaktifkan.'],
            ]);
        }

        // Generate JWT Token
        $token = auth('api')->login($user);

        // Update last activity
        $this->userRepository->update($user->id, [
            'last_activity' => now(),
        ]);

        return $this->respondWithToken($token, $user);
    }

    /**
     * Register a new user (Peserta).
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = '1';
        
        $user = $this->userRepository->create($data);

        // Assign default role (Peserta)
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $user->assignRole('Peserta');
        }

        return $user;
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return void
     */
    public function logout(): void
    {
        auth('api')->logout();
    }

    /**
     * Refresh a token.
     *
     * @return array
     */
    public function refresh(): array
    {
        $token = auth('api')->refresh();
        $user = auth('api')->user();

        return $this->respondWithToken($token, $user);
    }

    /**
     * Format the token response.
     *
     * @param string $token
     * @param User $user
     * @return array
     */
    protected function respondWithToken(string $token, User $user): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'telp' => $user->telp,
                'gender' => $user->gender,
                'roles' => $user->getRoleNames(),
            ]
        ];
    }
}
