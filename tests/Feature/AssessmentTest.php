<?php

use App\Models\User;
use App\Models\Group;
use App\Models\Category;
use App\Models\Question;
use App\Models\Assessment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed');
    
    // Create an authenticated user
    $this->user = User::where('username', 'admin')->first();
    $this->token = auth('api')->login($this->user);

    // Create dummy dependencies
    $this->group = Group::create(['name' => 'Kelas A']);
    $this->category = Category::create(['name' => 'Umum']);
    $this->question = Question::create([
        'category_id' => $this->category->id,
        'type' => 'essay',
        'difficulty' => 'easy',
        'content_text' => 'Berapakah 1 + 1?',
        'created_by' => $this->user->id,
    ]);
});

test('user can get all assessments', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson('/api/v1/assessments');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Daftar ujian berhasil diambil.',
        ]);
});

test('user can create an assessment with groups and questions', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/assessments', [
        'title' => 'Ujian Akhir Semester',
        'start_date' => now()->toDateTimeString(),
        'end_date' => now()->addDays(7)->toDateTimeString(),
        'duration_minutes' => 90,
        'max_attempts' => 2,
        'passing_grade' => 75.00,
        'group_ids' => [$this->group->id],
        'questions' => [$this->question->id],
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'Ujian Akhir Semester');

    $this->assertDatabaseHas('assessments', [
        'title' => 'Ujian Akhir Semester',
        'duration_minutes' => 90,
    ]);

    $assessmentId = $response->json('data.id');

    $this->assertDatabaseHas('assessment_group', [
        'assessment_id' => $assessmentId,
        'group_id' => $this->group->id,
    ]);

    $this->assertDatabaseHas('assessment_question', [
        'assessment_id' => $assessmentId,
        'question_id' => $this->question->id,
        'order_no' => 1,
    ]);
});

test('user can update an assessment', function () {
    $assessment = Assessment::create([
        'title' => 'Kuis Harian',
        'start_date' => now(),
        'end_date' => now()->addDay(),
        'duration_minutes' => 30,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->putJson('/api/v1/assessments/' . $assessment->id, [
        'title' => 'Kuis Mingguan',
        'duration_minutes' => 45,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.title', 'Kuis Mingguan')
        ->assertJsonPath('data.duration_minutes', 45);
});

test('user can delete an assessment', function () {
    $assessment = Assessment::create([
        'title' => 'Ujian Dihapus',
        'start_date' => now(),
        'end_date' => now()->addDay(),
        'duration_minutes' => 60,
    ]);

    $assessment->groups()->attach($this->group->id);
    $assessment->questions()->attach($this->question->id, ['order_no' => 1]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->deleteJson('/api/v1/assessments/' . $assessment->id);

    $response->assertStatus(200);

    $this->assertDatabaseMissing('assessments', ['id' => $assessment->id]);
    $this->assertDatabaseMissing('assessment_group', ['assessment_id' => $assessment->id]);
    $this->assertDatabaseMissing('assessment_question', ['assessment_id' => $assessment->id]);
});
