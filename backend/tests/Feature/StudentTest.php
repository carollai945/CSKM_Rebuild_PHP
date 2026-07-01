<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => Role::Admin]);
    }

    private function staffUser(): User
    {
        return User::factory()->create(['role' => Role::Staff]);
    }

    private function makeStudent(array $attrs = []): Student
    {
        static $seq = 1;
        return Student::create(array_merge([
            'student_no' => 'S' . str_pad($seq++, 4, '0', STR_PAD_LEFT),
            'name'       => '測試學員',
            'status'     => 'ACTIVE',
        ], $attrs));
    }

    public function test_index_returns_student_list(): void
    {
        Sanctum::actingAs($this->staffUser());
        $this->makeStudent(['name' => '王大明']);
        $this->makeStudent(['name' => '李小美']);

        $response = $this->getJson('/api/v1/students');

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_keyword(): void
    {
        Sanctum::actingAs($this->staffUser());
        $this->makeStudent(['name' => '王大明']);
        $this->makeStudent(['name' => '李小美']);

        $response = $this->getJson('/api/v1/students?keyword=王');

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_index_filters_by_status(): void
    {
        Sanctum::actingAs($this->staffUser());
        $this->makeStudent(['status' => 'ACTIVE']);
        $this->makeStudent(['status' => 'INACTIVE']);

        $response = $this->getJson('/api/v1/students?status=ACTIVE');

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_staff_can_create_student(): void
    {
        Sanctum::actingAs($this->staffUser());

        $response = $this->postJson('/api/v1/students', [
            'student_no' => 'S0001',
            'name'       => '新學員',
            'status'     => 'ACTIVE',
        ]);

        $response->assertStatus(201)->assertJsonPath('data.name', '新學員');
        $this->assertDatabaseHas('students', ['student_no' => 'S0001']);
    }

    public function test_staff_can_update_student(): void
    {
        Sanctum::actingAs($this->staffUser());
        $student = $this->makeStudent();

        $response = $this->putJson("/api/v1/students/{$student->id}", ['name' => '更新名稱']);

        $response->assertStatus(200)->assertJsonPath('data.name', '更新名稱');
    }

    public function test_admin_can_change_advisor(): void
    {
        Sanctum::actingAs($this->adminUser());
        $student = $this->makeStudent();
        $staff = \App\Models\Staff::factory()->create();

        $response = $this->patchJson("/api/v1/students/{$student->id}/advisor", [
            'advisor_staff_id' => $staff->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('students', ['id' => $student->id, 'advisor_staff_id' => $staff->id]);
    }

    public function test_can_get_student_courses(): void
    {
        Sanctum::actingAs($this->staffUser());
        $student = $this->makeStudent();
        $course = Course::create(['course_name' => '測試課程', 'status' => 'ACTIVE']);
        $student->studentCourses()->create(['course_id' => $course->id, 'status' => 'ENROLLED']);

        $response = $this->getJson("/api/v1/students/{$student->id}/courses");

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_can_update_student_courses(): void
    {
        Sanctum::actingAs($this->staffUser());
        $student = $this->makeStudent();
        $course = Course::create(['course_name' => '課程A', 'status' => 'ACTIVE']);

        $response = $this->putJson("/api/v1/students/{$student->id}/courses", [
            'courses' => [
                ['course_id' => $course->id, 'status' => 'ENROLLED', 'joined_at' => '2026-07-01'],
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('student_courses', ['student_id' => $student->id, 'course_id' => $course->id]);
    }

    public function test_unauthenticated_cannot_access_students(): void
    {
        $response = $this->getJson('/api/v1/students');

        $response->assertStatus(401);
    }
}
