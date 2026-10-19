<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicationsHomeTest extends TestCase
{
    use DatabaseTransactions;

    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador_posts', 'web');
        Permission::findOrCreate('posts', 'web');
        Permission::findOrCreate('post_acceder', 'web');
        Permission::findOrCreate('post_ver', 'web');
        Permission::findOrCreate('post_crear', 'web');
        Permission::findOrCreate('post_actualizar', 'web');
        Permission::findOrCreate('post_borrar', 'web');

        $role->givePermissionTo([
            'posts',
            'post_acceder',
            'post_ver',
            'post_crear',
            'post_actualizar',
            'post_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/publications');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso_posts', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/publications');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_publications_home_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/publications');
        $response->assertStatus(200);
        $response->assertSee('id="vue-publications"', false);
        $response->assertSee('__PUBLICATIONS_STATE__', false);
    }

    public function test_authorized_user_can_fetch_publications_options(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/publications/options');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'postTypes',
        ]);
    }

    public function test_authorized_user_can_fetch_publications_list_paginated(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->getJson('/admin/publications/list?page=1&pageSize=10');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data',
            'total',
            'currentPage',
            'lastPage',
            'pageSize',
            'permissions' => ['create', 'update', 'delete'],
        ]);
    }

    public function test_authorized_user_can_search_publications(): void
    {
        $user = $this->createAuthorizedUser();
        $type = PostType::first() ?? PostType::create(['description' => 'Noticia']);

        $post = Post::create([
            'type_id' => $type->id,
            'titulo' => 'Publicacion Unica de Prueba '.uniqid(),
            'titulo_corto' => 'Prueba Corta '.uniqid(),
            'url' => 'publicacion-unica-prueba-'.uniqid(),
            'img' => 'homepage/test.jpg',
            'descrip_img' => 'Descripcion de prueba',
            'alt_img' => 'alt-prueba',
            'resumen' => 'Resumen de prueba',
            'contenido' => '<p>Contenido completo de prueba</p>',
            'categories' => 'Neumología',
            'meta_content' => 'Meta descripción',
            'key_words' => 'salud, prueba',
            'etiquetas' => 'test, prueba',
            'autor_post' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson('/admin/publications/list?search='.urlencode($post->titulo));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $post->id,
            'titulo' => $post->titulo,
        ]);
    }

    public function test_authorized_user_can_show_publication(): void
    {
        $user = $this->createAuthorizedUser();
        $type = PostType::first() ?? PostType::create(['description' => 'Noticia']);

        $post = Post::create([
            'type_id' => $type->id,
            'titulo' => 'Publicacion Para Show '.uniqid(),
            'titulo_corto' => 'Show Corto '.uniqid(),
            'url' => 'publicacion-show-'.uniqid(),
            'img' => 'homepage/show.jpg',
            'descrip_img' => 'Descripcion show',
            'alt_img' => 'alt-show',
            'resumen' => 'Resumen show',
            'contenido' => '<p>Contenido show</p>',
            'categories' => 'Medicina',
            'meta_content' => 'Meta show',
            'key_words' => 'show',
            'etiquetas' => 'show',
            'autor_post' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson("/admin/publications/{$post->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $post->id,
            'titulo' => $post->titulo,
            'resumen' => $post->resumen,
            'contenido' => $post->contenido,
        ]);
    }

    public function test_authorized_user_can_store_new_publication_with_image(): void
    {
        Storage::fake('public');
        $user = $this->createAuthorizedUser();
        $type = PostType::first() ?? PostType::create(['description' => 'Noticia']);

        $image = UploadedFile::fake()->image('test_post.jpg', 600, 400);

        $payload = [
            'type_id' => $type->id,
            'titulo' => 'Nueva Publicacion Test '.uniqid(),
            'titulo_corto' => 'Nuevo Test '.uniqid(),
            'descrip_img' => 'Foto de prueba nueva',
            'resumen' => 'Resumen de nueva publicacion',
            'contenido' => '<p>Contenido completo nuevo</p>',
            'categories' => 'Investigación',
            'meta_content' => 'Meta de investigación',
            'key_words' => 'investigacion, prueba',
            'etiquetas' => 'tag1, tag2',
            'img' => $image,
        ];

        $response = $this->actingAs($user)->postJson('/admin/publications/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('post', [
            'titulo' => $payload['titulo'],
            'resumen' => $payload['resumen'],
        ]);
    }

    public function test_authorized_user_can_update_existing_publication(): void
    {
        $user = $this->createAuthorizedUser();
        $type = PostType::first() ?? PostType::create(['description' => 'Noticia']);

        $post = Post::create([
            'type_id' => $type->id,
            'titulo' => 'Publicacion Para Editar '.uniqid(),
            'titulo_corto' => 'Edit Corto '.uniqid(),
            'url' => 'publicacion-edit-'.uniqid(),
            'img' => 'homepage/initial.jpg',
            'descrip_img' => 'Descripcion inicial',
            'alt_img' => 'alt-inicial',
            'resumen' => 'Resumen inicial',
            'contenido' => '<p>Contenido inicial</p>',
            'categories' => 'Inicial',
            'meta_content' => 'Meta inicial',
            'key_words' => 'inicial',
            'etiquetas' => 'inicial',
            'autor_post' => $user->id,
        ]);

        $payload = [
            'postId' => $post->id,
            'type_id' => $type->id,
            'titulo' => 'Publicacion Editada con Exito '.uniqid(),
            'titulo_corto' => 'Editado '.uniqid(),
            'descrip_img' => 'Descripcion modificada',
            'resumen' => 'Resumen modificado',
            'contenido' => '<p>Contenido modificado</p>',
            'categories' => 'Actualizado',
            'meta_content' => 'Meta actualizado',
            'key_words' => 'actualizado',
            'etiquetas' => 'actualizado',
        ];

        $response = $this->actingAs($user)->postJson('/admin/publications/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('post', [
            'id' => $post->id,
            'titulo' => $payload['titulo'],
            'resumen' => 'Resumen modificado',
            'img' => 'homepage/initial.jpg',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->postJson('/admin/publications/store', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'titulo',
            'type_id',
            'img',
            'descrip_img',
            'resumen',
            'contenido',
            'categories',
            'meta_content',
            'key_words',
            'etiquetas',
        ]);
    }

    public function test_authorized_user_can_delete_publication(): void
    {
        $user = $this->createAuthorizedUser();
        $type = PostType::first() ?? PostType::create(['description' => 'Noticia']);

        $post = Post::create([
            'type_id' => $type->id,
            'titulo' => 'Publicacion Para Eliminar '.uniqid(),
            'titulo_corto' => 'Delete Corto '.uniqid(),
            'url' => 'publicacion-delete-'.uniqid(),
            'img' => 'homepage/delete.jpg',
            'descrip_img' => 'Descripcion delete',
            'alt_img' => 'alt-delete',
            'resumen' => 'Resumen delete',
            'contenido' => '<p>Contenido delete</p>',
            'categories' => 'Delete',
            'meta_content' => 'Meta delete',
            'key_words' => 'delete',
            'etiquetas' => 'delete',
            'autor_post' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/admin/publications/delete/{$post->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertSoftDeleted('post', [
            'id' => $post->id,
        ]);
    }

    public function test_legacy_routes_redirect_to_publications_index(): void
    {
        $user = $this->createAuthorizedUser();

        $responseAdd = $this->actingAs($user)->get('/admin/publications/add');
        $responseAdd->assertRedirect(route('business.posts'));

        $responseEdit = $this->actingAs($user)->get('/admin/publications/edit/1');
        $responseEdit->assertRedirect(route('business.posts'));
    }
}
