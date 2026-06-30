<?php

namespace Tests\Feature;

use App\Enums\Role;
use Tests\TestCase;

/**
 * Tests for Role Enum and RequireRole middleware.
 * These tests do NOT require a database connection.
 */
class RoleTest extends TestCase
{
    public function test_role_enum_has_all_required_roles(): void
    {
        $this->assertSame('CEO', Role::CEO->value);
        $this->assertSame('REG_MGR', Role::RegMgr->value);
        $this->assertSame('STAFF', Role::Staff->value);
        $this->assertSame('TEACHER', Role::Teacher->value);
        $this->assertSame('FINANCE', Role::Finance->value);
        $this->assertSame('ADMIN', Role::Admin->value);
    }

    public function test_role_enum_labels(): void
    {
        $this->assertSame('系統管理員', Role::CEO->label());
        $this->assertSame('區域主管', Role::RegMgr->label());
        $this->assertSame('學顧', Role::Staff->label());
        $this->assertSame('教務人員', Role::Teacher->label());
        $this->assertSame('財務人員', Role::Finance->label());
        $this->assertSame('管理部人員', Role::Admin->label());
    }

    public function test_master_data_writers_contains_ceo_and_admin(): void
    {
        $writers = Role::masterDataWriters();

        $this->assertContains(Role::CEO, $writers);
        $this->assertContains(Role::Admin, $writers);
        $this->assertNotContains(Role::Staff, $writers);
    }

    public function test_approvers_contains_ceo_regmgr_and_admin(): void
    {
        $approvers = Role::approvers();

        $this->assertContains(Role::CEO, $approvers);
        $this->assertContains(Role::RegMgr, $approvers);
        $this->assertContains(Role::Admin, $approvers);
        $this->assertNotContains(Role::Staff, $approvers);
    }

    public function test_unauthenticated_request_to_role_protected_route_returns_401(): void
    {
        // Register a test route protected by role middleware
        $this->app['router']->get('/test-role-ceo', fn() => 'ok')
            ->middleware(['auth:sanctum', 'role:CEO']);

        $response = $this->getJson('/test-role-ceo');

        $response->assertStatus(401);
    }
}
