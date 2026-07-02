<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class SystemBackupTest extends TestCase {
    use RefreshDatabase;
    public function test_admin_can_list_backups(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin]));
        $this->getJson('/api/v1/system/backup')->assertStatus(200)->assertJsonStructure(['data']);
    }
    public function test_admin_can_create_backup(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin]));
        $this->postJson('/api/v1/system/backup')->assertStatus(201)->assertJsonStructure(['message','filename']);
    }
    public function test_non_admin_cannot_access(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Staff]));
        $this->getJson('/api/v1/system/backup')->assertStatus(403);
    }
    public function test_unauthenticated(): void { auth()->forgetGuards(); $this->getJson('/api/v1/system/backup')->assertStatus(401); }
}
