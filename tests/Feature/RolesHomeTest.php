<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesHomeTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('seguridad', 'web');
        Permission::findOrCreate('roles', 'web');
        Permission::findOrCreate('rol_acceder', 'web');
        Permission::findOrCreate('rol_ver', 'web');
        Permission::findOrCreate('rol_crear', 'web');
        Permission::findOrCreate('rol_actualizar', 'web');
        Permission::findOrCreate('rol_borrar', 'web');

        $role->givePermissionTo([
            'seguridad',
            'roles',
            'rol_acceder',
            'rol_ver',
            'rol_crear',
            'rol_actualizar',
            'rol_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/roles');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/roles');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_roles_home_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/roles');
        $response->assertStatus(200);
        $response->assertSee('id="vue-roles"', false);
        $response->assertSee('window.__ROLES_STATE__', false);
    }

    public function test_authorized_user_can_fetch_roles_list_paginated(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'role_list_test_'.rand(1000, 9999);
        $role = Role::create([
            'name' => $uniqueName,
            'guard_name' => 'web',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/roles/list?pageSize=10&page=1');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data',
            'total',
            'permissions' => [
                'create',
                'update',
                'delete',
            ],
        ]);
        $response->assertJsonFragment([
            'name' => $role->name,
        ]);
    }

    public function test_authorized_user_can_search_roles(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'role_srch_'.rand(10000, 99999);
        Role::create([
            'name' => $uniqueName,
            'guard_name' => 'web',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/roles/list?search='.$uniqueName);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $uniqueName,
        ]);
    }

    public function test_authorized_user_can_store_new_role(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'role_new_'.rand(10000, 99999);
        $payload = [
            'name' => $uniqueName,
            'guard_name' => 'web',
        ];

        $response = $this->actingAs($user)->postJson('/admin/roles/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => $uniqueName,
            'guard_name' => 'web',
        ]);
    }

    public function test_authorized_user_can_update_existing_role(): void
    {
        $user = $this->createAuthorizedUser();

        $role = Role::create([
            'name' => 'role_before_update_'.rand(1000, 9999),
            'guard_name' => 'web',
        ]);

        $updatedName = 'role_after_update_'.rand(1000, 9999);
        $payload = [
            'id' => $role->id,
            'name' => $updatedName,
            'guard_name' => 'web',
        ];

        $response = $this->actingAs($user)->postJson('/admin/roles/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => $updatedName,
        ]);
    }

    public function test_store_validates_required_and_unique_name(): void
    {
        $user = $this->createAuthorizedUser();

        // 1. Validar requerido
        $response = $this->actingAs($user)->postJson('/admin/roles/store', [
            'name' => '',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);

        // 2. Validar único
        $existing = Role::create([
            'name' => 'role_unique_test_'.rand(1000, 9999),
            'guard_name' => 'web',
        ]);

        $duplicateResponse = $this->actingAs($user)->postJson('/admin/roles/store', [
            'name' => $existing->name,
            'guard_name' => 'web',
        ]);
        $duplicateResponse->assertStatus(422);
        $duplicateResponse->assertJsonValidationErrors(['name']);
    }

    public function test_authorized_user_can_show_role(): void
    {
        $user = $this->createAuthorizedUser();

        $role = Role::create([
            'name' => 'role_show_'.rand(1000, 9999),
            'guard_name' => 'web',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/roles/'.$role->id);
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $role->id,
            'name' => $role->name,
        ]);
    }

    public function test_authorized_user_can_get_role_permissions(): void
    {
        $user = $this->createAuthorizedUser();

        $role = Role::create([
            'name' => 'role_perms_test_'.rand(1000, 9999),
            'guard_name' => 'web',
        ]);

        $permission = Permission::findOrCreate('dummy_perm_test_'.rand(1000, 9999), 'web');
        $role->givePermissionTo($permission);

        $response = $this->actingAs($user)->getJson('/admin/roles/'.$role->id.'/permissions');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'role',
            'assigned',
            'available',
        ]);
    }

    public function test_authorized_user_can_sync_role_permissions(): void
    {
        $user = $this->createAuthorizedUser();

        $role = Role::create([
            'name' => 'role_sync_test_'.rand(1000, 9999),
            'guard_name' => 'web',
        ]);

        $perm1 = Permission::findOrCreate('sync_perm1_'.rand(1000, 9999), 'web');
        $perm2 = Permission::findOrCreate('sync_perm2_'.rand(1000, 9999), 'web');

        // Test array payload
        $response = $this->actingAs($user)->postJson('/admin/roles/'.$role->id.'/syncPermissions', [
            'permissions' => [$perm1->id, $perm2->id],
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertTrue($role->fresh()->hasPermissionTo($perm1->name));
        $this->assertTrue($role->fresh()->hasPermissionTo($perm2->name));
    }

    public function test_authorized_user_can_get_role_users(): void
    {
        $user = $this->createAuthorizedUser();

        $role = Role::create([
            'name' => 'role_users_test_'.rand(1000, 9999),
            'guard_name' => 'web',
        ]);

        $member = User::factory()->create();
        $member->assignRole($role);

        $response = $this->actingAs($user)->getJson('/admin/roles/'.$role->id.'/users');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'role',
            'users' => [
                '*' => ['id', 'name', 'email'],
            ],
        ]);
        $response->assertJsonFragment([
            'email' => $member->email,
        ]);
    }

    public function test_authorized_user_can_delete_role(): void
    {
        $user = $this->createAuthorizedUser();

        $role = Role::create([
            'name' => 'role_del_'.rand(1000, 9999),
            'guard_name' => 'web',
        ]);

        $response = $this->actingAs($user)->deleteJson('/admin/roles/'.$role->id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }
}
