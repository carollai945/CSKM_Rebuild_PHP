<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Report;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class ReportApprovalTest extends TestCase {
    use RefreshDatabase;
    private function makeReport(): Report {
        $u=User::factory()->create(['role'=>Role::Staff]);
        $s=Staff::factory()->create(['user_id'=>$u->id]);
        return Report::create(['staff_id'=>$s->id,'report_type'=>'DAILY','report_date'=>'2026-07-01','status'=>'SUBMITTED']);
    }
    public function test_management_can_list_pending(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin])); $this->makeReport();
        $this->getJson('/api/v1/approvals/reports/pending')->assertStatus(200);
    }
    public function test_management_can_approve(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin])); $r=$this->makeReport();
        $this->postJson("/api/v1/approvals/reports/{$r->id}/approve")->assertStatus(200)->assertJsonPath('data.status','APPROVED');
    }
    public function test_management_can_reject(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin])); $r=$this->makeReport();
        $this->postJson("/api/v1/approvals/reports/{$r->id}/reject")->assertStatus(200)->assertJsonPath('data.status','REJECTED');
    }
    public function test_non_management_cannot(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Staff])); $r=$this->makeReport();
        $this->postJson("/api/v1/approvals/reports/{$r->id}/approve")->assertStatus(403);
    }
    public function test_unauthenticated(): void { $this->getJson('/api/v1/approvals/reports/pending')->assertStatus(401); }
}
