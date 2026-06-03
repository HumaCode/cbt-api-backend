<?php

use App\Models\User;
use App\Models\Group;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Assessment;
use App\Models\AssessmentSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed');
    
    // Get candidate (Peserta) and authenticate
    $this->user = User::where('username', 'peserta')->first();
    
    // Add candidate to a group
    $this->group = Group::create(['name' => 'Kelas X-A']);
    $this->user->groups()->attach($this->group->id);

    $this->token = auth('api')->login($this->user);

    // Create a category
    $this->category = Category::create(['name' => 'Matematika']);

    // Create a PG question
    $this->question = Question::create([
        'category_id' => $this->category->id,
        'type' => 'pg',
        'difficulty' => 'easy',
        'content_text' => 'Berapakah 2 + 2?',
        'created_by' => User::where('username', 'admin')->first()->id,
    ]);

    $this->optionCorrect = $this->question->options()->create([
        'option_text' => '4',
        'is_correct' => true,
        'weight' => 10.00,
    ]);

    $this->optionIncorrect = $this->question->options()->create([
        'option_text' => '5',
        'is_correct' => false,
        'weight' => 0.00,
    ]);

    // Create Assessment target to this group
    $this->assessment = Assessment::create([
        'title' => 'Ujian Harian Matematika',
        'start_date' => now()->subHours(1)->toDateTimeString(),
        'end_date' => now()->addHours(2)->toDateTimeString(),
        'duration_minutes' => 60,
        'max_attempts' => 2,
        'passing_grade' => 70.00,
    ]);
    
    $this->assessment->groups()->attach($this->group->id);
    $this->assessment->questions()->attach($this->question->id, ['order_no' => 1]);
});

test('candidate can start an assessment session', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson("/api/v1/assessments/{$this->assessment->id}/start");

    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'in_progress');

    $this->assertDatabaseHas('assessment_sessions', [
        'assessment_id' => $this->assessment->id,
        'user_id' => $this->user->id,
        'status' => 'in_progress',
    ]);
});

test('candidate cannot start session if not in targeted group', function () {
    // Create another assessment that doesn't target our group
    $otherAssessment = Assessment::create([
        'title' => 'Ujian IPA',
        'start_date' => now()->subHours(1)->toDateTimeString(),
        'end_date' => now()->addHours(2)->toDateTimeString(),
        'duration_minutes' => 60,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson("/api/v1/assessments/{$otherAssessment->id}/start");

    $response->assertStatus(422)
        ->assertJsonStructure([
            'status',
            'message',
            'errors' => ['assessment']
        ]);
});

test('candidate can submit answers to their active session', function () {
    // Start session
    $session = AssessmentSession::create([
        'assessment_id' => $this->assessment->id,
        'user_id' => $this->user->id,
        'start_time' => now(),
        'end_time' => now()->addMinutes(60),
        'status' => 'in_progress',
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson("/api/v1/sessions/{$session->id}/answers", [
        'question_id' => $this->question->id,
        'selected_option_id' => $this->optionCorrect->id,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.is_correct', true)
        ->assertJsonPath('data.score_earned', "10.00");

    $this->assertDatabaseHas('session_answers', [
        'session_id' => $session->id,
        'question_id' => $this->question->id,
        'selected_option_id' => $this->optionCorrect->id,
        'is_correct' => true,
    ]);
});

test('candidate can finish session and see total score', function () {
    // Start session
    $session = AssessmentSession::create([
        'assessment_id' => $this->assessment->id,
        'user_id' => $this->user->id,
        'start_time' => now(),
        'end_time' => now()->addMinutes(60),
        'status' => 'in_progress',
    ]);

    // Submit correct answer
    $session->answers()->create([
        'question_id' => $this->question->id,
        'selected_option_id' => $this->optionCorrect->id,
        'is_correct' => true,
        'score_earned' => 10.00,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson("/api/v1/sessions/{$session->id}/finish");

    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'completed')
        ->assertJsonPath('data.total_score', "10.00");

    $this->assertDatabaseHas('assessment_sessions', [
        'id' => $session->id,
        'status' => 'completed',
        'total_score' => 10.00,
    ]);
});
