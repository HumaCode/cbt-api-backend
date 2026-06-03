<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed');
});

test('user can login with username', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'login' => 'admin',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'user' => [
                    'id',
                    'name',
                    'username',
                    'email',
                    'roles',
                ],
            ],
        ]);
});

test('user can login with email', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'login' => 'admin@cbt.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ]);
});

test('inactive user cannot login', function () {
    $user = User::where('username', 'peserta')->first();
    $user->update(['is_active' => '0']);

    $response = $this->postJson('/api/v1/auth/login', [
        'login' => 'peserta',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'status' => 'error',
        ]);
});

test('user can get their profile (me) using token', function () {
    $loginResponse = $this->postJson('/api/v1/auth/login', [
        'login' => 'peserta',
        'password' => 'password123',
    ]);

    $token = $loginResponse->json('data.access_token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->getJson('/api/v1/auth/me');

    $response->assertStatus(200)
        ->assertJsonPath('data.username', 'peserta');
});
