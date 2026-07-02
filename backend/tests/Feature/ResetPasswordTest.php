<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reset_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create();
        $staff = Staff::factory()->create(['user_id' => $target->id]);

        $response = $this->actingAs($admin)
            ->postJson('/api/v1/auth/reset-password', [
                'staff_id'                  => $staff->id,
                'new_password'              => 'NewPassword123!',
                'new_password_confirmation' => 'NewPassword123!',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Password reset successfully.');
    }

    public function test_staff_cannot_reset_others_password(): void
    {
        $staff_user = User::factory()->create(['role' => 'staff']);
        $target = User::factory()->create();
        $staff = Staff::factory()->create(['user_id' => $target->id]);

        $response = $this->actingAs($staff_user)
            ->postJson('/api/v1/auth/reset-password', [
                'staff_id'                  => $staff->id,
                'new_password'              => 'NewPassword123!',
                'new_password_confirmation' => 'NewPassword123!',
            ]);

        $response->assertStatus(403);
    }
}
