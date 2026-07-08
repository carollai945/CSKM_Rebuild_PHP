<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Jobs\ImportLeadsJob;
use App\Models\ImportJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LeadImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_management_can_queue_lead_import_job(): void
    {
        Queue::fake();
        Storage::fake('local');
        Sanctum::actingAs(User::factory()->create(['role' => Role::Admin]));

        $csv = "name,phone,source_code\n王小明,0912345678,CSV\n李小華,0987654321,CSV";
        $file = UploadedFile::fake()->createWithContent('leads.csv', $csv);

        $response = $this->postJson('/api/v1/leads/import', ['file' => $file], ['Content-Type' => 'multipart/form-data'])
            ->assertStatus(202)
            ->assertJsonStructure(['job_id']);

        $jobId = $response->json('job_id');

        $this->assertDatabaseHas('import_jobs', [
            'id' => $jobId,
            'status' => ImportJob::STATUS_PENDING,
            'total' => 0,
            'processed' => 0,
        ]);

        Queue::assertPushed(ImportLeadsJob::class);

        $this->getJson("/api/v1/system/import-jobs/{$jobId}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $jobId)
            ->assertJsonPath('data.status', ImportJob::STATUS_PENDING);
    }

    public function test_import_leads_job_creates_leads_and_updates_progress(): void
    {
        Storage::fake('local');

        $path = 'imports/leads.csv';
        Storage::disk('local')->put($path, "name,phone,source_code\n王小明,0912345678,CSV\n李小華,0987654321,CSV");

        $importJob = ImportJob::create([
            'status' => ImportJob::STATUS_PENDING,
            'total' => 0,
            'processed' => 0,
            'errors' => [],
        ]);

        (new ImportLeadsJob($importJob->id, $path))->handle();

        $this->assertDatabaseCount('leads', 2);
        $this->assertDatabaseHas('leads', [
            'name' => '王小明',
            'phone' => '0912345678',
            'source_code' => 'CSV',
            'import_job_id' => $importJob->id,
        ]);
        $this->assertDatabaseHas('leads', [
            'name' => '李小華',
            'phone' => '0987654321',
            'source_code' => 'CSV',
            'import_job_id' => $importJob->id,
        ]);

        $importJob->refresh();

        $this->assertSame(ImportJob::STATUS_DONE, $importJob->status);
        $this->assertSame(2, $importJob->total);
        $this->assertSame(2, $importJob->processed);
        $this->assertSame([], $importJob->errors);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_non_management_cannot_import(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => Role::Staff]));
        $file = UploadedFile::fake()->create('leads.csv', 100, 'text/csv');

        $this->postJson('/api/v1/leads/import', ['file' => $file])->assertStatus(403);
    }

    public function test_unauthenticated(): void
    {
        auth()->forgetGuards();

        $this->postJson('/api/v1/leads/import')->assertStatus(401);
    }
}
