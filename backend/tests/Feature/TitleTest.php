<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Title;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TitleTest extends TestCase
{
    use RefreshDatabase;

    private function createDepartment(string $no = 'D001', string $name = '業務部'): Department
    {
        return Department::create([
            'department_no'   => $no,
            'department_name' => $name,
            'status'          => 'active',
        ]);
    }

    // DT-INT-02: 職稱新增成功
    public function test_can_list_titles(): void
    {
        $department = $this->createDepartment();

        Title::create([
            'department_id' => $department->id,
            'title_no'      => 'T001',
            'title_name'    => '業務員',
            'status'        => 'active',
        ]);

        $response = $this->getJson('/api/v1/master/titles');

        $response->assertStatus(200)
                 ->assertJsonPath('data.0.title_no', 'T001');
    }

    public function test_can_create_title(): void
    {
        $department = $this->createDepartment();

        $response = $this->postJson('/api/v1/master/titles', [
            'department_id' => $department->id,
            'title_no'      => 'T001',
            'title_name'    => '業務員',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.title_no', 'T001')
                 ->assertJsonPath('data.title_name', '業務員');

        $this->assertDatabaseHas('titles', ['title_no' => 'T001']);
    }

    // DT-UNIT-02: 職稱代碼在同部門下不得重複
    public function test_duplicate_title_no_in_same_department_is_rejected(): void
    {
        $department = $this->createDepartment();

        Title::create([
            'department_id' => $department->id,
            'title_no'      => 'T001',
            'title_name'    => '業務員',
            'status'        => 'active',
        ]);

        $response = $this->postJson('/api/v1/master/titles', [
            'department_id' => $department->id,
            'title_no'      => 'T001',
            'title_name'    => '其他職稱',
        ]);

        $response->assertStatus(422);
    }

    public function test_same_title_no_allowed_in_different_departments(): void
    {
        $dept1 = $this->createDepartment('D001', '業務部');
        $dept2 = $this->createDepartment('D002', '行政部');

        Title::create([
            'department_id' => $dept1->id,
            'title_no'      => 'T001',
            'title_name'    => '員工',
            'status'        => 'active',
        ]);

        $response = $this->postJson('/api/v1/master/titles', [
            'department_id' => $dept2->id,
            'title_no'      => 'T001',
            'title_name'    => '員工',
        ]);

        $response->assertStatus(201);
    }

    // DT-UNIT-03: 必填欄位驗證
    public function test_missing_required_fields_are_rejected(): void
    {
        $department = $this->createDepartment();

        $response = $this->postJson('/api/v1/master/titles', [
            'department_id' => $department->id,
            'title_name'    => '業務員',
        ]);

        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/master/titles', [
            'department_id' => $department->id,
            'title_no'      => 'T001',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_update_title(): void
    {
        $department = $this->createDepartment();

        $title = Title::create([
            'department_id' => $department->id,
            'title_no'      => 'T001',
            'title_name'    => '業務員',
            'status'        => 'active',
        ]);

        $response = $this->putJson("/api/v1/master/titles/{$title->id}", [
            'title_name' => '資深業務員',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.title_name', '資深業務員');

        $this->assertDatabaseHas('titles', ['title_name' => '資深業務員']);
    }

    public function test_can_delete_title(): void
    {
        $department = $this->createDepartment();

        $title = Title::create([
            'department_id' => $department->id,
            'title_no'      => 'T001',
            'title_name'    => '業務員',
            'status'        => 'active',
        ]);

        $response = $this->deleteJson("/api/v1/master/titles/{$title->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('titles', ['id' => $title->id]);
    }

    public function test_title_requires_existing_department(): void
    {
        $response = $this->postJson('/api/v1/master/titles', [
            'department_id' => 9999,
            'title_no'      => 'T001',
            'title_name'    => '業務員',
        ]);

        $response->assertStatus(422);
    }
}
