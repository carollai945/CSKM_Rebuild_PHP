<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Lead;
use App\Models\Region;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => Role::Admin]);
    }

    private function staffUser(): User
    {
        return User::factory()->create(['role' => Role::Staff]);
    }

    public function test_index_returns_lead_list(): void
    {
        Sanctum::actingAs($this->staffUser());

        Lead::create(['name' => '張三', 'status' => 'NEW', 'created_by' => 1]);
        Lead::create(['name' => '李四', 'status' => 'CONTACTED', 'created_by' => 1]);

        $response = $this->getJson('/api/v1/leads');

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_status(): void
    {
        Sanctum::actingAs($this->staffUser());

        Lead::create(['name' => '新名單', 'status' => 'NEW', 'created_by' => 1]);
        Lead::create(['name' => '已聯絡', 'status' => 'CONTACTED', 'created_by' => 1]);

        $response = $this->getJson('/api/v1/leads?status=NEW');

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_index_filters_by_keyword(): void
    {
        Sanctum::actingAs($this->staffUser());

        Lead::create(['name' => '王大明', 'status' => 'NEW', 'created_by' => 1]);
        Lead::create(['name' => '李小美', 'status' => 'NEW', 'created_by' => 1]);

        $response = $this->getJson('/api/v1/leads?keyword=王');

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_staff_can_create_lead(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/leads', [
            'name'   => '新名單',
            'phone'  => '0912345678',
            'status' => 'NEW',
        ]);

        $response->assertStatus(201)->assertJsonPath('data.name', '新名單');
        $this->assertDatabaseHas('leads', ['name' => '新名單', 'created_by' => $user->id]);
    }

    public function test_staff_can_update_lead(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);
        $lead = Lead::create(['name' => '舊名稱', 'status' => 'NEW', 'created_by' => $user->id]);

        $response = $this->putJson("/api/v1/leads/{$lead->id}", ['name' => '新名稱']);

        $response->assertStatus(200)->assertJsonPath('data.name', '新名稱');
    }

    public function test_admin_can_delete_lead(): void
    {
        $admin = $this->adminUser();
        Sanctum::actingAs($admin);
        $lead = Lead::create(['name' => '待刪名單', 'status' => 'NEW', 'created_by' => $admin->id]);

        $response = $this->deleteJson("/api/v1/leads/{$lead->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    public function test_manager_can_bulk_assign_leads(): void
    {
        $admin = $this->adminUser();
        Sanctum::actingAs($admin);

        $staff = Staff::factory()->create();
        $lead1 = Lead::create(['name' => '名單1', 'status' => 'NEW', 'created_by' => $admin->id]);
        $lead2 = Lead::create(['name' => '名單2', 'status' => 'NEW', 'created_by' => $admin->id]);

        $response = $this->postJson('/api/v1/leads/assign', [
            'lead_ids'    => [$lead1->id, $lead2->id],
            'to_staff_id' => $staff->id,
        ]);

        $response->assertStatus(200)->assertJsonPath('data.assigned_count', 2);
        $this->assertDatabaseHas('leads', ['id' => $lead1->id, 'assigned_staff_id' => $staff->id]);
    }

    public function test_unauthenticated_cannot_access_leads(): void
    {
        $response = $this->getJson('/api/v1/leads');

        $response->assertStatus(401);
    }
}
