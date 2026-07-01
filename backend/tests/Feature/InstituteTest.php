<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Institute;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InstituteTest extends TestCase
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

    public function test_index_returns_institute_list(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Institute::create([
            'institute_name' => '台北校區',
            'status' => 'ACTIVE',
        ]);

        Institute::create([
            'institute_name' => '台中校區',
            'status' => 'INACTIVE',
        ]);

        $response = $this->getJson('/api/v1/institutes');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_region_id(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $regionA = Region::factory()->create();
        $regionB = Region::factory()->create();

        Institute::create([
            'region_id' => $regionA->id,
            'institute_name' => '北區校區',
            'status' => 'ACTIVE',
        ]);

        Institute::create([
            'region_id' => $regionB->id,
            'institute_name' => '南區校區',
            'status' => 'ACTIVE',
        ]);

        $response = $this->getJson("/api/v1/institutes?region_id={$regionA->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.institute_name', '北區校區')
            ->assertJsonPath('data.0.region_id', $regionA->id);
    }

    public function test_admin_can_create_institute(): void
    {
        Sanctum::actingAs($this->adminUser());
        $region = Region::factory()->create();

        $response = $this->postJson('/api/v1/institutes', [
            'region_id' => $region->id,
            'institute_name' => '高雄校區',
            'institute_code' => 'KH',
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.institute_name', '高雄校區')
            ->assertJsonPath('data.institute_code', 'KH');

        $this->assertDatabaseHas('institutes', [
            'region_id' => $region->id,
            'institute_name' => '高雄校區',
            'institute_code' => 'KH',
            'status' => 'ACTIVE',
        ]);
    }

    public function test_non_admin_cannot_create_institute(): void
    {
        Sanctum::actingAs($this->staffUser());

        $response = $this->postJson('/api/v1/institutes', [
            'institute_name' => '高雄校區',
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_institute(): void
    {
        Sanctum::actingAs($this->adminUser());

        $region = Region::factory()->create();
        $institute = Institute::create([
            'institute_name' => '原校區',
            'status' => 'ACTIVE',
        ]);

        $response = $this->putJson("/api/v1/institutes/{$institute->id}", [
            'region_id' => $region->id,
            'institute_name' => '新校區',
            'institute_code' => 'NEW',
            'status' => 'INACTIVE',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.institute_name', '新校區')
            ->assertJsonPath('data.status', 'INACTIVE');

        $this->assertDatabaseHas('institutes', [
            'id' => $institute->id,
            'region_id' => $region->id,
            'institute_name' => '新校區',
            'institute_code' => 'NEW',
            'status' => 'INACTIVE',
        ]);
    }

    public function test_admin_can_delete_institute(): void
    {
        Sanctum::actingAs($this->adminUser());

        $institute = Institute::create([
            'institute_name' => '待刪除校區',
            'status' => 'ACTIVE',
        ]);

        $response = $this->deleteJson("/api/v1/institutes/{$institute->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('institutes', ['id' => $institute->id]);
    }

    public function test_create_institute_requires_institute_name(): void
    {
        Sanctum::actingAs($this->adminUser());

        $response = $this->postJson('/api/v1/institutes', [
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['institute_name']);
    }
}
