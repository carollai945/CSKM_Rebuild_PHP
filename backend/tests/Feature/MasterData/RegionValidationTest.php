<?php

namespace Tests\Feature\MasterData;

use Tests\TestCase;

/**
 * Region API tests that do NOT require a database.
 */
class RegionValidationTest extends TestCase
{
    public function test_region_list_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/master/regions');

        $response->assertStatus(401);
    }

    public function test_region_create_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/master/regions', [
            'code' => 'TPE',
            'name' => '台北',
        ]);

        $response->assertStatus(401);
    }

    public function test_region_update_requires_authentication(): void
    {
        $response = $this->putJson('/api/v1/master/regions/1', [
            'name' => '台北市',
        ]);

        $response->assertStatus(401);
    }

    public function test_region_delete_requires_authentication(): void
    {
        $response = $this->deleteJson('/api/v1/master/regions/1');

        $response->assertStatus(401);
    }
}
