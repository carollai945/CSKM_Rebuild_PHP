<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\InvoiceRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class InvoiceRequestTest extends TestCase {
    use RefreshDatabase;
    private User $user; private Staff $staff;
    protected function setUp(): void {
        parent::setUp();
        $this->user=User::factory()->create(['role'=>Role::Staff]);
        $this->staff=Staff::factory()->create(['user_id'=>$this->user->id]);
        Sanctum::actingAs($this->user);
    }
    public function test_can_list_invoice_requests(): void { $this->getJson('/api/v1/applications/invoice-requests')->assertStatus(200); }
    public function test_can_create(): void {
        $this->postJson('/api/v1/applications/invoice-requests',['title'=>'交通費','amount'=>500])
            ->assertStatus(201)->assertJsonPath('data.title','交通費');
    }
    public function test_can_update_pending(): void {
        $ir=InvoiceRequest::create(['staff_id'=>$this->staff->id,'title'=>'old','amount'=>100]);
        $this->putJson("/api/v1/applications/invoice-requests/{$ir->id}",['amount'=>200])->assertStatus(200)->assertJsonPath('data.amount','200.00');
    }
    public function test_can_cancel(): void {
        $ir=InvoiceRequest::create(['staff_id'=>$this->staff->id,'title'=>'test','amount'=>100]);
        $this->deleteJson("/api/v1/applications/invoice-requests/{$ir->id}")->assertStatus(204);
    }
    public function test_unauthenticated(): void { auth()->forgetGuards(); $this->getJson('/api/v1/applications/invoice-requests')->assertStatus(401); }
}
