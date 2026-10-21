<?php

namespace Tests\Feature;

use App\Models\AppVersion;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ConfigVersionTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('seguridad', 'web');
        Permission::findOrCreate('configuracion', 'web');
        Permission::findOrCreate('configuracion_acceder', 'web');
        Permission::findOrCreate('configuracion_ver', 'web');
        Permission::findOrCreate('configuracion_crear', 'web');
        Permission::findOrCreate('configuracion_actualizar', 'web');
        Permission::findOrCreate('configuracion_borrar', 'web');

        $role->givePermissionTo([
            'seguridad',
            'configuracion',
            'configuracion_acceder',
            'configuracion_ver',
            'configuracion_crear',
            'configuracion_actualizar',
            'configuracion_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/config');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/config');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_config_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/config');
        $response->assertStatus(200);
        $response->assertViewIs('security.config.index');
        $response->assertSee('vue-config');
    }

    public function test_authorized_user_can_list_versions(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/config/list');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data',
            'total',
            'metrics' => ['total', 'major', 'minor', 'patch'],
            'permissions',
        ]);
        $this->assertTrue($response->json('status'));
    }

    public function test_authorized_user_can_detect_codebase_changes(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/config/detect');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'detection' => [
                'current_version',
                'next_version',
                'recommended_type',
                'rationale',
                'classes_changed',
                'methods_changed',
                'views_changed',
                'scripts_changed',
                'stats',
            ],
        ]);
        $this->assertTrue($response->json('status'));
    }

    public function test_authorized_user_can_generate_new_semantic_version(): void
    {
        $user = $this->createAuthorizedUser();

        $payload = [
            'version' => '3.9.99',
            'version_type' => 'MINOR',
            'summary' => 'Versión de prueba automatizada',
            'description' => 'Notas detalladas de la versión de prueba',
            'classes_changed' => ['App\Http\Controllers\security\ConfigController'],
            'methods_changed' => [
                [
                    'class' => 'App\Http\Controllers\security\ConfigController',
                    'method' => 'detect',
                    'visibility' => 'public',
                    'change_type' => 'added',
                    'is_breaking' => false,
                    'parameters' => [],
                ],
            ],
            'views_changed' => ['resources/views/security/config/index.blade.php'],
            'scripts_changed' => ['resources/js/modules/config.js'],
            'stats' => ['classes_count' => 1, 'methods_count' => 1, 'views_count' => 1],
        ];

        $response = $this->actingAs($user)->postJson('/admin/config/generate', $payload);
        $response->assertStatus(201);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('app_versions', [
            'version' => '3.9.99',
            'version_type' => 'MINOR',
            'summary' => 'Versión de prueba automatizada',
        ]);

        // Cleanup
        AppVersion::where('version', '3.9.99')->delete();
    }

    public function test_version_generation_validates_semver_format(): void
    {
        $user = $this->createAuthorizedUser();

        $payload = [
            'version' => 'invalido-sin-formato',
            'version_type' => 'PATCH',
            'summary' => 'Prueba con formato erróneo',
        ];

        $response = $this->actingAs($user)->postJson('/admin/config/generate', $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['version']);
    }

    public function test_authorized_user_can_view_version_detail(): void
    {
        $user = $this->createAuthorizedUser();

        $version = AppVersion::first();
        if (! $version) {
            $version = AppVersion::create([
                'version' => '3.3.0',
                'version_type' => 'PATCH',
                'summary' => 'Base version',
            ]);
        }

        $response = $this->actingAs($user)->get("/admin/config/{$version->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'version' => [
                'id',
                'version',
                'version_type',
                'summary',
                'classes_changed',
                'methods_changed',
                'views_changed',
                'scripts_changed',
            ],
        ]);
    }

    public function test_authorized_user_can_delete_version(): void
    {
        $user = $this->createAuthorizedUser();

        $version = AppVersion::create([
            'version' => '3.8.88',
            'version_type' => 'PATCH',
            'summary' => 'Versión temporal para eliminación',
        ]);

        $response = $this->actingAs($user)->deleteJson("/admin/config/{$version->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseMissing('app_versions', [
            'id' => $version->id,
        ]);
    }
}
