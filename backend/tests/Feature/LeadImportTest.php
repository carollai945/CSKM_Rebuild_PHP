<?php
namespace Tests\Feature;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class LeadImportTest extends TestCase {
    use RefreshDatabase;
    public function test_management_can_import_leads_csv(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Admin]));
        $csv = "name,phone,source_code\n王小明,0912345678,CSV\n李小華,0987654321,CSV";
        $file = UploadedFile::fake()->createWithContent('leads.csv', $csv);
        $this->postJson('/api/v1/leads/import',['file'=>$file],['Content-Type'=>'multipart/form-data'])
            ->assertStatus(201)->assertJsonPath('imported',2);
    }
    public function test_non_management_cannot_import(): void {
        Sanctum::actingAs(User::factory()->create(['role'=>Role::Staff]));
        $file = UploadedFile::fake()->create('leads.csv', 100, 'text/csv');
        $this->postJson('/api/v1/leads/import',['file'=>$file])->assertStatus(403);
    }
    public function test_unauthenticated(): void { auth()->forgetGuards(); $this->postJson('/api/v1/leads/import')->assertStatus(401); }
}
