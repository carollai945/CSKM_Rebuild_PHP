<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => Role::Admin]);
        $this->student = Student::factory()->create();
    }

    public function test_can_list_student_services(): void
    {
        StudentService::create([
            'student_id'   => $this->student->id,
            'service_type' => 'CALL',
            'content'      => 'Test',
            'status'       => 'OPEN',
        ]);

        Sanctum::actingAs($this->user);

        $this->getJson('/api/v1/student-services')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_filter_by_student(): void
    {
        $other = Student::factory()->create();
        StudentService::create(['student_id' => $this->student->id, 'service_type' => 'CALL', 'status' => 'OPEN']);
        StudentService::create(['student_id' => $other->id, 'service_type' => 'CALL', 'status' => 'OPEN']);

        Sanctum::actingAs($this->user);
        $response = $this->getJson("/api/v1/student-services?student_id={$this->student->id}");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
    }

    public function test_can_create_service_record(): void
    {
        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/v1/student-services', [
            'student_id'   => $this->student->id,
            'service_type' => 'VISIT',
            'content'      => 'Follow-up visit',
            'status'       => 'OPEN',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.service_type', 'VISIT');
        $this->assertDatabaseHas('student_services', ['student_id' => $this->student->id, 'service_type' => 'VISIT']);
    }

    public function test_can_update_service_record(): void
    {
        $service = StudentService::create([
            'student_id'   => $this->student->id,
            'service_type' => 'CALL',
            'status'       => 'OPEN',
        ]);

        Sanctum::actingAs($this->user);
        $this->putJson("/api/v1/student-services/{$service->id}", ['status' => 'CLOSED'])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'CLOSED');
    }

    public function test_can_delete_service_record(): void
    {
        $service = StudentService::create([
            'student_id'   => $this->student->id,
            'service_type' => 'CALL',
            'status'       => 'OPEN',
        ]);

        Sanctum::actingAs($this->user);
        $this->deleteJson("/api/v1/student-services/{$service->id}")
            ->assertStatus(204);
    }

    public function test_unauthenticated_cannot_access(): void
    {
        $this->getJson('/api/v1/student-services')->assertStatus(401);
    }
}
