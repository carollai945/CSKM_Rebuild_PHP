<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StaffOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_get_staff_overview(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => Role::Admin]));
        Staff::factory()->create(['status' => 'ACTIVE']);
        Staff::factory()->create(['status' => 'ACTIVE']);

        $response = $this->getJson('/api/v1/staff/overview');

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_overview_filters_by_status(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => Role::Admin]));
        Staff::factory()->create(['status' => 'ACTIVE']);
        Staff::factory()->create(['status' => 'INACTIVE']);

        $response = $this->getJson('/api/v1/staff/overview?status=ACTIVE');

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_overview_filters_by_keyword(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => Role::Admin]));
        Staff::factory()->create(['name' => '王大明', 'status' => 'ACTIVE']);
        Staff::factory()->create(['name' => '李小美', 'status' => 'ACTIVE']);

        $response = $this->getJson('/api/v1/staff/overview?keyword=王');

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_unauthenticated_cannot_access_overview(): void
    {
        $response = $this->getJson('/api/v1/staff/overview');

        $response->assertStatus(401);
    }
}
