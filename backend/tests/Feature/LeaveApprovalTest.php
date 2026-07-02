<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\LeaveRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class LeaveApprovalTest extends TestCase {
    use RefreshDatabase;
    private function makeLeaveRequest(): LeaveRequest {
        $u = User::factory()->create(['role'=>Role::Staff]);
        $s = Staff::factory()->create(['user_id'=>$u->id]);
        return LeaveRequest::create(['staff_id'=>$s->id,'leave_type'=>'ANNUAL','start_at'=>'2026-07-10 09:00:00','end_at'=>'2026-07-10 18:00:00']);
    }
    public function test_management_can_list_pending(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin]));
        $this->makeLeaveRequest();
        $this->getJson('/api/v1/approvals/leave-requests/pending')->assertStatus(200);
    }
    public function test_management_can_approve(): void {
        $admin = User::factory()->create(['role'=>Role::Admin]);
        Staff::factory()->create(['user_id'=>$admin->id]);
        Sanctum::actingAs($admin);
        $lr = $this->makeLeaveRequest();
        $this->postJson("/api/v1/approvals/leave-requests/{$lr->id}/approve")->assertStatus(200)->assertJsonPath('data.status','APPROVED');
    }
    public function test_management_can_reject(): void {
        $admin = User::factory()->create(['role'=>Role::Admin]);
        Staff::factory()->create(['user_id'=>$admin->id]);
        Sanctum::actingAs($admin);
        $lr = $this->makeLeaveRequest();
        $this->postJson("/api/v1/approvals/leave-requests/{$lr->id}/reject",['reject_reason'=>'時間不符'])->assertStatus(200)->assertJsonPath('data.status','REJECTED');
    }
    public function test_non_management_cannot_approve(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Staff]));
        $lr = $this->makeLeaveRequest();
        $this->postJson("/api/v1/approvals/leave-requests/{$lr->id}/approve")->assertStatus(403);
    }
    public function test_unauthenticated(): void { $this->getJson('/api/v1/approvals/leave-requests/pending')->assertStatus(401); }
}
