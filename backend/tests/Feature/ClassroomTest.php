<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Classroom;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassroomTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(User $user): string
    {
        return $user->createToken('api-token')->plainTextToken;
    }

    public function test_index_returns_classroom_list(): void
    {
        Classroom::create([
            'classroom_name' => '101教室',
            'capacity' => 30,
            'status' => 'ACTIVE',
        ]);

        Classroom::create([
            'classroom_name' => '102教室',
            'capacity' => 25,
            'status' => 'INACTIVE',
        ]);

        $user = User::factory()->create();
        $response = $this->withToken($this->tokenFor($user))
            ->getJson('/api/v1/classrooms');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.classroom_name', '101教室');
    }

    public function test_index_filters_by_region_id(): void
    {
        $regionA = Region::factory()->create();
        $regionB = Region::factory()->create();

        Classroom::create([
            'region_id' => $regionA->id,
            'classroom_name' => 'A教室',
            'capacity' => 30,
            'status' => 'ACTIVE',
        ]);

        Classroom::create([
            'region_id' => $regionB->id,
            'classroom_name' => 'B教室',
            'capacity' => 20,
            'status' => 'ACTIVE',
        ]);

        $user = User::factory()->create();
        $response = $this->withToken($this->tokenFor($user))
            ->getJson("/api/v1/classrooms?region_id={$regionA->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.classroom_name', 'A教室')
            ->assertJsonPath('data.0.region_id', $regionA->id);
    }

    public function test_admin_can_create_classroom(): void
    {
        $region = Region::factory()->create();
        $admin = User::factory()->create(['role' => Role::Admin]);

        $response = $this->withToken($this->tokenFor($admin))
            ->postJson('/api/v1/classrooms', [
                'region_id' => $region->id,
                'classroom_name' => '101教室',
                'capacity' => 30,
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.classroom_name', '101教室')
            ->assertJsonPath('data.capacity', 30);

        $this->assertDatabaseHas('classrooms', [
            'classroom_name' => '101教室',
            'region_id' => $region->id,
            'capacity' => 30,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_staff_cannot_create_classroom(): void
    {
        $region = Region::factory()->create();
        $staff = User::factory()->create(['role' => Role::Staff]);

        $response = $this->withToken($this->tokenFor($staff))
            ->postJson('/api/v1/classrooms', [
                'region_id' => $region->id,
                'classroom_name' => '101教室',
                'capacity' => 30,
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_classroom(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $classroom = Classroom::create([
            'classroom_name' => '101教室',
            'capacity' => 30,
            'status' => 'ACTIVE',
        ]);

        $response = $this->withToken($this->tokenFor($admin))
            ->putJson("/api/v1/classrooms/{$classroom->id}", [
                'classroom_name' => '201教室',
                'capacity' => 40,
                'status' => 'INACTIVE',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.classroom_name', '201教室')
            ->assertJsonPath('data.capacity', 40)
            ->assertJsonPath('data.status', 'INACTIVE');

        $this->assertDatabaseHas('classrooms', [
            'id' => $classroom->id,
            'classroom_name' => '201教室',
            'capacity' => 40,
            'status' => 'INACTIVE',
        ]);
    }

    public function test_admin_can_delete_classroom(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $classroom = Classroom::create([
            'classroom_name' => '101教室',
            'capacity' => 30,
            'status' => 'ACTIVE',
        ]);

        $response = $this->withToken($this->tokenFor($admin))
            ->deleteJson("/api/v1/classrooms/{$classroom->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('classrooms', ['id' => $classroom->id]);
    }

    public function test_create_validates_capacity_min_1(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        $response = $this->withToken($this->tokenFor($admin))
            ->postJson('/api/v1/classrooms', [
                'classroom_name' => '101教室',
                'capacity' => 0,
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['capacity']);
    }
}
