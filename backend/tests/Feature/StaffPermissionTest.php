<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_permission_groups(): void
    {
        $user = User::factory()->create(['role' => Role::Admin]);
        $response = $this->actingAs($user)->getJson('/api/v1/permission-groups');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['roles', 'modules']]);
    }

    public function test_admin_can_view_staff_permissions(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $target = User::factory()->create(['role' => Role::Staff]);
        $staff = Staff::factory()->create(['user_id' => $target->id]);

        $response = $this->actingAs($admin)->getJson("/api/v1/staff/{$staff->id}/permissions");
        $response->assertStatus(200)
            ->assertJsonPath('data.staff_id', $staff->id);
    }

    public function test_admin_can_update_staff_permissions(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $target = User::factory()->create(['role' => Role::Staff]);
        $staff = Staff::factory()->create(['user_id' => $target->id]);

        $response = $this->actingAs($admin)->putJson("/api/v1/staff/{$staff->id}/permissions", [
            'role'    => 'regmgr',
            'modules' => ['A00', 'A01', 'B00'],
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('data.role', 'regmgr');
    }

    public function test_staff_cannot_update_permissions(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        $target = User::factory()->create(['role' => Role::Staff]);
        $staff = Staff::factory()->create(['user_id' => $target->id]);

        $response = $this->actingAs($user)->putJson("/api/v1/staff/{$staff->id}/permissions", [
            'role' => 'ceo',
        ]);
        $response->assertStatus(403);
    }
}
