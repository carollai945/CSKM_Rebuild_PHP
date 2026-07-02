<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\Announcement;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class AnnouncementTest extends TestCase {
    use RefreshDatabase;
    private User $user; private Staff $staff;
    protected function setUp(): void {
        parent::setUp();
        $this->user=User::factory()->create(['role'=>Role::Admin]);
        $this->staff=Staff::factory()->create(['user_id'=>$this->user->id]);
        Sanctum::actingAs($this->user);
    }
    public function test_can_list_announcements(): void { $this->getJson('/api/v1/applications/announcements')->assertStatus(200); }
    public function test_can_create(): void {
        $this->postJson('/api/v1/applications/announcements',['title'=>'重要通知','content'=>'請注意'])
            ->assertStatus(201)->assertJsonPath('data.title','重要通知');
    }
    public function test_can_update_draft(): void {
        $a=Announcement::create(['staff_id'=>$this->staff->id,'title'=>'old']);
        $this->putJson("/api/v1/applications/announcements/{$a->id}",['title'=>'new'])->assertStatus(200)->assertJsonPath('data.title','new');
    }
    public function test_cannot_update_published(): void {
        $a=Announcement::create(['staff_id'=>$this->staff->id,'title'=>'pub','status'=>'PUBLISHED']);
        $this->putJson("/api/v1/applications/announcements/{$a->id}",['title'=>'x'])->assertStatus(422);
    }
    public function test_can_delete_draft(): void {
        $a=Announcement::create(['staff_id'=>$this->staff->id,'title'=>'del']);
        $this->deleteJson("/api/v1/applications/announcements/{$a->id}")->assertStatus(204);
    }
    public function test_unauthenticated(): void { auth()->forgetGuards(); $this->getJson('/api/v1/applications/announcements')->assertStatus(401); }
}
