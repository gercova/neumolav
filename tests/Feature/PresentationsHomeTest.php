<?php

namespace Tests\Feature;

use App\Models\Presentation;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PresentationsHomeTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('presentaciones', 'web');
        Permission::findOrCreate('presentacion_acceder', 'web');
        Permission::findOrCreate('presentacion_ver', 'web');
        Permission::findOrCreate('presentacion_crear', 'web');
        Permission::findOrCreate('presentacion_actualizar', 'web');
        Permission::findOrCreate('presentacion_borrar', 'web');

        $role->givePermissionTo([
            'presentaciones',
            'presentacion_acceder',
            'presentacion_ver',
            'presentacion_crear',
            'presentacion_actualizar',
            'presentacion_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/presentations');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/presentations');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_presentations_home_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/presentations');
        $response->assertStatus(200);
        $response->assertSee('id="vue-presentations"', false);
        $response->assertSee('resources/js/modules/presentations.js', false);
    }

    public function test_authorized_user_can_fetch_presentations_list(): void
    {
        $user = $this->createAuthorizedUser();

        $presentation = Presentation::factory()->create([
            'descripcion' => 'Test Presentacion '.uniqid(),
            'aka' => 'TP'.rand(10, 99),
        ]);

        $response = $this->actingAs($user)->getJson('/admin/presentations/list?pageSize=10&page=1');
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
            'descripcion' => $presentation->descripcion,
            'aka' => $presentation->aka,
        ]);
    }

    public function test_authorized_user_can_search_presentations(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'UniqSearchPres_'.uniqid();
        Presentation::factory()->create([
            'descripcion' => $uniqueName,
            'aka' => 'USP',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/presentations/list?search='.$uniqueName);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'descripcion' => $uniqueName,
        ]);
    }

    public function test_authorized_user_can_show_presentation(): void
    {
        $user = $this->createAuthorizedUser();

        $presentation = Presentation::factory()->create([
            'descripcion' => 'Presentacion Show '.uniqid(),
            'aka' => 'PS',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/presentations/'.$presentation->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $presentation->id,
            'descripcion' => $presentation->descripcion,
            'aka' => $presentation->aka,
        ]);
    }

    public function test_authorized_user_can_store_new_presentation(): void
    {
        $user = $this->createAuthorizedUser();

        $payload = [
            'descripcion' => 'PresNueva_'.uniqid(),
            'aka' => 'PN'.rand(10, 99),
        ];

        $response = $this->actingAs($user)->postJson('/admin/presentations/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('droga_presentacion', [
            'descripcion' => $payload['descripcion'],
            'aka' => $payload['aka'],
        ]);
    }

    public function test_authorized_user_can_update_existing_presentation(): void
    {
        $user = $this->createAuthorizedUser();

        $presentation = Presentation::factory()->create([
            'descripcion' => 'PresEdit_'.uniqid(),
            'aka' => 'PE',
        ]);

        $payload = [
            'id' => $presentation->id,
            'descripcion' => 'PresEditModificada_'.uniqid(),
            'aka' => 'PEM',
        ];

        $response = $this->actingAs($user)->postJson('/admin/presentations/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('droga_presentacion', [
            'id' => $presentation->id,
            'descripcion' => $payload['descripcion'],
            'aka' => $payload['aka'],
        ]);
    }

    public function test_authorized_user_can_delete_presentation(): void
    {
        $user = $this->createAuthorizedUser();

        $presentation = Presentation::factory()->create([
            'descripcion' => 'PresDel_'.uniqid(),
            'aka' => 'PD',
        ]);

        $response = $this->actingAs($user)->deleteJson('/admin/presentations/delete/'.$presentation->id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertSoftDeleted('droga_presentacion', [
            'id' => $presentation->id,
        ]);
    }
}
