<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\InterviewRecord;
use App\Models\Lead;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InterviewTest extends TestCase
{
    use RefreshDatabase;

    private function staffUser(): User
    {
        return User::factory()->create(['role' => Role::Staff]);
    }

    private function adminUser(): User
    {
        return User::factory()->create(['role' => Role::Admin]);
    }

    private function makeLead(int $createdBy): Lead
    {
        return Lead::create(['name' => '測試名單', 'status' => 'NEW', 'created_by' => $createdBy]);
    }

    public function test_index_returns_interview_list(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);
        $staff = Staff::factory()->create();
        $lead = $this->makeLead($user->id);

        InterviewRecord::create([
            'lead_id'        => $lead->id,
            'staff_id'       => $staff->id,
            'interview_date' => '2026-07-01',
            'result_code'    => 'INTERESTED',
        ]);

        $response = $this->getJson('/api/v1/interviews');

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_index_filters_by_lead_id(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);
        $staff = Staff::factory()->create();
        $lead1 = $this->makeLead($user->id);
        $lead2 = $this->makeLead($user->id);

        InterviewRecord::create(['lead_id' => $lead1->id, 'staff_id' => $staff->id, 'interview_date' => '2026-07-01', 'result_code' => 'INTERESTED']);
        InterviewRecord::create(['lead_id' => $lead2->id, 'staff_id' => $staff->id, 'interview_date' => '2026-07-01', 'result_code' => 'NOT_INTERESTED']);

        $response = $this->getJson("/api/v1/interviews?lead_id={$lead1->id}");

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_staff_can_create_interview(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);
        $staff = Staff::factory()->create();
        $lead = $this->makeLead($user->id);

        $response = $this->postJson('/api/v1/interviews', [
            'lead_id'           => $lead->id,
            'staff_id'          => $staff->id,
            'interview_date'    => '2026-07-01',
            'result_code'       => 'INTERESTED',
            'content'           => '有意願',
            'next_contact_date' => '2026-07-10',
        ]);

        $response->assertStatus(201)->assertJsonPath('data.result_code', 'INTERESTED');
        $this->assertDatabaseHas('interview_records', ['lead_id' => $lead->id, 'result_code' => 'INTERESTED']);
    }

    public function test_staff_can_update_interview(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);
        $staff = Staff::factory()->create();
        $lead = $this->makeLead($user->id);
        $record = InterviewRecord::create([
            'lead_id'        => $lead->id,
            'staff_id'       => $staff->id,
            'interview_date' => '2026-07-01',
            'result_code'    => 'INTERESTED',
        ]);

        $response = $this->putJson("/api/v1/interviews/{$record->id}", [
            'result_code' => 'CONVERTED',
        ]);

        $response->assertStatus(200)->assertJsonPath('data.result_code', 'CONVERTED');
    }

    public function test_staff_can_delete_interview(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);
        $staff = Staff::factory()->create();
        $lead = $this->makeLead($user->id);
        $record = InterviewRecord::create([
            'lead_id'        => $lead->id,
            'staff_id'       => $staff->id,
            'interview_date' => '2026-07-01',
            'result_code'    => 'INTERESTED',
        ]);

        $response = $this->deleteJson("/api/v1/interviews/{$record->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('interview_records', ['id' => $record->id]);
    }

    public function test_unauthenticated_cannot_access_interviews(): void
    {
        $response = $this->getJson('/api/v1/interviews');

        $response->assertStatus(401);
    }
}
