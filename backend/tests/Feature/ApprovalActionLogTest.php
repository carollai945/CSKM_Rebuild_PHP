<?php
namespace Tests\Feature;

use App\Enums\Role;
use App\Models\ApprovalActionLog;
use App\Models\LeaveRequest;
use App\Models\Petition;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApprovalActionLogTest extends TestCase {
    use RefreshDatabase;

    private function makeAdmin(): User {
        $admin = User::factory()->create(['role' => Role::Admin]);
        Staff::factory()->create(['user_id' => $admin->id]);
        return $admin;
    }

    private function makeLeaveRequest(): LeaveRequest {
        $u = User::factory()->create(['role' => Role::Staff]);
        $s = Staff::factory()->create(['user_id' => $u->id]);
        return LeaveRequest::create(['staff_id' => $s->id, 'leave_type' => 'ANNUAL', 'start_at' => '2026-07-10 09:00:00', 'end_at' => '2026-07-10 18:00:00', 'status' => 'PENDING']);
    }

    private function makePetition(): Petition {
        $u = User::factory()->create(['role' => Role::Staff]);
        $s = Staff::factory()->create(['user_id' => $u->id]);
        return Petition::create(['staff_id' => $s->id, 'title' => 'test petition', 'status' => 'PENDING']);
    }

    public function test_approve_leave_request_creates_log(): void {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);
        $lr = $this->makeLeaveRequest();

        $this->postJson("/api/v1/approvals/leave-requests/{$lr->id}/approve")->assertStatus(200);

        $this->assertDatabaseHas('approval_action_logs', [
            'related_type' => 'leave_request',
            'related_id'   => $lr->id,
            'actor_id'     => $admin->id,
            'action'       => 'APPROVE',
        ]);
    }

    public function test_reject_leave_request_creates_log_with_comment(): void {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);
        $lr = $this->makeLeaveRequest();

        $this->postJson("/api/v1/approvals/leave-requests/{$lr->id}/reject", ['reject_reason' => '時間不符'])->assertStatus(200);

        $this->assertDatabaseHas('approval_action_logs', [
            'related_type' => 'leave_request',
            'related_id'   => $lr->id,
            'actor_id'     => $admin->id,
            'action'       => 'REJECT',
            'comment'      => '時間不符',
        ]);
    }

    public function test_approve_petition_creates_log(): void {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);
        $petition = $this->makePetition();

        $this->postJson("/api/v1/approvals/petitions/{$petition->id}/approve")->assertStatus(200);

        $this->assertDatabaseHas('approval_action_logs', [
            'related_type' => 'petition',
            'related_id'   => $petition->id,
            'actor_id'     => $admin->id,
            'action'       => 'APPROVE',
        ]);
    }

    public function test_reject_petition_creates_log_with_comment(): void {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);
        $petition = $this->makePetition();

        $this->postJson("/api/v1/approvals/petitions/{$petition->id}/reject", ['reject_reason' => '內容不完整'])->assertStatus(200);

        $this->assertDatabaseHas('approval_action_logs', [
            'related_type' => 'petition',
            'related_id'   => $petition->id,
            'actor_id'     => $admin->id,
            'action'       => 'REJECT',
            'comment'      => '內容不完整',
        ]);
    }

    public function test_submit_leave_request_creates_log(): void {
        $user = User::factory()->create(['role' => Role::Staff]);
        Staff::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/applications/leave-requests', [
            'leave_type' => 'ANNUAL',
            'start_at'   => '2026-07-10 09:00:00',
            'end_at'     => '2026-07-10 18:00:00',
        ])->assertStatus(201);

        $lrId = $response->json('data.id');
        $this->assertDatabaseHas('approval_action_logs', [
            'related_type' => 'leave_request',
            'related_id'   => $lrId,
            'actor_id'     => $user->id,
            'action'       => 'SUBMIT',
        ]);
    }

    public function test_cancel_leave_request_creates_log(): void {
        $user = User::factory()->create(['role' => Role::Staff]);
        Staff::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/applications/leave-requests', [
            'leave_type' => 'ANNUAL',
            'start_at'   => '2026-07-10 09:00:00',
            'end_at'     => '2026-07-10 18:00:00',
        ])->assertStatus(201);

        $lrId = $response->json('data.id');
        $this->deleteJson("/api/v1/applications/leave-requests/{$lrId}")->assertStatus(204);

        $this->assertDatabaseHas('approval_action_logs', [
            'related_type' => 'leave_request',
            'related_id'   => $lrId,
            'actor_id'     => $user->id,
            'action'       => 'CANCEL',
        ]);
    }

    public function test_log_count_after_approve(): void {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);
        $lr = $this->makeLeaveRequest();

        $this->postJson("/api/v1/approvals/leave-requests/{$lr->id}/approve");

        $this->assertSame(1, ApprovalActionLog::where('related_type', 'leave_request')->where('related_id', $lr->id)->count());
    }
}
