<?php

use App\Models\User;
use App\Models\Group;
use App\Models\Category;
use App\Models\Question;
use App\Models\Assessment;
use App\Models\AssessmentSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed');
    
    // Get candidate (Peserta) and authenticate
    $this->user = User::where('username', 'peserta')->first();
    $this->token = auth('api')->login($this->user);

    // Create a group and assessment
    $this->group = Group::create(['name' => 'Kelas X-A']);
    $this->user->groups()->attach($this->group->id);

    $this->assessment = Assessment::create([
        'title' => 'Ujian Harian Matematika',
        'start_date' => now()->subHours(1)->toDateTimeString(),
        'end_date' => now()->addHours(2)->toDateTimeString(),
        'duration_minutes' => 60,
    ]);

    // Create active session
    $this->session = AssessmentSession::create([
        'assessment_id' => $this->assessment->id,
        'user_id' => $this->user->id,
        'start_time' => now(),
        'end_time' => now()->addMinutes(60),
        'status' => 'in_progress',
    ]);
});

test('candidate can submit a proctoring log during their session', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson("/api/v1/sessions/{$this->session->id}/proctor-logs", [
        'event_type' => 'tab_switch',
        'event_details' => 'User switched browser tab to google.com',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.event_type', 'tab_switch');

    $this->assertDatabaseHas('assessment_proctoring_logs', [
        'session_id' => $this->session->id,
        'event_type' => 'tab_switch',
        'event_details' => 'User switched browser tab to google.com',
    ]);
});

test('candidate can view their own session proctoring logs', function () {
    $this->session->proctoringLogs()->create([
        'event_type' => 'tab_switch',
        'event_details' => 'Warning 1',
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson("/api/v1/sessions/{$this->session->id}/proctor-logs");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('candidate cannot submit log to other user session', function () {
    // Create session belonging to other user
    $otherUser = User::create([
        'name' => 'Other User',
        'username' => 'otheruser',
        'email' => 'other@cbt.com',
        'password' => bcrypt('password123'),
    ]);

    $otherSession = AssessmentSession::create([
        'assessment_id' => $this->assessment->id,
        'user_id' => $otherUser->id,
        'start_time' => now(),
        'end_time' => now()->addMinutes(60),
        'status' => 'in_progress',
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson("/api/v1/sessions/{$otherSession->id}/proctor-logs", [
        'event_type' => 'tab_switch',
    ]);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'status',
            'message',
            'errors' => ['session']
        ]);
});

test('admin can view proctoring logs of any candidate session', function () {
    $this->session->proctoringLogs()->create([
        'event_type' => 'tab_switch',
        'event_details' => 'Warning 1',
    ]);

    // Admin token
    $admin = User::where('username', 'admin')->first();
    $adminToken = auth('api')->login($admin);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $adminToken,
    ])->getJson("/api/v1/sessions/{$this->session->id}/proctor-logs");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});
