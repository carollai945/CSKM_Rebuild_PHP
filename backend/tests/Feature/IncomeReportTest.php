<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class IncomeReportTest extends TestCase {
    use RefreshDatabase;
    private function financeUser(): User { return User::factory()->create(['role'=>Role::Finance]); }
    private function makeStudent(): Student { return Student::create(['student_no'=>'ST001','name'=>'test','status'=>'ACTIVE']); }
    public function test_finance_can_view_income_report(): void {
        Sanctum::actingAs($this->financeUser());
        $s=$this->makeStudent();
        Payment::create(['student_id'=>$s->id,'amount'=>1000,'status'=>'ACADEMIC_CONFIRMED','payment_date'=>'2026-07-01']);
        $this->getJson('/api/v1/reports/income')->assertStatus(200)->assertJsonStructure(['data']);
    }
    public function test_can_filter_by_date_range(): void {
        Sanctum::actingAs($this->financeUser());
        $this->getJson('/api/v1/reports/income?start_date=2026-07-01&end_date=2026-07-31')->assertStatus(200);
    }
    public function test_non_finance_cannot_access(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Staff]));
        $this->getJson('/api/v1/reports/income')->assertStatus(403);
    }
    public function test_unauthenticated(): void { auth()->forgetGuards(); $this->getJson('/api/v1/reports/income')->assertStatus(401); }
}
