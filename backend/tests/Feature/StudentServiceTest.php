<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentServiceTest extends TestCase
{
    use RefreshDatabase;

    private Staff $staff;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staff = Staff::factory()->create(['role' => 'admin']);
        $this->student = Student::factory()->create();
    }

    public function test_can_list_student_services(): void
    {
        StudentService::create([
            'student_id'   => $this->student->id,
            'staff_id'     => $this->staff->id,
            'service_type' => 'CALL',
            'content'      => 'Test',
            'status'       => 'OPEN',
        ]);

        $this->actingAs($this->staff)
            ->getJson('/api/v1/student-services')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_filter_by_student(): void
    {
        $other = Student::factory()->create();
        StudentService::create(['student_id' => $this->student->id, 'staff_id' => $this->staff->id, 'service_type' => 'CALL', 'status' => 'OPEN']);
        StudentService::create(['student_id' => $other->id, 'staff_id' => $this->staff->id, 'service_type' => 'CALL', 'status' => 'OPEN']);

        $response = $this->actingAs($this->staff)
            ->getJson("/api/v1/student-services?student_id={$this->student->id}");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
    }

    public function test_can_create_service_record(): void
    {
        $response = $this->actingAs($this->staff)
            ->postJson('/api/v1/student-services', [
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
            'staff_id'     => $this->staff->id,
            'service_type' => 'CALL',
            'status'       => 'OPEN',
        ]);

        $this->actingAs($this->staff)
            ->putJson("/api/v1/student-services/{$service->id}", ['status' => 'CLOSED'])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'CLOSED');
    }

    public function test_can_delete_service_record(): void
    {
        $service = StudentService::create([
            'student_id' => $this->student->id,
            'staff_id'   => $this->staff->id,
            'service_type' => 'CALL',
            'status'       => 'OPEN',
        ]);

        $this->actingAs($this->staff)
            ->deleteJson("/api/v1/student-services/{$service->id}")
            ->assertStatus(204);
    }

    public function test_unauthenticated_cannot_access(): void
    {
        $this->getJson('/api/v1/student-services')->assertStatus(401);
    }
}
