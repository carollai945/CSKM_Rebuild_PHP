<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseTest extends TestCase
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

    public function test_index_returns_course_list(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Course::create([
            'course_name' => '國文課程',
            'status' => 'ACTIVE',
        ]);

        Course::create([
            'course_name' => '數學課程',
            'status' => 'INACTIVE',
        ]);

        $response = $this->getJson('/api/v1/courses');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_status(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Course::create([
            'course_name' => '啟用課程',
            'status' => 'ACTIVE',
        ]);

        Course::create([
            'course_name' => '停用課程',
            'status' => 'INACTIVE',
        ]);

        $response = $this->getJson('/api/v1/courses?status=ACTIVE');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.course_name', '啟用課程')
            ->assertJsonPath('data.0.status', 'ACTIVE');
    }

    public function test_admin_can_create_course(): void
    {
        Sanctum::actingAs($this->adminUser());

        $response = $this->postJson('/api/v1/courses', [
            'course_name' => '英文課程',
            'course_code' => 'ENG',
            'description' => '英文基礎課程',
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.course_name', '英文課程')
            ->assertJsonPath('data.course_code', 'ENG');

        $this->assertDatabaseHas('courses', [
            'course_name' => '英文課程',
            'course_code' => 'ENG',
            'description' => '英文基礎課程',
            'status' => 'ACTIVE',
        ]);
    }

    public function test_non_admin_cannot_create_course(): void
    {
        Sanctum::actingAs($this->staffUser());

        $response = $this->postJson('/api/v1/courses', [
            'course_name' => '英文課程',
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_course(): void
    {
        Sanctum::actingAs($this->adminUser());

        $course = Course::create([
            'course_name' => '原課程',
            'status' => 'ACTIVE',
        ]);

        $response = $this->putJson("/api/v1/courses/{$course->id}", [
            'course_name' => '新課程',
            'course_code' => 'NEW',
            'description' => '更新後課程',
            'status' => 'INACTIVE',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.course_name', '新課程')
            ->assertJsonPath('data.description', '更新後課程')
            ->assertJsonPath('data.status', 'INACTIVE');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'course_name' => '新課程',
            'course_code' => 'NEW',
            'description' => '更新後課程',
            'status' => 'INACTIVE',
        ]);
    }

    public function test_admin_can_delete_course(): void
    {
        Sanctum::actingAs($this->adminUser());

        $course = Course::create([
            'course_name' => '待刪除課程',
            'status' => 'ACTIVE',
        ]);

        $response = $this->deleteJson("/api/v1/courses/{$course->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_create_course_requires_course_name(): void
    {
        Sanctum::actingAs($this->adminUser());

        $response = $this->postJson('/api/v1/courses', [
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['course_name']);
    }
}
