<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessControlTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_access_admin_protected_route(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/ping');

        $response->assertStatus(200)
                 ->assertJson(['status' => 'ok']);
    }

    /** @test */
    public function non_admin_roles_receive_403_on_admin_route(): void
    {
        $nonAdminRoles = [
            Role::CEO,
            Role::RegMgr,
            Role::Staff,
            Role::Teacher,
            Role::Finance,
        ];

        foreach ($nonAdminRoles as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user, 'sanctum')->getJson('/api/admin/ping');

            $response->assertStatus(403);
        }
    }

    /** @test */
    public function unauthenticated_request_receives_401_on_protected_route(): void
    {
        $response = $this->getJson('/api/admin/ping');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_factory_defaults_to_staff_role(): void
    {
        $user = User::factory()->create();

        $this->assertSame(Role::Staff, $user->role);
    }

    /** @test */
    public function role_enum_covers_all_required_roles(): void
    {
        $expected = ['ceo', 'regmgr', 'staff', 'teacher', 'finance', 'admin'];

        $actual = array_column(Role::cases(), 'value');

        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    /** @test */
    public function gate_is_admin_allows_only_admin(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $staff = User::factory()->create(['role' => Role::Staff]);

        $this->assertTrue($admin->can('is-admin'));
        $this->assertFalse($staff->can('is-admin'));
    }

    /** @test */
    public function gate_management_allows_admin_ceo_regmgr(): void
    {
        $managementRoles = [Role::Admin, Role::CEO, Role::RegMgr];
        $nonManagementRoles = [Role::Staff, Role::Teacher, Role::Finance];

        foreach ($managementRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->assertTrue($user->can('management'), "Expected role [{$role->value}] to pass 'management' gate.");
        }

        foreach ($nonManagementRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->assertFalse($user->can('management'), "Expected role [{$role->value}] to fail 'management' gate.");
        }
    }
}
