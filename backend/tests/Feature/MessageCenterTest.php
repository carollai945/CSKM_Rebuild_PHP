<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_messages(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/v1/messages');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['announcements', 'pending_counts']]);
    }

    public function test_unauthenticated_cannot_get_messages(): void
    {
        $response = $this->getJson('/api/v1/messages');
        $response->assertStatus(401);
    }

    public function test_mark_message_read(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->patchJson('/api/v1/messages/1/read');
        $response->assertStatus(200)
            ->assertJsonPath('data.read', true);
    }
}
