<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Petition;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class PetitionApprovalTest extends TestCase {
    use RefreshDatabase;
    private function makePetition(): Petition {
        $u=User::factory()->create(['role'=>Role::Staff]);
        $s=Staff::factory()->create(['user_id'=>$u->id]);
        return Petition::create(['staff_id'=>$s->id,'title'=>'test']);
    }
    public function test_management_can_list_pending(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin])); $this->makePetition();
        $this->getJson('/api/v1/approvals/petitions/pending')->assertStatus(200);
    }
    public function test_management_can_approve(): void {
        $admin=User::factory()->create(['role'=>Role::Admin]); Staff::factory()->create(['user_id'=>$admin->id]);
        Sanctum::actingAs($admin); $p=$this->makePetition();
        $this->postJson("/api/v1/approvals/petitions/{$p->id}/approve")->assertStatus(200)->assertJsonPath('data.status','APPROVED');
    }
    public function test_management_can_reject(): void {
        $admin=User::factory()->create(['role'=>Role::Admin]); Staff::factory()->create(['user_id'=>$admin->id]);
        Sanctum::actingAs($admin); $p=$this->makePetition();
        $this->postJson("/api/v1/approvals/petitions/{$p->id}/reject")->assertStatus(200)->assertJsonPath('data.status','REJECTED');
    }
    public function test_non_management_cannot(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Staff])); $p=$this->makePetition();
        $this->postJson("/api/v1/approvals/petitions/{$p->id}/approve")->assertStatus(403);
    }
    public function test_unauthenticated(): void { $this->getJson('/api/v1/approvals/petitions/pending')->assertStatus(401); }
}
