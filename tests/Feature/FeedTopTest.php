<?php

namespace Tests\Feature;

use App\Models\Entry;
use App\Models\Title;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedTopTest extends TestCase
{
    use RefreshDatabase;

    public function test_top_feed_returns_entries_and_titles(): void
    {
        $user = User::factory()->create();
        $title = Title::factory()->for($user)->create();
        Entry::factory()->for($title)->for($user)->create();

        $response = $this->getJson('/api/feed/top');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'entries',
                    'titles',
                ],
            ]);
    }
}
