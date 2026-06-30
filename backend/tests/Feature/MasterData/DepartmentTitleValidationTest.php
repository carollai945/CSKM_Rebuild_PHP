<?php

namespace Tests\Feature\MasterData;

use Tests\TestCase;

class DepartmentTitleValidationTest extends TestCase
{
    public function test_department_list_requires_authentication(): void
    {
        $this->getJson('/api/v1/master/departments')->assertStatus(401);
    }

    public function test_department_create_requires_authentication(): void
    {
        $this->postJson('/api/v1/master/departments', ['code' => 'HR', 'name' => '人資部'])
             ->assertStatus(401);
    }

    public function test_title_list_requires_authentication(): void
    {
        $this->getJson('/api/v1/master/titles')->assertStatus(401);
    }

    public function test_title_create_requires_authentication(): void
    {
        $this->postJson('/api/v1/master/titles', ['code' => 'MGR', 'name' => '主管'])
             ->assertStatus(401);
    }
}
