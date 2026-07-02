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
    public function test_can_update_pending(): void {
        $p = Petition::create(['staff_id'=>$this->staff->id,'title'=>'old']);
        $this->putJson("/api/v1/applications/petitions/{$p->id}",['title'=>'new'])->assertStatus(200)->assertJsonPath('data.title','new');
    }
    public function test_can_cancel_pending(): void {
        $p = Petition::create(['staff_id'=>$this->staff->id,'title'=>'test']);
        $this->deleteJson("/api/v1/applications/petitions/{$p->id}")->assertStatus(204);
    }
    public function test_unauthenticated(): void { auth()->forgetGuards(); $this->getJson('/api/v1/applications/petitions')->assertStatus(401); }
}
