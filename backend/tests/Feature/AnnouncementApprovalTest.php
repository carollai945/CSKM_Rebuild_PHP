<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Announcement;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class AnnouncementApprovalTest extends TestCase {
    use RefreshDatabase;
    private function makeAnnouncement(): Announcement {
        $u=User::factory()->create(['role'=>Role::Staff]);
        $s=Staff::factory()->create(['user_id'=>$u->id]);
        return Announcement::create(['staff_id'=>$s->id,'title'=>'test','status'=>'PENDING_APPROVAL']);
    }
    public function test_management_can_list_pending(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin])); $this->makeAnnouncement();
        $this->getJson('/api/v1/approvals/announcements/pending')->assertStatus(200);
    }
    public function test_management_can_approve(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin])); $a=$this->makeAnnouncement();
        $this->postJson("/api/v1/approvals/announcements/{$a->id}/approve")->assertStatus(200)->assertJsonPath('data.status','PUBLISHED');
    }
    public function test_management_can_reject(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin])); $a=$this->makeAnnouncement();
        $this->postJson("/api/v1/approvals/announcements/{$a->id}/reject")->assertStatus(200)->assertJsonPath('data.status','DRAFT');
    }
    public function test_non_management_cannot(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Staff])); $a=$this->makeAnnouncement();
        $this->postJson("/api/v1/approvals/announcements/{$a->id}/approve")->assertStatus(403);
    }
    public function test_unauthenticated(): void { $this->getJson('/api/v1/approvals/announcements/pending')->assertStatus(401); }
}
