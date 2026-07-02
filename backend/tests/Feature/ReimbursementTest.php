<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Reimbursement;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class ReimbursementTest extends TestCase {
    use RefreshDatabase;
    private User $user; private Staff $staff;
    protected function setUp(): void {
        parent::setUp();
        $this->user=User::factory()->create(['role'=>Role::Staff]);
        $this->staff=Staff::factory()->create(['user_id'=>$this->user->id]);
        Sanctum::actingAs($this->user);
    }
    public function test_can_list_own_reimbursements(): void { $this->getJson('/api/v1/reimbursements')->assertStatus(200); }
    public function test_can_create(): void {
        $this->postJson('/api/v1/reimbursements',['title'=>'交通費','amount'=>300])
            ->assertStatus(201)->assertJsonPath('data.title','交通費');
    }
    public function test_finance_can_confirm(): void {
        $fu=User::factory()->create(['role'=>Role::Finance]); Staff::factory()->create(['user_id'=>$fu->id]);
        Sanctum::actingAs($fu);
        $r=Reimbursement::create(['staff_id'=>$this->staff->id,'title'=>'test','amount'=>100]);
        $this->postJson("/api/v1/reimbursements/{$r->id}/finance-confirm")->assertStatus(200)->assertJsonPath('data.status','FINANCE_CONFIRMED');
    }
    public function test_non_finance_cannot_confirm(): void {
        $r=Reimbursement::create(['staff_id'=>$this->staff->id,'title'=>'test','amount'=>100]);
        $this->postJson("/api/v1/reimbursements/{$r->id}/finance-confirm")->assertStatus(403);
    }
    public function test_unauthenticated(): void { auth()->forgetGuards(); $this->getJson('/api/v1/reimbursements')->assertStatus(401); }
}
