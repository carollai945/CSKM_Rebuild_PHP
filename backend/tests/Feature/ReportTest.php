<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Report;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportTest extends TestCase {
    use RefreshDatabase;
    private User $user;
    private Staff $staff;

    protected function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create(['role' => Role::Staff]);
        $this->staff = Staff::factory()->create(['user_id' => $this->user->id]);
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_own_reports(): void {
        Report::create(['staff_id'=>$this->staff->id,'report_type'=>'DAILY','report_date'=>'2026-07-01','content'=>'test']);
        $this->getJson('/api/v1/reports')->assertStatus(200)->assertJsonStructure(['data']);
    }

    public function test_can_create_report(): void {
        $this->postJson('/api/v1/reports',[
            'report_type'=>'DAILY','report_date'=>'2026-07-01','content'=>'今日工作'
        ])->assertStatus(201)->assertJsonPath('data.report_type','DAILY');
    }

    public function test_can_update_draft_report(): void {
        $r = Report::create(['staff_id'=>$this->staff->id,'report_type'=>'DAILY','report_date'=>'2026-07-01']);
        $this->putJson("/api/v1/reports/{$r->id}",['content'=>'updated'])
            ->assertStatus(200)->assertJsonPath('data.content','updated');
    }

    public function test_can_submit_report(): void {
        $r = Report::create(['staff_id'=>$this->staff->id,'report_type'=>'DAILY','report_date'=>'2026-07-01']);
        $this->postJson("/api/v1/reports/{$r->id}/submit")
            ->assertStatus(200)->assertJsonPath('data.status','SUBMITTED');
    }

    public function test_cannot_update_submitted_report(): void {
        $r = Report::create(['staff_id'=>$this->staff->id,'report_type'=>'DAILY','report_date'=>'2026-07-01','status'=>'SUBMITTED']);
        $this->putJson("/api/v1/reports/{$r->id}",['content'=>'x'])->assertStatus(422);
    }

    public function test_unauthenticated_cannot_access(): void {
        auth()->forgetGuards();
        $this->getJson('/api/v1/reports')->assertStatus(401);
    }
}
