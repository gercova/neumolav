<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Drug;
use App\Models\Presentation;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DrugsHomeTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('farmaco_acceder', 'web');
        Permission::findOrCreate('farmaco_ver', 'web');
        Permission::findOrCreate('farmaco_crear', 'web');
        Permission::findOrCreate('farmaco_actualizar', 'web');
        Permission::findOrCreate('farmaco_borrar', 'web');

        $role->givePermissionTo([
            'farmaco_acceder',
            'farmaco_ver',
            'farmaco_crear',
            'farmaco_actualizar',
            'farmaco_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/drugs');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/drugs');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_drugs_home_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/drugs');
        $response->assertStatus(200);
        $response->assertSee('id="vue-drugs"', false);
        $response->assertSee('resources/js/modules/drugs.js', false);
        $response->assertSee('window.__DRUGS_STATE__', false);
    }

    public function test_authorized_user_can_fetch_drugs_options(): void
    {
        $user = $this->createAuthorizedUser();

        $category = Category::factory()->create(['descripcion' => 'CatOptionsTest_'.uniqid()]);
        $presentation = Presentation::factory()->create(['descripcion' => 'PresOptionsTest_'.uniqid()]);

        $response = $this->actingAs($user)->getJson('/admin/drugs/options');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'categories',
            'presentations',
        ]);
        $response->assertJsonFragment([
            'id' => $category->id,
            'descripcion' => $category->descripcion,
        ]);
        $response->assertJsonFragment([
            'id' => $presentation->id,
            'descripcion' => $presentation->descripcion,
        ]);
    }

    public function test_authorized_user_can_fetch_drugs_list(): void
    {
        $user = $this->createAuthorizedUser();

        $drug = Drug::factory()->create([
            'descripcion' => 'TEST_DRUG_'.rand(1000, 9999),
            'detalle' => 'Detalle de prueba para fármaco',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/drugs/list?pageSize=10&page=1');
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
            'descripcion' => $drug->descripcion,
            'detalle' => $drug->detalle,
        ]);
    }

    public function test_authorized_user_can_search_drugs(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'UNIQ_DRUG_'.rand(10000, 99999);
        Drug::factory()->create([
            'descripcion' => $uniqueName,
            'detalle' => 'Detalle unico para buscar',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/drugs/list?search='.$uniqueName);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'descripcion' => $uniqueName,
        ]);
    }

    public function test_authorized_user_can_show_drug(): void
    {
        $user = $this->createAuthorizedUser();

        $drug = Drug::factory()->create([
            'descripcion' => 'DRUG_SHOW_'.rand(1000, 9999),
            'detalle' => 'Detalle show',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/drugs/'.$drug->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $drug->id,
            'descripcion' => $drug->descripcion,
        ]);
    }

    public function test_authorized_user_can_store_new_drug(): void
    {
        $user = $this->createAuthorizedUser();
        $category = Category::factory()->create();
        $presentation = Presentation::factory()->create();

        $payload = [
            'id_categoria' => $category->id,
            'id_presentacion' => $presentation->id,
            'descripcion' => 'FARMACO_NEW_'.rand(1000, 9999),
            'detalle' => 'Detalle nuevo farmaco',
        ];

        $response = $this->actingAs($user)->postJson('/admin/drugs/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('drogas', [
            'id_categoria' => $category->id,
            'id_presentacion' => $presentation->id,
            'descripcion' => $payload['descripcion'],
            'detalle' => strtoupper($payload['detalle']),
        ]);
    }

    public function test_authorized_user_can_update_existing_drug(): void
    {
        $user = $this->createAuthorizedUser();
        $drug = Drug::factory()->create();
        $newCategory = Category::factory()->create();

        $payload = [
            'id' => $drug->id,
            'id_categoria' => $newCategory->id,
            'id_presentacion' => $drug->id_presentacion,
            'descripcion' => 'FARMACO_UPD_'.rand(1000, 9999),
            'detalle' => 'Detalle actualizado',
        ];

        $response = $this->actingAs($user)->postJson('/admin/drugs/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('drogas', [
            'id' => $drug->id,
            'id_categoria' => $newCategory->id,
            'descripcion' => $payload['descripcion'],
            'detalle' => strtoupper($payload['detalle']),
        ]);
    }

    public function test_authorized_user_can_delete_drug(): void
    {
        $user = $this->createAuthorizedUser();
        $drug = Drug::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/admin/drugs/delete/'.$drug->id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertSoftDeleted('drogas', [
            'id' => $drug->id,
        ]);
    }
}
