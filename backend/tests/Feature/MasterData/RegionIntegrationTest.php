<?php

namespace Tests\Feature\MasterData;

use App\Enums\Role;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @requires extension pdo_sqlite
 */
class RegionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => Role::Admin->value]);
    }

    private function staffUser(): User
    {
        return User::factory()->create(['role' => Role::Staff->value]);
    }

    public function test_admin_can_list_regions(): void
    {
        Region::create(['code' => 'TPE', 'name' => '台北', 'is_active' => true]);

        $response = $this->actingAs($this->adminUser())->getJson('/api/v1/master/regions');

        $response->assertStatus(200)
                 ->assertJsonFragment(['code' => 'TPE', 'name' => '台北']);
    }

    public function test_admin_can_create_region(): void
    {
        $response = $this->actingAs($this->adminUser())->postJson('/api/v1/master/regions', [
            'code' => 'KHH',
            'name' => '高雄',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['code' => 'KHH', 'name' => '高雄']);
    }

    public function test_staff_cannot_create_region(): void
    {
        $response = $this->actingAs($this->staffUser())->postJson('/api/v1/master/regions', [
            'code' => 'TPE',
            'name' => '台北',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_region(): void
    {
        $region = Region::create(['code' => 'TPE', 'name' => '台北', 'is_active' => true]);

        $response = $this->actingAs($this->adminUser())->putJson("/api/v1/master/regions/{$region->id}", [
            'name' => '台北市',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => '台北市']);
    }

    public function test_admin_can_delete_region(): void
    {
        $region = Region::create(['code' => 'TPE', 'name' => '台北', 'is_active' => true]);

        $response = $this->actingAs($this->adminUser())->deleteJson("/api/v1/master/regions/{$region->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('regions', ['id' => $region->id]);
    }
}
