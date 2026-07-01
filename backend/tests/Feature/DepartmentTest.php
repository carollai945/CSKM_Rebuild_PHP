<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Title;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    // DT-INT-01: 部門新增成功
    public function test_can_list_departments(): void
    {
        Department::create([
            'department_no'   => 'D001',
            'department_name' => '業務部',
            'status'          => 'active',
        ]);

        $response = $this->getJson('/api/v1/master/departments');

        $response->assertStatus(200)
                 ->assertJsonPath('data.0.department_no', 'D001');
    }

    public function test_can_create_department(): void
    {
        $response = $this->postJson('/api/v1/master/departments', [
            'department_no'   => 'D001',
            'department_name' => '業務部',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.department_no', 'D001')
                 ->assertJsonPath('data.department_name', '業務部');

        $this->assertDatabaseHas('departments', ['department_no' => 'D001']);
    }

    // DT-UNIT-01: 部門代碼唯一性驗證
    public function test_duplicate_department_no_is_rejected(): void
    {
        Department::create([
            'department_no'   => 'D001',
            'department_name' => '業務部',
            'status'          => 'active',
        ]);

        $response = $this->postJson('/api/v1/master/departments', [
            'department_no'   => 'D001',
            'department_name' => '其他部',
        ]);

        $response->assertStatus(422);
    }

    // DT-UNIT-03: 必填欄位驗證
    public function test_missing_required_fields_are_rejected(): void
    {
        $response = $this->postJson('/api/v1/master/departments', [
            'department_name' => '業務部',
        ]);

        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/master/departments', [
            'department_no' => 'D001',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_update_department(): void
    {
        $department = Department::create([
            'department_no'   => 'D001',
            'department_name' => '業務部',
            'status'          => 'active',
        ]);

        $response = $this->putJson("/api/v1/master/departments/{$department->id}", [
            'department_name' => '業務二部',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.department_name', '業務二部');

        $this->assertDatabaseHas('departments', ['department_name' => '業務二部']);
    }

    public function test_can_delete_department_without_titles(): void
    {
        $department = Department::create([
            'department_no'   => 'D001',
            'department_name' => '業務部',
            'status'          => 'active',
        ]);

        $response = $this->deleteJson("/api/v1/master/departments/{$department->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    // DT-INT-04: 被引用的部門不得停用
    public function test_cannot_disable_department_with_titles(): void
    {
        $department = Department::create([
            'department_no'   => 'D001',
            'department_name' => '業務部',
            'status'          => 'active',
        ]);

        Title::create([
            'department_id' => $department->id,
            'title_no'      => 'T001',
            'title_name'    => '業務員',
            'status'        => 'active',
        ]);

        $response = $this->putJson("/api/v1/master/departments/{$department->id}", [
            'status' => 'inactive',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('error.code', 'DEPARTMENT_REFERENCED');
    }

    public function test_cannot_delete_department_with_titles(): void
    {
        $department = Department::create([
            'department_no'   => 'D001',
            'department_name' => '業務部',
            'status'          => 'active',
        ]);

        Title::create([
            'department_id' => $department->id,
            'title_no'      => 'T001',
            'title_name'    => '業務員',
            'status'        => 'active',
        ]);

        $response = $this->deleteJson("/api/v1/master/departments/{$department->id}");

        $response->assertStatus(422)
                 ->assertJsonPath('error.code', 'DEPARTMENT_REFERENCED');
    }
}
