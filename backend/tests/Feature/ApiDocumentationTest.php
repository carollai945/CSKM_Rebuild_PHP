<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_api_docs_ui_is_available(): void
    {
        $this->get('/docs/api')
            ->assertOk()
            ->assertSeeText('CSKM Rebuild PHP API 文件');
    }

    public function test_api_docs_json_includes_core_v1_endpoints(): void
    {
        $response = $this->getJson('/docs/api.json');

        $response->assertOk()
            ->assertJsonPath('servers.0.url', url('/api/v1'));

        $paths = array_keys($response->json('paths', []));

        $this->assertContains('/auth/login', $paths);
        $this->assertContains('/applications/leave-requests', $paths);
        $this->assertContains('/applications/petitions', $paths);
    }
}
