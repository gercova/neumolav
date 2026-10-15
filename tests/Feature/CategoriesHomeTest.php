<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoriesHomeTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('categorias', 'web');
        Permission::findOrCreate('categoria_acceder', 'web');
        Permission::findOrCreate('categoria_ver', 'web');
        Permission::findOrCreate('categoria_crear', 'web');
        Permission::findOrCreate('categoria_actualizar', 'web');
        Permission::findOrCreate('categoria_borrar', 'web');

        $role->givePermissionTo([
            'categorias',
            'categoria_acceder',
            'categoria_ver',
            'categoria_crear',
            'categoria_actualizar',
            'categoria_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_categories_home_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertStatus(200);
        $response->assertSee('id="vue-categories"', false);
        $response->assertSee('resources/js/modules/categories.js', false);
    }

    public function test_authorized_user_can_fetch_categories_list(): void
    {
        $user = $this->createAuthorizedUser();

        $category = Category::factory()->create([
            'descripcion' => 'Test Categoria '.uniqid(),
            'detalle' => 'Detalle de prueba para categoria',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/categories/list?pageSize=10&page=1');
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
            'descripcion' => $category->descripcion,
        ]);
    }

    public function test_authorized_user_can_search_categories(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'UniqSearchCat_'.uniqid();
        Category::factory()->create([
            'descripcion' => $uniqueName,
            'detalle' => 'Detalle especifico',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/categories/list?search='.$uniqueName);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'descripcion' => $uniqueName,
        ]);
    }

    public function test_authorized_user_can_show_category(): void
    {
        $user = $this->createAuthorizedUser();

        $category = Category::factory()->create([
            'descripcion' => 'Categoria Show '.uniqid(),
            'detalle' => 'Detalle show',
        ]);

        $response = $this->actingAs($user)->getJson('/admin/categories/'.$category->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $category->id,
            'descripcion' => $category->descripcion,
            'detalle' => $category->detalle,
        ]);
    }

    public function test_authorized_user_can_store_new_category(): void
    {
        $user = $this->createAuthorizedUser();

        $payload = [
            'descripcion' => 'CatNueva_'.uniqid(),
            'detalle' => 'Detalle de la nueva categoria',
        ];

        $response = $this->actingAs($user)->postJson('/admin/categories/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('droga_categoria', [
            'descripcion' => $payload['descripcion'],
            'detalle' => $payload['detalle'],
        ]);
    }

    public function test_authorized_user_can_update_existing_category(): void
    {
        $user = $this->createAuthorizedUser();

        $category = Category::factory()->create([
            'descripcion' => 'CatEdit_'.uniqid(),
            'detalle' => 'Detalle original',
        ]);

        $payload = [
            'id' => $category->id,
            'descripcion' => 'CatEditModificada_'.uniqid(),
            'detalle' => 'Detalle modificado',
        ];

        $response = $this->actingAs($user)->postJson('/admin/categories/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('droga_categoria', [
            'id' => $category->id,
            'descripcion' => $payload['descripcion'],
            'detalle' => $payload['detalle'],
        ]);
    }

    public function test_authorized_user_can_delete_category(): void
    {
        $user = $this->createAuthorizedUser();

        $category = Category::factory()->create([
            'descripcion' => 'CatDel_'.uniqid(),
            'detalle' => 'Detalle para borrar',
        ]);

        $response = $this->actingAs($user)->deleteJson('/admin/categories/delete/'.$category->id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertSoftDeleted('droga_categoria', [
            'id' => $category->id,
        ]);
    }
}
