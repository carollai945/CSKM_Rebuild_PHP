<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PersonalDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_personal_data(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        $staff = Staff::factory()->create([
            'user_id' => $user->id,
            'join_date' => '2026-02-03',
            'registered_address' => [
                'postal_code' => '100',
                'city' => '台北市',
                'district' => '中正區',
                'address_line' => '仁愛路 1 號',
            ],
            'mailing_address' => [
                'postal_code' => '100',
                'city' => '台北市',
                'district' => '中正區',
                'address_line' => '仁愛路 1 號',
                'same_as_registered' => true,
            ],
            'language_abilities' => [
                'english' => '進階',
                'japanese' => '初級',
                'other_languages' => [
                    ['language' => '台語', 'speaking' => '流利', 'reading' => null, 'writing' => null, 'notes' => '母語'],
                ],
            ],
            'skills' => [
                'office_software' => ['Excel', 'PowerPoint'],
                'programming_languages' => ['PHP'],
                'professional_skills' => ['招生'],
                'self_evaluation' => '善於跨部門溝通',
            ],
            'certifications' => [
                'english' => [['name' => 'TOEIC', 'score' => '850', 'issued_by' => 'ETS', 'acquired_on' => '2025-01-01', 'notes' => null]],
                'japanese' => [],
                'professional' => [['name' => 'PMP', 'license_no' => 'PMP-001', 'issued_by' => 'PMI', 'acquired_on' => '2024-08-01', 'expires_on' => null, 'notes' => null]],
            ],
            'family_information' => [
                ['name' => '王小明', 'relationship' => '配偶', 'occupation' => '工程師', 'phone' => '0912000000', 'is_emergency_contact' => true],
            ],
            'work_experiences' => [
                ['company_name' => '中碩教育', 'position' => '顧問', 'start_date' => '2020-01-01', 'end_date' => '2022-12-31', 'responsibilities' => '課程規劃'],
            ],
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/me/personal-data');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $staff->id)
            ->assertJsonPath('data.join_date', '2026-02-03')
            ->assertJsonPath('data.currentStatus', 'EDITABLE')
            ->assertJsonPath('data.registered_address.city', '台北市')
            ->assertJsonPath('data.mailing_address.same_as_registered', true)
            ->assertJsonPath('data.language_abilities.english', '進階')
            ->assertJsonPath('data.skills.office_software.0', 'Excel')
            ->assertJsonPath('data.certifications.english.0.name', 'TOEIC')
            ->assertJsonPath('data.family_information.0.relationship', '配偶')
            ->assertJsonPath('data.work_experiences.0.company_name', '中碩教育')
            ->assertJsonPath('data.handbook_url', '/staff.pdf')
            ->assertJsonStructure(['data' => ['id', 'name', 'join_date', 'registered_address', 'language_abilities', 'skills', 'certifications', 'family_information', 'work_experiences', 'currentStatus', 'allowedActions']]);
    }

    public function test_unauthenticated_cannot_get_personal_data(): void
    {
        $this->getJson('/api/v1/me/personal-data')->assertStatus(401);
    }

    public function test_authenticated_user_can_update_personal_data(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        $staff = Staff::factory()->create(['user_id' => $user->id, 'join_date' => '2026-02-03']);
        Sanctum::actingAs($user);

        $payload = [
            'phone' => '0912345678',
            'gender' => 'M',
            'blood_type' => 'A',
            'registered_address' => [
                'postal_code' => '104',
                'city' => '台北市',
                'district' => '中山區',
                'address_line' => '松江路 10 號',
            ],
            'mailing_address' => [
                'postal_code' => '',
                'city' => '',
                'district' => '',
                'address_line' => '',
                'same_as_registered' => true,
            ],
            'language_abilities' => [
                'english' => '中級',
                'japanese' => '初級',
                'other_languages' => [
                    ['language' => '客語', 'speaking' => '普通', 'reading' => '', 'writing' => '', 'notes' => '可日常溝通'],
                ],
            ],
            'skills' => [
                'office_software' => ['Excel', ' PowerPoint '],
                'programming_languages' => ['PHP', ''],
                'professional_skills' => ['招生', '行政'],
                'self_evaluation' => '擅長流程優化',
            ],
            'certifications' => [
                'english' => [['name' => 'TOEIC', 'score' => '800', 'issued_by' => 'ETS', 'acquired_on' => '2025-03-03', 'notes' => 'recent']],
                'japanese' => [['name' => 'JLPT N2', 'score' => '', 'issued_by' => 'JLPT', 'acquired_on' => '2024-12-01', 'notes' => '']],
                'professional' => [['name' => 'PMP', 'license_no' => 'PMP-001', 'issued_by' => 'PMI', 'acquired_on' => '2024-01-01', 'expires_on' => '2027-01-01', 'notes' => '有效']],
            ],
            'family_information' => [
                ['name' => '王大華', 'relationship' => '父親', 'occupation' => '退休', 'phone' => '0223456789', 'is_emergency_contact' => true],
            ],
            'work_experiences' => [
                ['company_name' => '中碩教育', 'position' => '顧問', 'start_date' => '2021-01-01', 'end_date' => '2023-01-31', 'responsibilities' => '輔導招生與行政'],
            ],
        ];

        $response = $this->putJson('/api/v1/me/personal-data', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.join_date', '2026-02-03')
            ->assertJsonPath('data.currentStatus', 'EDITABLE')
            ->assertJsonPath('data.mailing_address.same_as_registered', true)
            ->assertJsonPath('data.mailing_address.city', '台北市')
            ->assertJsonPath('data.skills.programming_languages.0', 'PHP')
            ->assertJsonPath('data.certifications.professional.0.license_no', 'PMP-001');

        $fresh = $staff->fresh();
        $this->assertSame('0912345678', $fresh->phone);
        $this->assertSame('台北市', $fresh->registered_address['city']);
        $this->assertTrue($fresh->mailing_address['same_as_registered']);
        $this->assertSame('台北市', $fresh->mailing_address['city']);
        $this->assertSame('中級', $fresh->language_abilities['english']);
        $this->assertSame(['Excel', 'PowerPoint'], $fresh->skills['office_software']);
        $this->assertSame('PMP', $fresh->certifications['professional'][0]['name']);
        $this->assertSame('王大華', $fresh->family_information[0]['name']);
        $this->assertSame('中碩教育', $fresh->work_experiences[0]['company_name']);
    }

    public function test_update_validates_nested_personal_data_fields(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        Staff::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/me/personal-data', [
            'certifications' => [
                'english' => [
                    ['score' => '800'],
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['certifications.english.0.name']);
    }

    public function test_unauthenticated_cannot_update_personal_data(): void
    {
        $this->putJson('/api/v1/me/personal-data', ['phone' => '0912345678'])->assertStatus(401);
    }

    public function test_admin_can_view_staff_personal_data_readonly(): void
    {
        $adminUser = User::factory()->create(['role' => Role::Admin]);
        $targetUser = User::factory()->create(['role' => Role::Staff]);
        $target = Staff::factory()->create([
            'user_id' => $targetUser->id,
            'join_date' => '2026-02-03',
            'family_information' => [
                ['name' => '家人', 'relationship' => '母親', 'occupation' => null, 'phone' => null, 'is_emergency_contact' => false],
            ],
        ]);
        Sanctum::actingAs($adminUser);

        $response = $this->getJson("/api/v1/staff/{$target->id}/personal-data");

        $response->assertStatus(200)
            ->assertJsonPath('data.join_date', '2026-02-03')
            ->assertJsonPath('data.currentStatus', 'READONLY')
            ->assertJsonPath('data.allowedActions', [])
            ->assertJsonPath('data.handbook_url', '/staff.pdf')
            ->assertJsonPath('data.family_information.0.relationship', '母親');
    }

    public function test_non_management_cannot_view_other_staff_personal_data(): void
    {
        $user = User::factory()->create(['role' => Role::Staff]);
        $targetUser = User::factory()->create(['role' => Role::Staff]);
        $target = Staff::factory()->create(['user_id' => $targetUser->id]);
        Sanctum::actingAs($user);

        $this->getJson("/api/v1/staff/{$target->id}/personal-data")
            ->assertStatus(403);
    }
}
