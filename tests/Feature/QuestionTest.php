<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed');
    
    // Create an authenticated user
    $this->user = User::where('username', 'admin')->first();
    $this->token = auth('api')->login($this->user);

    // Create a dummy category
    $this->category = Category::create(['name' => 'Matematika']);
});

test('user can get all questions paginated', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson('/api/v1/questions');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Daftar soal berhasil diambil.',
        ]);
});

test('user can create a PG question with options', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/questions', [
        'category_id' => $this->category->id,
        'type' => 'pg',
        'difficulty' => 'medium',
        'content_text' => 'Berapakah 2 + 2?',
        'options' => [
            ['option_text' => '3', 'is_correct' => false],
            ['option_text' => '4', 'is_correct' => true, 'weight' => 1.00],
            ['option_text' => '5', 'is_correct' => false],
        ],
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.content_text', 'Berapakah 2 + 2?');

    $this->assertDatabaseHas('questions', [
        'content_text' => 'Berapakah 2 + 2?',
        'type' => 'pg',
    ]);

    $this->assertDatabaseHas('question_options', [
        'option_text' => '4',
        'is_correct' => true,
    ]);
});

test('user can create a question with file attachments', function () {
    Storage::fake('public');
    
    $file = UploadedFile::fake()->image('geometry_diagram.jpg');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/questions', [
        'category_id' => $this->category->id,
        'type' => 'essay',
        'difficulty' => 'hard',
        'content_text' => 'Jelaskan rumus Pythagoras berdasarkan gambar berikut.',
        'attachments' => [$file],
    ]);

    $response->assertStatus(201);
    
    $createdId = $response->json('data.id');
    $question = Question::find($createdId);
    expect($question->getMedia('attachments'))->toHaveCount(1);
});

test('user can update a question and options', function () {
    $question = Question::create([
        'category_id' => $this->category->id,
        'type' => 'pg',
        'difficulty' => 'easy',
        'content_text' => 'Siapa presiden pertama RI?',
        'created_by' => $this->user->id,
    ]);

    $question->options()->create([
        'option_text' => 'Soekarno',
        'is_correct' => true,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->putJson('/api/v1/questions/' . $question->id, [
        'content_text' => 'Siapakah nama presiden pertama Indonesia?',
        'options' => [
            ['option_text' => 'Ir. Soekarno', 'is_correct' => true, 'weight' => 1.00],
            ['option_text' => 'Soeharto', 'is_correct' => false],
        ]
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('questions', [
        'id' => $question->id,
        'content_text' => 'Siapakah nama presiden pertama Indonesia?',
    ]);

    $this->assertDatabaseHas('question_options', [
        'option_text' => 'Ir. Soekarno',
        'is_correct' => true,
    ]);
});

test('user can delete a question and its options', function () {
    $question = Question::create([
        'category_id' => $this->category->id,
        'type' => 'pg',
        'difficulty' => 'easy',
        'content_text' => 'Soal Hapus',
        'created_by' => $this->user->id,
    ]);

    $option = $question->options()->create([
        'option_text' => 'Opsi Hapus',
        'is_correct' => true,
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->deleteJson('/api/v1/questions/' . $question->id);

    $response->assertStatus(200);

    $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    $this->assertDatabaseMissing('question_options', ['id' => $option->id]);
});
