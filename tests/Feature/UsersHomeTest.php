<?php

namespace Tests\Feature;

use App\Models\Specialty;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsersHomeTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('seguridad', 'web');
        Permission::findOrCreate('usuarios', 'web');
        Permission::findOrCreate('usuario_acceder', 'web');
        Permission::findOrCreate('usuario_ver', 'web');
        Permission::findOrCreate('usuario_crear', 'web');
        Permission::findOrCreate('usuario_actualizar', 'web');
        Permission::findOrCreate('usuario_borrar', 'web');

        $role->givePermissionTo([
            'seguridad',
            'usuarios',
            'usuario_acceder',
            'usuario_ver',
            'usuario_crear',
            'usuario_actualizar',
            'usuario_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/users/home');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso_users', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/users/home');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_users_home_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/users/home');
        $response->assertStatus(200);
        $response->assertSee('id="vue-users"', false);
        $response->assertSee('window.__USERS_STATE__', false);
    }

    public function test_authorized_user_can_fetch_users_list_paginated(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->getJson('/admin/users/list?pageSize=10&page=1');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data',
            'total',
            'currentPage',
            'lastPage',
            'pageSize',
            'permissions' => [
                'create',
                'update',
                'delete',
                'assign_roles',
            ],
        ]);
        $response->assertJsonFragment([
            'id' => $user->id,
            'name' => $user->name,
        ]);
    }

    public function test_authorized_user_can_search_users(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'User_Search_'.rand(10000, 99999);
        $newUser = User::factory()->create([
            'name' => $uniqueName,
            'email' => strtolower($uniqueName).'@example.com',
            'username' => strtolower($uniqueName),
        ]);

        $response = $this->actingAs($user)->getJson('/admin/users/list?pageSize=10&search='.$uniqueName);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $uniqueName,
            'id' => $newUser->id,
        ]);
    }

    public function test_authorized_user_can_filter_users_by_role(): void
    {
        $user = $this->createAuthorizedUser();

        $customRole = Role::findOrCreate('RolEspecial_'.rand(1000, 9999), 'web');
        $roleUser = User::factory()->create([
            'name' => 'User_With_Custom_Role_'.rand(1000, 9999),
        ]);
        $roleUser->assignRole($customRole);

        $response = $this->actingAs($user)->getJson('/admin/users/list?pageSize=10&role='.$customRole->name);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $roleUser->id,
            'role_name' => $customRole->name,
        ]);
    }

    public function test_authorized_user_can_show_user_details(): void
    {
        $user = $this->createAuthorizedUser();

        $targetUser = User::factory()->create([
            'name' => 'Target Show User',
            'biografia' => 'Médico especialista en neumología',
            'cmp' => '12345',
            'rne' => '67890',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/users/'.$targetUser->id);
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $targetUser->id,
            'name' => $targetUser->name,
            'cmp' => '12345',
            'rne' => '67890',
        ]);
    }

    public function test_user_cannot_delete_own_account(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->deleteJson('/admin/users/'.$user->id);
        $response->assertStatus(422);
        $response->assertJson([
            'status' => false,
            'message' => 'No puede eliminar su propia cuenta de usuario',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }

    public function test_authorized_user_can_delete_other_user(): void
    {
        $user = $this->createAuthorizedUser();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/admin/users/'.$targetUser->id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertSoftDeleted('users', [
            'id' => $targetUser->id,
        ]);
    }

    public function test_authorized_user_can_update_user_password(): void
    {
        $user = $this->createAuthorizedUser();
        $targetUser = User::factory()->create();

        $payload = [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($user)->postJson('/admin/users/storePassword/'.$targetUser->id, $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertTrue(Hash::check('newpassword123', $targetUser->fresh()->password));
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->postJson('/admin/users/store', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'role_id', 'specialty']);
    }

    public function test_authorized_user_can_store_new_user(): void
    {
        $user = $this->createAuthorizedUser();
        $role = Role::findOrCreate('medico_test_'.rand(100, 999), 'web');
        $specialty = Specialty::first() ?? Specialty::create(['descripcion' => 'Neumología']);

        $uniqueName = 'Dr. Juan Pérez '.rand(1000, 9999);
        $payload = [
            'name' => $uniqueName,
            'role_id' => $role->id,
            'specialty' => $specialty->id,
            'biografia' => 'Especialista clínico',
            'cmp' => '54321',
            'rne' => '98765',
        ];

        $response = $this->actingAs($user)->postJson('/admin/users/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $uniqueName,
            'cmp' => '54321',
            'rne' => '98765',
        ]);
    }
}
