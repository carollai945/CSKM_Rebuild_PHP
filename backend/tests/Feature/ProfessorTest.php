<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Professor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfessorTest extends TestCase
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

    public function test_index_returns_professor_list(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Professor::create(['name' => '王大明', 'status' => 'ACTIVE']);
        Professor::create(['name' => '李小美', 'status' => 'INACTIVE']);

        $response = $this->getJson('/api/v1/professors');

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_keyword(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Professor::create(['name' => '王大明', 'status' => 'ACTIVE']);
        Professor::create(['name' => '李小美', 'status' => 'ACTIVE']);

        $response = $this->getJson('/api/v1/professors?keyword=王');

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_admin_can_create_professor(): void
    {
        Sanctum::actingAs($this->adminUser());

        $response = $this->postJson('/api/v1/professors', [
            'name'    => '新師資',
            'gender'  => 'M',
            'email'   => 'prof@test.com',
            'status'  => 'ACTIVE',
        ]);

        $response->assertStatus(201)->assertJsonPath('data.name', '新師資');
        $this->assertDatabaseHas('professors', ['name' => '新師資']);
    }

    public function test_admin_can_create_professor_with_files(): void
    {
        Sanctum::actingAs($this->adminUser());

        $response = $this->postJson('/api/v1/professors', [
            'name'                 => '有附件師資',
            'document_file_names'  => ['cert.pdf', 'resume.pdf'],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('professor_files', ['file_name' => 'cert.pdf']);
        $this->assertDatabaseHas('professor_files', ['file_name' => 'resume.pdf']);
    }

    public function test_non_admin_cannot_create_professor(): void
    {
        Sanctum::actingAs($this->staffUser());

        $response = $this->postJson('/api/v1/professors', ['name' => '無權師資']);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_professor(): void
    {
        Sanctum::actingAs($this->adminUser());
        $professor = Professor::create(['name' => '舊名稱', 'status' => 'ACTIVE']);

        $response = $this->putJson("/api/v1/professors/{$professor->id}", [
            'name' => '新名稱',
        ]);

        $response->assertStatus(200)->assertJsonPath('data.name', '新名稱');
    }

    public function test_update_replaces_files_when_provided(): void
    {
        Sanctum::actingAs($this->adminUser());
        $professor = Professor::create(['name' => '師資A', 'status' => 'ACTIVE']);
        $professor->files()->create(['file_name' => 'old.pdf', 'file_path' => 'C:/old.pdf']);

        $this->putJson("/api/v1/professors/{$professor->id}", [
            'document_file_names' => ['new.pdf'],
        ]);

        $this->assertDatabaseMissing('professor_files', ['file_name' => 'old.pdf']);
        $this->assertDatabaseHas('professor_files', ['file_name' => 'new.pdf']);
    }

    public function test_admin_can_delete_professor(): void
    {
        Sanctum::actingAs($this->adminUser());
        $professor = Professor::create(['name' => '要刪除師資', 'status' => 'ACTIVE']);

        $response = $this->deleteJson("/api/v1/professors/{$professor->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('professors', ['id' => $professor->id]);
    }

    public function test_admin_can_delete_professor_file(): void
    {
        Sanctum::actingAs($this->adminUser());
        $professor = Professor::create(['name' => '師資B', 'status' => 'ACTIVE']);
        $file = $professor->files()->create(['file_name' => 'del.pdf', 'file_path' => 'C:/del.pdf']);

        $response = $this->deleteJson("/api/v1/professors/{$professor->id}/files/{$file->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('professor_files', ['id' => $file->id]);
    }
}
