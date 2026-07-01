<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PersonalDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_personal_data(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        $staff = Staff::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/me/personal-data');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $staff->id)
            ->assertJsonPath('data.currentStatus', 'EDITABLE')
            ->assertJsonStructure(['data' => ['id', 'name', 'currentStatus', 'allowedActions']]);
    }

    public function test_unauthenticated_cannot_get_personal_data(): void
    {
        $this->getJson('/api/v1/me/personal-data')->assertStatus(401);
    }

    public function test_authenticated_user_can_update_personal_data(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        $staff = Staff::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/me/personal-data', [
            'phone'      => '0912345678',
            'gender'     => 'M',
            'blood_type' => 'A',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('staff', ['id' => $staff->id, 'phone' => '0912345678']);
    }

    public function test_unauthenticated_cannot_update_personal_data(): void
    {
        $this->putJson('/api/v1/me/personal-data', ['phone' => '0912345678'])->assertStatus(401);
    }

    public function test_admin_can_view_staff_personal_data_readonly(): void
    {
        $adminUser = User::factory()->create(['role' => Role::Admin]);
        $targetUser = User::factory()->create(['role' => Role::Staff]);
        $target = Staff::factory()->create(['user_id' => $targetUser->id]);
        Sanctum::actingAs($adminUser);

        $response = $this->getJson("/api/v1/staff/{$target->id}/personal-data");

        $response->assertStatus(200)
            ->assertJsonPath('data.currentStatus', 'READONLY')
            ->assertJsonPath('data.allowedActions', []);
    }

    public function test_non_management_cannot_view_other_staff_personal_data(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        $targetUser = User::factory()->create(['role' => Role::Staff]);
        $target = Staff::factory()->create(['user_id' => $targetUser->id]);
        Sanctum::actingAs($user);

        $this->getJson("/api/v1/staff/{$target->id}/personal-data")
            ->assertStatus(403);
    }
}
