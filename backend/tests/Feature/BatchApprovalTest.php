<?php
namespace Tests\Feature;

use App\Enums\Role;
use App\Models\LeaveRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BatchApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function makeLeaveRequest(): LeaveRequest
    {
        $u = User::factory()->create(['role' => Role::Staff]);
        $s = Staff::factory()->create(['user_id' => $u->id]);
        return LeaveRequest::create([
            'staff_id'   => $s->id,
            'leave_type' => 'ANNUAL',
            'start_at'   => '2026-07-10 09:00:00',
            'end_at'     => '2026-07-10 18:00:00',
            'status'     => 'PENDING',
        ]);
    }

    public function test_batch_approve_leave_requests(): void
    {
        $mgr = User::factory()->create(['role' => Role::RegMgr]);
        Staff::factory()->create(['user_id' => $mgr->id]);
        Sanctum::actingAs($mgr);

        $ids = [
            $this->makeLeaveRequest()->id,
            $this->makeLeaveRequest()->id,
            $this->makeLeaveRequest()->id,
        ];

        $response = $this->postJson('/api/v1/approvals/leave-requests/batch-approve', ['ids' => $ids]);
        $response->assertStatus(200)->assertJsonPath('data.approved_count', 3);
    }

    public function test_batch_reject_leave_requests(): void
    {
        $mgr = User::factory()->create(['role' => Role::RegMgr]);
        Staff::factory()->create(['user_id' => $mgr->id]);
        Sanctum::actingAs($mgr);

        $ids = [
            $this->makeLeaveRequest()->id,
            $this->makeLeaveRequest()->id,
        ];

        $response = $this->postJson('/api/v1/approvals/leave-requests/batch-reject', [
            'ids'           => $ids,
            'reject_reason' => '假期額滿',
        ]);
        $response->assertStatus(200)->assertJsonPath('data.rejected_count', 2);
    }

    public function test_staff_cannot_batch_approve(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => Role::Staff]));
        $response = $this->postJson('/api/v1/approvals/leave-requests/batch-approve', ['ids' => [1]]);
        $response->assertStatus(403);
    }
}
