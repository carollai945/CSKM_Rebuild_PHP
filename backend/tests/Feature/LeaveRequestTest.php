<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\LeaveRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LeaveRequestTest extends TestCase {
    use RefreshDatabase;
    private User $user;
    private Staff $staff;

    protected function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create(['role' => Role::Staff]);
        $this->staff = Staff::factory()->create(['user_id' => $this->user->id]);
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_own_leave_requests(): void {
        LeaveRequest::create(['staff_id'=>$this->staff->id,'leave_type'=>'ANNUAL','start_at'=>'2026-07-10 09:00:00','end_at'=>'2026-07-10 18:00:00']);
        $this->getJson('/api/v1/applications/leave-requests')->assertStatus(200);
    }

    public function test_can_create_leave_request(): void {
        $this->postJson('/api/v1/applications/leave-requests',[
            'leave_type'=>'ANNUAL','start_at'=>'2026-07-10 09:00:00','end_at'=>'2026-07-10 18:00:00','reason'=>'休息'
        ])->assertStatus(201)->assertJsonPath('data.leave_type','ANNUAL');
    }

    public function test_can_update_pending_leave_request(): void {
        $lr = LeaveRequest::create(['staff_id'=>$this->staff->id,'leave_type'=>'ANNUAL','start_at'=>'2026-07-10 09:00:00','end_at'=>'2026-07-10 18:00:00']);
        $this->putJson("/api/v1/applications/leave-requests/{$lr->id}",['leave_type'=>'SICK'])
            ->assertStatus(200)->assertJsonPath('data.leave_type','SICK');
    }

    public function test_can_cancel_pending_leave_request(): void {
        $lr = LeaveRequest::create(['staff_id'=>$this->staff->id,'leave_type'=>'ANNUAL','start_at'=>'2026-07-10 09:00:00','end_at'=>'2026-07-10 18:00:00']);
        $this->deleteJson("/api/v1/applications/leave-requests/{$lr->id}")->assertStatus(204);
    }

    public function test_cannot_update_approved_leave_request(): void {
        $lr = LeaveRequest::create(['staff_id'=>$this->staff->id,'leave_type'=>'ANNUAL','start_at'=>'2026-07-10 09:00:00','end_at'=>'2026-07-10 18:00:00','status'=>'APPROVED']);
        $this->putJson("/api/v1/applications/leave-requests/{$lr->id}",['leave_type'=>'SICK'])->assertStatus(422);
    }

    public function test_unauthenticated_cannot_access(): void {
        auth()->forgetGuards();
        $this->getJson('/api/v1/applications/leave-requests')->assertStatus(401);
    }
}
