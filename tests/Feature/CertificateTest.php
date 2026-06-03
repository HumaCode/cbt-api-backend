<?php

use App\Models\User;
use App\Models\Assessment;
use App\Models\AssessmentSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed');
    
    // Get candidate (Peserta) and authenticate
    $this->user = User::where('username', 'peserta')->first();
    $this->token = auth('api')->login($this->user);

    $this->assessment = Assessment::create([
        'title' => 'Ujian Sertifikasi',
        'start_date' => now()->subHours(1),
        'end_date' => now()->addHours(2),
        'duration_minutes' => 60,
        'passing_grade' => 75.00,
    ]);
});

test('candidate can get certificate if they pass the assessment', function () {
    // Create completed session with passing score (e.g. 80.00)
    $session = AssessmentSession::create([
        'assessment_id' => $this->assessment->id,
        'user_id' => $this->user->id,
        'start_time' => now()->subMinutes(30),
        'end_time' => now(),
        'status' => 'completed',
        'total_score' => 80.00,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/v1/sessions/{$session->id}/certificate");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'certificate_number',
                'issue_date',
                'file_path',
            ]
        ]);

    $this->assertDatabaseHas('certificates', [
        'assessment_session_id' => $session->id,
        'user_id' => $this->user->id,
    ]);
});

test('candidate cannot get certificate if score is below passing grade', function () {
    // Create completed session with failing score (e.g. 60.00)
    $session = AssessmentSession::create([
        'assessment_id' => $this->assessment->id,
        'user_id' => $this->user->id,
        'start_time' => now()->subMinutes(30),
        'end_time' => now(),
        'status' => 'completed',
        'total_score' => 60.00,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/v1/sessions/{$session->id}/certificate");

    $response->assertStatus(422)
        ->assertJsonStructure([
            'status',
            'message',
            'errors' => ['session']
        ]);
});

test('candidate cannot get certificate if session is not completed', function () {
    $session = AssessmentSession::create([
        'assessment_id' => $this->assessment->id,
        'user_id' => $this->user->id,
        'start_time' => now(),
        'end_time' => now()->addMinutes(60),
        'status' => 'in_progress',
        'total_score' => 0.00,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/v1/sessions/{$session->id}/certificate");

    $response->assertStatus(422);
});
