<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class StudentAssignmentTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); }
    private function adminUser(): User { return User::factory()->create(['role'=>Role::Admin]); }
    private function makeStudent(): Student {
        static $n=1;
        return Student::create(['student_no'=>'ST'.str_pad($n++,4,'0',STR_PAD_LEFT),'name'=>'學員','status'=>'ACTIVE']);
    }
    public function test_management_can_list_students_for_assignment(): void {
        Sanctum::actingAs($this->adminUser());
        $this->makeStudent();
        $this->getJson('/api/v1/students/assign')->assertStatus(200);
    }
    public function test_can_batch_assign_advisor(): void {
        Sanctum::actingAs($this->adminUser());
        $s1=$this->makeStudent(); $s2=$this->makeStudent();
        $advisor=Staff::factory()->create();
        $this->postJson('/api/v1/students/assign',['student_ids'=>[$s1->id,$s2->id],'advisor_staff_id'=>$advisor->id])
            ->assertStatus(200)->assertJsonPath('message','批次分配成功。');
        $this->assertDatabaseHas('students',['id'=>$s1->id,'advisor_staff_id'=>$advisor->id]);
    }
    public function test_non_management_cannot_access(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Staff]));
        $this->getJson('/api/v1/students/assign')->assertStatus(403);
    }
    public function test_unauthenticated(): void { $this->getJson('/api/v1/students/assign')->assertStatus(401); }
}
