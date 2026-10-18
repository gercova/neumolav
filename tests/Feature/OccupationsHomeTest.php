<?php

namespace Tests\Feature;

use App\Models\Occupation;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OccupationsHomeTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('ocupaciones', 'web');
        Permission::findOrCreate('ocupacion_acceder', 'web');
        Permission::findOrCreate('ocupacion_ver', 'web');
        Permission::findOrCreate('ocupacion_crear', 'web');
        Permission::findOrCreate('ocupacion_actualizar', 'web');
        Permission::findOrCreate('ocupacion_borrar', 'web');

        $role->givePermissionTo([
            'ocupaciones',
            'ocupacion_acceder',
            'ocupacion_ver',
            'ocupacion_crear',
            'ocupacion_actualizar',
            'ocupacion_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/occupations');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/occupations');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_occupations_home_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/occupations');
        $response->assertStatus(200);
        $response->assertSee('id="vue-occupations"', false);
        $response->assertSee('window.__OCCUPATIONS_STATE__', false);
    }

    public function test_authorized_user_can_fetch_occupations_list(): void
    {
        $user = $this->createAuthorizedUser();

        $occupation = Occupation::factory()->create([
            'descripcion' => 'OCC_LIST_TEST_'.rand(1000, 9999),
        ]);

        $response = $this->actingAs($user)->getJson('/admin/occupations/list?pageSize=10&page=1');
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
            'descripcion' => $occupation->descripcion,
        ]);
    }

    public function test_authorized_user_can_search_occupations(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'OCC_SRCH_'.rand(10000, 99999);
        Occupation::factory()->create([
            'descripcion' => $uniqueName,
        ]);

        $response = $this->actingAs($user)->getJson('/admin/occupations/list?search='.$uniqueName);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'descripcion' => $uniqueName,
        ]);
    }

    public function test_authorized_user_can_show_occupation(): void
    {
        $user = $this->createAuthorizedUser();

        $occupation = Occupation::factory()->create([
            'descripcion' => 'OCC_SHOW_'.rand(1000, 9999),
        ]);

        $response = $this->actingAs($user)->getJson('/admin/occupations/'.$occupation->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $occupation->id,
            'descripcion' => $occupation->descripcion,
        ]);
    }

    public function test_authorized_user_can_store_new_occupation(): void
    {
        $user = $this->createAuthorizedUser();

        $payload = [
            'descripcion' => 'OCC_NEW_'.rand(1000, 9999),
            'estado' => true,
        ];

        $response = $this->actingAs($user)->postJson('/admin/occupations/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);
        $response->assertJsonStructure([
            'status',
            'type',
            'messages',
            'item' => [
                'id',
                'descripcion',
                'ocupacion',
            ],
        ]);

        $this->assertDatabaseHas('ocupaciones', [
            'descripcion' => $payload['descripcion'],
        ]);
    }

    public function test_authorized_user_can_update_existing_occupation(): void
    {
        $user = $this->createAuthorizedUser();
        $occupation = Occupation::factory()->create();

        $payload = [
            'id' => $occupation->id,
            'descripcion' => 'OCC_UPD_'.rand(1000, 9999),
            'estado' => true,
        ];

        $response = $this->actingAs($user)->postJson('/admin/occupations/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('ocupaciones', [
            'id' => $occupation->id,
            'descripcion' => $payload['descripcion'],
        ]);
    }

    public function test_authorized_user_can_delete_occupation(): void
    {
        $user = $this->createAuthorizedUser();
        $occupation = Occupation::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/admin/occupations/'.$occupation->id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertSoftDeleted('ocupaciones', [
            'id' => $occupation->id,
        ]);
    }

    public function test_authorized_user_can_search_occupations_term(): void
    {
        $user = $this->createAuthorizedUser();
        $uniqueName = 'OCC_TRM_'.rand(1000, 9999);
        Occupation::factory()->create(['descripcion' => $uniqueName]);

        $searchResponse = $this->actingAs($user)->postJson('/admin/occupations/search', ['term' => $uniqueName]);
        $searchResponse->assertStatus(200);
        $searchResponse->assertJsonFragment([
            'descripcion' => $uniqueName,
        ]);
    }
}
