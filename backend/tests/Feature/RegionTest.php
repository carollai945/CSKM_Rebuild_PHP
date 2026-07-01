<?php

namespace Tests\Feature;

use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegionTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function staffUser(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    // RG-INT-01: 查詢區域清單（無需驗證）
    public function test_index_returns_region_list(): void
    {
        Region::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/master/regions');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    // RG-INT-01: 管理員可新增區域
    public function test_admin_can_create_region(): void
    {
        Sanctum::actingAs($this->adminUser());

        $response = $this->postJson('/api/v1/master/regions', [
            'region_code' => 'R001',
            'region_name' => '北區',
            'status' => 'active',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.region_code', 'R001')
                 ->assertJsonPath('data.region_name', '北區');

        $this->assertDatabaseHas('regions', ['region_code' => 'R001']);
    }

    // RG-INT-02: 管理員可修改區域
    public function test_admin_can_update_region(): void
    {
        Sanctum::actingAs($this->adminUser());

        $region = Region::factory()->create([
            'region_code' => 'R002',
            'region_name' => '南區',
        ]);

        $response = $this->putJson("/api/v1/master/regions/{$region->id}", [
            'region_name' => '南區（更新）',
            'status' => 'inactive',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.region_name', '南區（更新）');

        $this->assertDatabaseHas('regions', [
            'id' => $region->id,
            'region_name' => '南區（更新）',
            'status' => 'inactive',
        ]);
    }

    // 管理員可刪除區域
    public function test_admin_can_delete_region(): void
    {
        Sanctum::actingAs($this->adminUser());

        $region = Region::factory()->create();

        $response = $this->deleteJson("/api/v1/master/regions/{$region->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('regions', ['id' => $region->id]);
    }

    // RG-UNIT-01: 區域代碼重複時不得儲存
    public function test_duplicate_region_code_is_rejected(): void
    {
        Sanctum::actingAs($this->adminUser());

        Region::factory()->create(['region_code' => 'R001']);

        $response = $this->postJson('/api/v1/master/regions', [
            'region_code' => 'R001',
            'region_name' => '另一區',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['region_code']);
    }

    // RG-UNIT-02: 缺少必填欄位時不得儲存
    public function test_missing_required_fields_are_rejected(): void
    {
        Sanctum::actingAs($this->adminUser());

        $response = $this->postJson('/api/v1/master/regions', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['region_code', 'region_name']);
    }

    // RG-INT-03: 非管理員不可新增區域
    public function test_non_admin_cannot_create_region(): void
    {
        Sanctum::actingAs($this->staffUser());

        $response = $this->postJson('/api/v1/master/regions', [
            'region_code' => 'R003',
            'region_name' => '西區',
        ]);

        $response->assertStatus(403);
    }

    // RG-INT-03: 非管理員不可修改區域
    public function test_non_admin_cannot_update_region(): void
    {
        Sanctum::actingAs($this->staffUser());

        $region = Region::factory()->create();

        $response = $this->putJson("/api/v1/master/regions/{$region->id}", [
            'region_name' => '修改名稱',
        ]);

        $response->assertStatus(403);
    }

    // RG-INT-03: 非管理員不可刪除區域
    public function test_non_admin_cannot_delete_region(): void
    {
        Sanctum::actingAs($this->staffUser());

        $region = Region::factory()->create();

        $response = $this->deleteJson("/api/v1/master/regions/{$region->id}");

        $response->assertStatus(403);
    }

    // 未驗證使用者不可新增區域
    public function test_unauthenticated_user_cannot_create_region(): void
    {
        $response = $this->postJson('/api/v1/master/regions', [
            'region_code' => 'R004',
            'region_name' => '東區',
        ]);

        $response->assertStatus(401);
    }

    // CEO 角色可新增區域
    public function test_ceo_can_create_region(): void
    {
        $ceo = User::factory()->create(['role' => 'ceo']);
        Sanctum::actingAs($ceo);

        $response = $this->postJson('/api/v1/master/regions', [
            'region_code' => 'RCEO',
            'region_name' => 'CEO區域',
        ]);

        $response->assertStatus(201);
    }
}
