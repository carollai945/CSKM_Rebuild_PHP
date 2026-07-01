<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPass123')]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/me/change-password', [
            'current_password'          => 'OldPass123',
            'new_password'              => 'NewPass456',
            'new_password_confirmation' => 'NewPass456',
        ]);

        $response->assertStatus(200)->assertJsonPath('message', '密碼修改成功。');
        $this->assertTrue(Hash::check('NewPass456', $user->fresh()->password));
    }

    public function test_wrong_current_password_returns_422(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPass123')]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/me/change-password', [
            'current_password'          => 'WrongPass!',
            'new_password'              => 'NewPass456',
            'new_password_confirmation' => 'NewPass456',
        ]);

        $response->assertStatus(422);
    }

    public function test_new_password_same_as_current_returns_422(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPass123')]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/me/change-password', [
            'current_password'          => 'OldPass123',
            'new_password'              => 'OldPass123',
            'new_password_confirmation' => 'OldPass123',
        ]);

        $response->assertStatus(422);
    }

    public function test_confirmation_mismatch_returns_422(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPass123')]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/me/change-password', [
            'current_password'          => 'OldPass123',
            'new_password'              => 'NewPass456',
            'new_password_confirmation' => 'Different789',
        ]);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_cannot_change_password(): void
    {
        $response = $this->postJson('/api/v1/me/change-password', [
            'current_password'          => 'OldPass123',
            'new_password'              => 'NewPass456',
            'new_password_confirmation' => 'NewPass456',
        ]);

        $response->assertStatus(401);
    }
}
