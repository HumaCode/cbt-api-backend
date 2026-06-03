<?php

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed');
    
    // Create an authenticated user
    $this->user = User::where('username', 'admin')->first();
    $this->token = auth('api')->login($this->user);
});

test('user can get all categories', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->getJson('/api/v1/categories');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Daftar kategori berhasil diambil.',
        ]);
});

test('user can create a category', function () {
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/categories', [
        'name' => 'Fisika Dasar',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'Fisika Dasar');

    $this->assertDatabaseHas('categories', [
        'name' => 'Fisika Dasar',
    ]);
});

test('user can create a child category', function () {
    $parent = Category::create(['name' => 'Sains']);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/categories', [
        'name' => 'Biologi',
        'parent_id' => $parent->id,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.parent_id', $parent->id);
});

test('user can update a category', function () {
    $category = Category::create(['name' => 'Kimia']);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->putJson('/api/v1/categories/' . $category->id, [
        'name' => 'Kimia Organik',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'Kimia Organik');
});

test('category cannot be parent of itself', function () {
    $category = Category::create(['name' => 'Astronomi']);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->putJson('/api/v1/categories/' . $category->id, [
        'parent_id' => $category->id,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['parent_id']);
});

test('user can delete a category', function () {
    $category = Category::create(['name' => 'Geografi']);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->deleteJson('/api/v1/categories/' . $category->id);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus.',
        ]);

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});
