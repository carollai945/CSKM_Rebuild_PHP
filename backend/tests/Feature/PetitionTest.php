<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Petition;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetitionTest extends TestCase {
    use RefreshDatabase;
    private User $user; private Staff $staff;
    protected function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create(['role'=>Role::Staff]);
        $this->staff = Staff::factory()->create(['user_id'=>$this->user->id]);
        Sanctum::actingAs($this->user);
    }
    public function test_can_list_petitions(): void { $this->getJson('/api/v1/applications/petitions')->assertStatus(200); }
    public function test_can_create_petition(): void {
        $this->postJson('/api/v1/applications/petitions',['title'=>'請求採購','content'=>'詳細說明'])
            ->assertStatus(201)->assertJsonPath('data.title','請求採購');
    }
    public function test_can_update_draft(): void {
        $p = Petition::create(['staff_id'=>$this->staff->id,'title'=>'old']);
        $this->putJson("/api/v1/applications/petitions/{$p->id}",['title'=>'new'])->assertStatus(200)->assertJsonPath('data.title','new');
    }
    public function test_can_delete_draft(): void {
        $p = Petition::create(['staff_id'=>$this->staff->id,'title'=>'test']);
        $this->deleteJson("/api/v1/applications/petitions/{$p->id}")->assertStatus(204);
    }
    public function test_can_submit_draft_petition(): void {
        $p = Petition::create(['staff_id'=>$this->staff->id,'title'=>'test']);
        $this->postJson("/api/v1/applications/petitions/{$p->id}/submit")
            ->assertStatus(200)->assertJsonPath('data.status','PENDING');
    }
    public function test_cannot_submit_pending_petition(): void {
        $p = Petition::create(['staff_id'=>$this->staff->id,'title'=>'test','status'=>'PENDING']);
        $this->postJson("/api/v1/applications/petitions/{$p->id}/submit")->assertStatus(422);
    }
    public function test_can_cancel_pending_petition(): void {
        $p = Petition::create(['staff_id'=>$this->staff->id,'title'=>'test','status'=>'PENDING']);
        $this->postJson("/api/v1/applications/petitions/{$p->id}/cancel")
            ->assertStatus(200)->assertJsonPath('data.status','CANCELLED');
    }
    public function test_other_user_cannot_cancel_petition(): void {
        $otherUser = User::factory()->create(['role'=>Role::Staff]);
        $otherStaff = Staff::factory()->create(['user_id'=>$otherUser->id]);
        $p = Petition::create(['staff_id'=>$otherStaff->id,'title'=>'test','status'=>'PENDING']);
        $this->postJson("/api/v1/applications/petitions/{$p->id}/cancel")->assertStatus(403);
    }
    public function test_unauthenticated(): void { auth()->forgetGuards(); $this->getJson('/api/v1/applications/petitions')->assertStatus(401); }
}
