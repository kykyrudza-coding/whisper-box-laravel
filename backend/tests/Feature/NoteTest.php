<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\MoodEnum;
use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_notes_can_be_listed(): void
    {
        Note::factory()->count(3)->create();

        $response = $this->getJson('/api/notes');

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function test_single_note_can_be_shown(): void
    {
        $note = Note::factory()->create();

        $response = $this->getJson("/api/notes/$note->id");

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
            ]);
    }

    #[Test]
    public function test_note_can_be_created(): void
    {
        $payload = [
            'title' => 'Тестова нотатка',
            'content' => 'Текст тестової нотатки',
            'mood' => MoodEnum::HAPPY->value,
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Note created successfully',
            ]);

        $this->assertDatabaseHas('notes', $payload);
    }

    #[Test]
    public function test_note_title_is_required(): void
    {
        $payload = [
            'content' => 'Текст без заголовка',
            'mood' => MoodEnum::HAPPY->value,
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function test_note_content_is_required(): void
    {
        $payload = [
            'title' => 'Без контенту',
            'mood' => MoodEnum::HAPPY->value,
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    #[Test]
    public function test_note_mood_must_be_valid(): void
    {
        $payload = [
            'title' => 'Поганий mood',
            'content' => 'Тут неправильний mood',
            'mood' => 'super_angry_banana',
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mood']);
    }
}
