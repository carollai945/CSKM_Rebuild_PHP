<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_upload_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $staff = Staff::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->postJson('/api/v1/me/personal-data/photo', [
                'photo' => UploadedFile::fake()->image('avatar.jpg', 100, 100),
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.photo_url', fn ($url) => str_starts_with($url, '/storage/'));
    }

    public function test_upload_requires_image(): void
    {
        $user = User::factory()->create();
        Staff::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->postJson('/api/v1/me/personal-data/photo', [
                'photo' => UploadedFile::fake()->create('doc.pdf', 100),
            ]);

        $response->assertStatus(422);
    }
}
