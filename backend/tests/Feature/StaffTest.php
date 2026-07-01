<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Department;
use App\Models\Region;
use App\Models\Staff;
use App\Models\Title;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_staff_list(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        $staff = Staff::create([
            'staff_no' => 'E001',
            'name' => '王小明',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/staff');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $staff->id)
            ->assertJsonPath('data.0.staff_no', 'E001');
    }

    public function test_index_filters_by_keyword(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        Staff::create([
            'staff_no' => 'E001',
            'name' => '王小明',
            'status' => 'ACTIVE',
        ]);
        Staff::create([
            'staff_no' => 'E002',
            'name' => '李小華',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/staff?keyword=王');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', '王小明');
    }

    public function test_index_filters_by_status(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        Staff::create([
            'staff_no' => 'E001',
            'name' => '王小明',
            'status' => 'ACTIVE',
        ]);
        Staff::create([
            'staff_no' => 'E002',
            'name' => '李小華',
            'status' => 'LEFT',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/staff?status=ACTIVE');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'ACTIVE');
    }

    public function test_admin_can_create_staff(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        [$region, $department, $title] = $this->createReferenceData();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/staff', [
            'staff_no' => 'E001',
            'name' => '王小明',
            'abbr' => '小明',
            'region_id' => $region->id,
            'department_id' => $department->id,
            'title_id' => $title->id,
            'join_date' => '2026-01-01',
            'leave_date' => null,
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.staff_no', 'E001')
            ->assertJsonPath('data.name', '王小明');

        $this->assertDatabaseHas('staff', [
            'staff_no' => 'E001',
            'name' => '王小明',
        ]);
    }

    public function test_staff_no_must_be_unique(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        Staff::create([
            'staff_no' => 'E001',
            'name' => '王小明',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/staff', [
            'staff_no' => 'E001',
            'name' => '李小華',
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('staff_no');
    }

    public function test_admin_can_update_staff(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        [$region, $department, $title] = $this->createReferenceData();
        $staff = Staff::create([
            'staff_no' => 'E001',
            'name' => '王小明',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/v1/staff/{$staff->id}", [
            'name' => '王大明',
            'abbr' => '大明',
            'region_id' => $region->id,
            'department_id' => $department->id,
            'title_id' => $title->id,
            'join_date' => '2026-01-01',
            'status' => 'INACTIVE',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', '王大明')
            ->assertJsonPath('data.status', 'INACTIVE');

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'name' => '王大明',
            'status' => 'INACTIVE',
        ]);
    }

    public function test_admin_can_change_status(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $staff = Staff::create([
            'staff_no' => 'E001',
            'name' => '王小明',
            'join_date' => '2026-01-01',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->patchJson("/api/v1/staff/{$staff->id}/status", [
            'status' => 'LEFT',
            'leave_date' => '2026-07-01',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'LEFT')
            ->assertJsonPath('data.leave_date', '2026-07-01');

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'status' => 'LEFT',
            'leave_date' => '2026-07-01',
        ]);
    }

    public function test_non_admin_cannot_create_staff(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/staff', [
            'staff_no' => 'E001',
            'name' => '王小明',
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(403);
    }

    public function test_show_returns_staff(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        $staff = Staff::create([
            'staff_no' => 'E001',
            'name' => '王小明',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/staff/{$staff->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $staff->id)
            ->assertJsonPath('data.name', '王小明');
    }

    public function test_autocomplete_returns_name_list(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        Staff::create([
            'staff_no' => 'E001',
            'name' => '王小明',
            'status' => 'ACTIVE',
        ]);
        Staff::create([
            'staff_no' => 'E002',
            'name' => '李小華',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/staff/autocomplete?keyword=王');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', '王小明')
            ->assertJsonPath('data.0.staff_no', 'E001');
    }

    private function createReferenceData(): array
    {
        $region = Region::factory()->create();
        $department = Department::create([
            'region_id' => $region->id,
            'department_no' => 'D001',
            'department_name' => '業務部',
            'status' => 'active',
        ]);
        $title = Title::create([
            'region_id' => $region->id,
            'department_id' => $department->id,
            'title_no' => 'T001',
            'title_name' => '主任',
            'status' => 'active',
        ]);

        return [$region, $department, $title];
    }
}
