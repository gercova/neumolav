<?php

namespace Tests\Feature;

use App\Models\Diagnostic;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DiagnosticsHomeTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('diagnosticos', 'web');
        Permission::findOrCreate('diagnostico_acceder', 'web');
        Permission::findOrCreate('diagnostico_ver', 'web');
        Permission::findOrCreate('diagnostico_crear', 'web');
        Permission::findOrCreate('diagnostico_actualizar', 'web');
        Permission::findOrCreate('diagnostico_borrar', 'web');

        $role->givePermissionTo([
            'diagnosticos',
            'diagnostico_acceder',
            'diagnostico_ver',
            'diagnostico_crear',
            'diagnostico_actualizar',
            'diagnostico_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/diagnostics');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/diagnostics');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_diagnostics_home_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/diagnostics');
        $response->assertStatus(200);
        $response->assertSee('id="vue-diagnostics"', false);
        $response->assertSee('window.__DIAGNOSTICS_STATE__', false);
    }

    public function test_authorized_user_can_fetch_diagnostics_list(): void
    {
        $user = $this->createAuthorizedUser();

        $diagnostic = Diagnostic::factory()->create([
            'descripcion' => 'DX_LIST_TEST_'.rand(1000, 9999),
        ]);

        $response = $this->actingAs($user)->getJson('/admin/diagnostics/list?pageSize=10&page=1');
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
            'descripcion' => $diagnostic->descripcion,
        ]);
    }

    public function test_authorized_user_can_search_diagnostics(): void
    {
        $user = $this->createAuthorizedUser();

        $uniqueName = 'DX_SRCH_'.rand(10000, 99999);
        Diagnostic::factory()->create([
            'descripcion' => $uniqueName,
        ]);

        $response = $this->actingAs($user)->getJson('/admin/diagnostics/list?search='.$uniqueName);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'descripcion' => $uniqueName,
        ]);
    }

    public function test_authorized_user_can_show_diagnostic(): void
    {
        $user = $this->createAuthorizedUser();

        $diagnostic = Diagnostic::factory()->create([
            'descripcion' => 'DX_SHOW_'.rand(1000, 9999),
        ]);

        $response = $this->actingAs($user)->getJson('/admin/diagnostics/'.$diagnostic->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $diagnostic->id,
            'descripcion' => $diagnostic->descripcion,
        ]);
    }

    public function test_authorized_user_can_store_new_diagnostic(): void
    {
        $user = $this->createAuthorizedUser();

        $payload = [
            'descripcion' => 'DX_NEW_'.rand(1000, 9999),
        ];

        $response = $this->actingAs($user)->postJson('/admin/diagnostics/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('diagnosticos', [
            'descripcion' => $payload['descripcion'],
        ]);
    }

    public function test_authorized_user_can_update_existing_diagnostic(): void
    {
        $user = $this->createAuthorizedUser();
        $diagnostic = Diagnostic::factory()->create();

        $payload = [
            'id' => $diagnostic->id,
            'descripcion' => 'DX_UPD_'.rand(1000, 9999),
        ];

        $response = $this->actingAs($user)->postJson('/admin/diagnostics/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('diagnosticos', [
            'id' => $diagnostic->id,
            'descripcion' => $payload['descripcion'],
        ]);
    }

    public function test_authorized_user_can_delete_diagnostic(): void
    {
        $user = $this->createAuthorizedUser();
        $diagnostic = Diagnostic::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/admin/diagnostics/'.$diagnostic->id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertSoftDeleted('diagnosticos', [
            'id' => $diagnostic->id,
        ]);
    }

    public function test_authorized_user_can_search_and_autocomplete_diagnostics(): void
    {
        $user = $this->createAuthorizedUser();
        $uniqueName = 'DX_AUTO_'.rand(1000, 9999);
        Diagnostic::factory()->create(['descripcion' => $uniqueName]);

        $searchResponse = $this->actingAs($user)->postJson('/admin/diagnostics/search', ['q' => $uniqueName]);
        $searchResponse->assertStatus(200);
        $searchResponse->assertJsonFragment([
            'text' => strtoupper($uniqueName),
        ]);

        $autoResponse = $this->actingAs($user)->getJson('/admin/diagnostics/autocomplete?q='.$uniqueName);
        $autoResponse->assertStatus(200);
        $autoResponse->assertJsonFragment([$uniqueName]);
    }
}
