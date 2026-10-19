<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionsHomeTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('seguridad', 'web');
        Permission::findOrCreate('permisos', 'web');
        Permission::findOrCreate('permiso_acceder', 'web');
        Permission::findOrCreate('permiso_ver', 'web');
        Permission::findOrCreate('permiso_crear', 'web');
        Permission::findOrCreate('permiso_actualizar', 'web');
        Permission::findOrCreate('permiso_borrar', 'web');

        $role->givePermissionTo([
            'seguridad',
            'permisos',
            'permiso_acceder',
            'permiso_ver',
            'permiso_crear',
            'permiso_actualizar',
            'permiso_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/permissions');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/permissions');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_permissions_home_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/permissions');
        $response->assertStatus(200);
        $response->assertSee('id="vue-permissions"', false);
        $response->assertSee('window.__PERMISSIONS_STATE__', false);
    }

    public function test_authorized_user_can_fetch_permissions_list_paginated(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'perm_list_test_'.rand(1000, 9999);
        $permission = Permission::create([
            'name' => $uniqueName,
            'description' => 'Test Description for List',
            'guard_name' => 'web',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/permissions/list?pageSize=10&page=1');
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
            'name' => $permission->name,
        ]);
    }

    public function test_authorized_user_can_search_permissions(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'perm_srch_'.rand(10000, 99999);
        Permission::create([
            'name' => $uniqueName,
            'description' => 'Unique Searchable Desc',
            'guard_name' => 'web',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/permissions/list?search='.$uniqueName);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $uniqueName,
        ]);
    }

    public function test_authorized_user_can_store_new_permission(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'perm_new_'.rand(10000, 99999);
        $payload = [
            'name' => $uniqueName,
            'description' => 'Permiso de prueba nuevo',
            'guard_name' => 'web',
        ];

        $response = $this->actingAs($user)->postJson('/admin/permissions/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('permissions', [
            'name' => $uniqueName,
            'description' => 'Permiso de prueba nuevo',
            'guard_name' => 'web',
        ]);
    }

    public function test_authorized_user_can_update_existing_permission(): void
    {
        $user = $this->createAuthorizedUser();

        $permission = Permission::create([
            'name' => 'perm_before_update_'.rand(1000, 9999),
            'description' => 'Before update',
            'guard_name' => 'web',
        ]);

        $updatedName = 'perm_after_update_'.rand(1000, 9999);
        $payload = [
            'id' => $permission->id,
            'name' => $updatedName,
            'description' => 'Updated Description',
            'guard_name' => 'web',
        ];

        $response = $this->actingAs($user)->postJson('/admin/permissions/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => $updatedName,
            'description' => 'Updated Description',
        ]);
    }

    public function test_store_validates_required_and_unique_name(): void
    {
        $user = $this->createAuthorizedUser();

        // 1. Validar requerido
        $response = $this->actingAs($user)->postJson('/admin/permissions/store', [
            'name' => '',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);

        // 2. Validar único
        $existing = Permission::create([
            'name' => 'perm_unique_test_'.rand(1000, 9999),
            'guard_name' => 'web',
        ]);

        $duplicateResponse = $this->actingAs($user)->postJson('/admin/permissions/store', [
            'name' => $existing->name,
            'guard_name' => 'web',
        ]);
        $duplicateResponse->assertStatus(422);
        $duplicateResponse->assertJsonValidationErrors(['name']);
    }

    public function test_authorized_user_can_show_permission(): void
    {
        $user = $this->createAuthorizedUser();

        $permission = Permission::create([
            'name' => 'perm_show_'.rand(1000, 9999),
            'description' => 'Show test',
            'guard_name' => 'web',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/permissions/'.$permission->id);
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $permission->id,
            'name' => $permission->name,
        ]);
    }

    public function test_authorized_user_can_delete_permission(): void
    {
        $user = $this->createAuthorizedUser();

        $permission = Permission::create([
            'name' => 'perm_del_'.rand(1000, 9999),
            'guard_name' => 'web',
        ]);

        $response = $this->actingAs($user)->deleteJson('/admin/permissions/'.$permission->id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id,
        ]);
    }
}
