<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_is_created_with_valid_data(): void
    {
        $user = User::factory()->create();
        $payload = [
            'title' => 'Test Book',
            'author' => 'Admin User',
            'summary' => 'This is a valid summary with enough characters.',
            'isbn' => '9781111111111',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/books', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('books', [
            'title' => $payload['title'],
            'author' => $payload['author'],
            'isbn' => $payload['isbn'],
        ]);
    }

    public function test_book_is_not_created_with_invalid_data(): void
    {
        $user = User::factory()->create();
        $payload = [
            'title' => 'No',
            'author' => 'Admin User',
            'summary' => 'This is a valid summary with enough characters.',
            'isbn' => '9782222222222',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/books', $payload);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('books', [
            'isbn' => $payload['isbn'],
        ]);
    }

    public function test_book_is_not_created_when_unauthenticated(): void
    {
        $payload = [
            'title' => 'Another Book',
            'author' => 'Admin User',
            'summary' => 'This is a valid summary with enough characters.',
            'isbn' => '9783333333333',
        ];

        $response = $this->postJson('/api/v1/books', $payload);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('books', [
            'isbn' => $payload['isbn'],
        ]);
    }
}
