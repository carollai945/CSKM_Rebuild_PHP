<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Course;
use App\Models\FeeItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FeeItemTest extends TestCase
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

    private function makeCourse(): Course
    {
        return Course::create(['course_name' => '測試課程', 'status' => 'ACTIVE']);
    }

    public function test_index_returns_fee_item_list(): void
    {
        Sanctum::actingAs($this->staffUser());
        $course = $this->makeCourse();
        FeeItem::create(['course_id' => $course->id, 'item_code' => 'FEE001', 'item_name' => '報名費', 'amount' => 1000]);
        FeeItem::create(['course_id' => $course->id, 'item_code' => 'FEE002', 'item_name' => '教材費', 'amount' => 500]);

        $response = $this->getJson('/api/v1/fee-items');

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_course_id(): void
    {
        Sanctum::actingAs($this->staffUser());
        $course1 = $this->makeCourse();
        $course2 = Course::create(['course_name' => '另一課程', 'status' => 'ACTIVE']);
        FeeItem::create(['course_id' => $course1->id, 'item_code' => 'FEE001', 'item_name' => '報名費', 'amount' => 1000]);
        FeeItem::create(['course_id' => $course2->id, 'item_code' => 'FEE002', 'item_name' => '教材費', 'amount' => 500]);

        $response = $this->getJson("/api/v1/fee-items?course_id={$course1->id}");

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_admin_can_create_fee_item(): void
    {
        Sanctum::actingAs($this->adminUser());
        $course = $this->makeCourse();

        $response = $this->postJson('/api/v1/fee-items', [
            'course_id' => $course->id,
            'item_code' => 'FEE001',
            'item_name' => '報名費',
            'amount'    => 3000,
            'currency'  => 'TWD',
        ]);

        $response->assertStatus(201)->assertJsonPath('data.item_name', '報名費');
        $this->assertDatabaseHas('fee_items', ['item_code' => 'FEE001']);
    }

    public function test_non_admin_cannot_create_fee_item(): void
    {
        Sanctum::actingAs($this->staffUser());
        $course = $this->makeCourse();

        $response = $this->postJson('/api/v1/fee-items', [
            'course_id' => $course->id,
            'item_code' => 'FEE001',
            'item_name' => '報名費',
            'amount'    => 3000,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_fee_item(): void
    {
        Sanctum::actingAs($this->adminUser());
        $course = $this->makeCourse();
        $item = FeeItem::create(['course_id' => $course->id, 'item_code' => 'FEE001', 'item_name' => '舊名', 'amount' => 1000]);

        $response = $this->putJson("/api/v1/fee-items/{$item->id}", ['item_name' => '新名稱', 'amount' => 1500]);

        $response->assertStatus(200)->assertJsonPath('data.item_name', '新名稱');
    }

    public function test_admin_can_delete_fee_item(): void
    {
        Sanctum::actingAs($this->adminUser());
        $course = $this->makeCourse();
        $item = FeeItem::create(['course_id' => $course->id, 'item_code' => 'FEE001', 'item_name' => '待刪', 'amount' => 1000]);

        $response = $this->deleteJson("/api/v1/fee-items/{$item->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('fee_items', ['id' => $item->id]);
    }
}
