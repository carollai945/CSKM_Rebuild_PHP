<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\LeaveRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_approve_leave_requests(): void
    {
        $mgr = User::factory()->create(['role' => Role::RegMgr]);
        $staff = User::factory()->create(['role' => Role::Staff]);
        $staffRecord = Staff::factory()->create(['user_id' => $staff->id]);
        Staff::factory()->create(['user_id' => $mgr->id]);
        $leaves = LeaveRequest::factory()->count(3)->create(['staff_id' => $staffRecord->id, 'status' => 'PENDING']);

        $response = $this->actingAs($mgr)->postJson('/api/v1/approvals/leave-requests/batch-approve', [
            'ids' => $leaves->pluck('id')->toArray(),
        ]);
        $response->assertStatus(200)->assertJsonPath('data.approved_count', 3);
    }

    public function test_batch_reject_leave_requests(): void
    {
        $mgr = User::factory()->create(['role' => Role::RegMgr]);
        $staff = User::factory()->create(['role' => Role::Staff]);
        $staffRecord = Staff::factory()->create(['user_id' => $staff->id]);
        Staff::factory()->create(['user_id' => $mgr->id]);
        $leaves = LeaveRequest::factory()->count(2)->create(['staff_id' => $staffRecord->id, 'status' => 'PENDING']);

        $response = $this->actingAs($mgr)->postJson('/api/v1/approvals/leave-requests/batch-reject', [
            'ids' => $leaves->pluck('id')->toArray(),
            'reject_reason' => '假期額滿',
        ]);
        $response->assertStatus(200)->assertJsonPath('data.rejected_count', 2);
    }

    public function test_staff_cannot_batch_approve(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        $response = $this->actingAs($user)->postJson('/api/v1/approvals/leave-requests/batch-approve', ['ids' => [1]]);
        $response->assertStatus(403);
    }
}
