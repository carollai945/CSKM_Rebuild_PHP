<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentTest extends TestCase {
    use RefreshDatabase;
    private User $user;
    private Staff $staff;
    private Student $student;

    protected function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create(['role' => Role::Admin]);
        $this->staff = Staff::factory()->create(['user_id' => $this->user->id]);
        $this->student = Student::create(['student_no'=>'ST001','name'=>'測試學員','status'=>'ACTIVE']);
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_payments(): void {
        Payment::create(['student_id'=>$this->student->id,'amount'=>1000]);
        $this->getJson('/api/v1/payments')->assertStatus(200)->assertJsonStructure(['data']);
    }

    public function test_can_create_payment(): void {
        $this->postJson('/api/v1/payments',[
            'student_id'=>$this->student->id,'amount'=>5000,'payment_method'=>'CASH','payment_date'=>'2026-07-01'
        ])->assertStatus(201)->assertJsonPath('data.amount','5000.00');
    }

    public function test_finance_can_confirm_payment(): void {
        $p = Payment::create(['student_id'=>$this->student->id,'amount'=>1000]);
        $this->postJson("/api/v1/payments/{$p->id}/finance-confirm")
            ->assertStatus(200)->assertJsonPath('data.status','FINANCE_CONFIRMED');
    }

    public function test_academic_confirm_requires_finance_confirm_first(): void {
        $p = Payment::create(['student_id'=>$this->student->id,'amount'=>1000]);
        $this->postJson("/api/v1/payments/{$p->id}/academic-confirm")->assertStatus(422);
    }

    public function test_can_reject_payment(): void {
        $p = Payment::create(['student_id'=>$this->student->id,'amount'=>1000]);
        $this->postJson("/api/v1/payments/{$p->id}/reject",['note'=>'資料不符'])
            ->assertStatus(200)->assertJsonPath('data.status','REJECTED');
    }

    public function test_unauthenticated_cannot_access(): void {
        auth()->forgetGuards();
        $this->getJson('/api/v1/payments')->assertStatus(401);
    }
}
