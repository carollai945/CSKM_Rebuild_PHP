<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentFeedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class StudentFeedbackTest extends TestCase {
    use RefreshDatabase;
    private Student $student;
    protected function setUp(): void {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin]));
        $this->student = Student::create(['student_no'=>'ST001','name'=>'test','status'=>'ACTIVE']);
    }
    public function test_can_list_feedbacks(): void {
        StudentFeedback::create(['student_id'=>$this->student->id,'content'=>'意見']);
        $this->getJson('/api/v1/student-feedbacks')->assertStatus(200);
    }
    public function test_can_create_feedback(): void {
        $this->postJson('/api/v1/student-feedbacks',['student_id'=>$this->student->id,'content'=>'很好'])
            ->assertStatus(201)->assertJsonPath('data.content','很好');
    }
    public function test_can_resolve_feedback(): void {
        $u=User::factory()->create(['role'=>Role::Admin]); Staff::factory()->create(['user_id'=>$u->id]);
        Sanctum::actingAs($u);
        $fb=StudentFeedback::create(['student_id'=>$this->student->id,'content'=>'issue']);
        $this->putJson("/api/v1/student-feedbacks/{$fb->id}",['status'=>'RESOLVED','reply'=>'已處理'])
            ->assertStatus(200)->assertJsonPath('data.status','RESOLVED');
    }
    public function test_unauthenticated(): void { auth()->forgetGuards(); $this->getJson('/api/v1/student-feedbacks')->assertStatus(401); }
}
