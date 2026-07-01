<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubjectTest extends TestCase
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

    public function test_index_returns_subject_list(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Subject::create([
            'subject_name' => '國文',
            'status' => 'ACTIVE',
        ]);

        Subject::create([
            'subject_name' => '數學',
            'status' => 'INACTIVE',
        ]);

        $response = $this->getJson('/api/v1/subjects');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_course_id(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $courseA = Course::create([
            'course_name' => '文學課程',
            'status' => 'ACTIVE',
        ]);

        $courseB = Course::create([
            'course_name' => '理學課程',
            'status' => 'ACTIVE',
        ]);

        Subject::create([
            'course_id' => $courseA->id,
            'subject_name' => '國文',
            'status' => 'ACTIVE',
        ]);

        Subject::create([
            'course_id' => $courseB->id,
            'subject_name' => '物理',
            'status' => 'ACTIVE',
        ]);

        $response = $this->getJson("/api/v1/subjects?course_id={$courseA->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.subject_name', '國文')
            ->assertJsonPath('data.0.course_id', $courseA->id);
    }

    public function test_admin_can_create_subject(): void
    {
        Sanctum::actingAs($this->adminUser());

        $course = Course::create([
            'course_name' => '英文課程',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson('/api/v1/subjects', [
            'course_id' => $course->id,
            'subject_name' => '英文',
            'subject_code' => 'ENG-01',
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.subject_name', '英文')
            ->assertJsonPath('data.subject_code', 'ENG-01');

        $this->assertDatabaseHas('subjects', [
            'course_id' => $course->id,
            'subject_name' => '英文',
            'subject_code' => 'ENG-01',
            'status' => 'ACTIVE',
        ]);
    }

    public function test_non_admin_cannot_create_subject(): void
    {
        Sanctum::actingAs($this->staffUser());

        $response = $this->postJson('/api/v1/subjects', [
            'subject_name' => '英文',
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_subject(): void
    {
        Sanctum::actingAs($this->adminUser());

        $course = Course::create([
            'course_name' => '數學課程',
            'status' => 'ACTIVE',
        ]);

        $subject = Subject::create([
            'subject_name' => '代數',
            'status' => 'ACTIVE',
        ]);

        $response = $this->putJson("/api/v1/subjects/{$subject->id}", [
            'course_id' => $course->id,
            'subject_name' => '幾何',
            'subject_code' => 'MATH-02',
            'status' => 'INACTIVE',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.subject_name', '幾何')
            ->assertJsonPath('data.status', 'INACTIVE');

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'course_id' => $course->id,
            'subject_name' => '幾何',
            'subject_code' => 'MATH-02',
            'status' => 'INACTIVE',
        ]);
    }

    public function test_admin_can_delete_subject(): void
    {
        Sanctum::actingAs($this->adminUser());

        $subject = Subject::create([
            'subject_name' => '待刪除科目',
            'status' => 'ACTIVE',
        ]);

        $response = $this->deleteJson("/api/v1/subjects/{$subject->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_create_subject_requires_subject_name(): void
    {
        Sanctum::actingAs($this->adminUser());

        $response = $this->postJson('/api/v1/subjects', [
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject_name']);
    }
}
