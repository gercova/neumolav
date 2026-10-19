<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnterpriseHomeTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador_empresa', 'web');
        Permission::findOrCreate('empresa', 'web');
        Permission::findOrCreate('empresa_acceder', 'web');
        Permission::findOrCreate('empresa_ver', 'web');
        Permission::findOrCreate('empresa_actualizar', 'web');

        $role->givePermissionTo([
            'empresa',
            'empresa_acceder',
            'empresa_ver',
            'empresa_actualizar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function ensureEnterpriseExists(): Enterprise
    {
        return Enterprise::first() ?? Enterprise::create([
            'razon_social' => 'Test SAC',
            'nombre_comercial' => 'Test',
            'ruc' => '20000000001',
            'email' => 'test@test.com',
            'pais' => 'Perú',
            'codigo_pais' => '+51',
            'telefono' => '999999999',
            'pagina_web' => 'test.com',
            'representante_legal' => 'Dr. Test',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/enterprise');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso_enterprise', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->ensureEnterpriseExists();

        $response = $this->actingAs($user)->get('/admin/enterprise');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_enterprise_view(): void
    {
        $user = $this->createAuthorizedUser();
        $this->ensureEnterpriseExists();

        $response = $this->actingAs($user)->get('/admin/enterprise');
        $response->assertStatus(200);
        $response->assertSee('id="vue-enterprise"', false);
        $response->assertSee('window.__ENTERPRISE_STATE__', false);
    }

    public function test_authorized_user_can_fetch_enterprise_data(): void
    {
        $user = $this->createAuthorizedUser();
        $enterprise = $this->ensureEnterpriseExists();

        $response = $this->actingAs($user)->getJson('/admin/enterprise/data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data',
            'images' => ['foto_representante', 'logo', 'logo_mini', 'logo_receta'],
            'permissions' => ['update'],
        ]);
        $response->assertJson(['status' => true]);
    }

    public function test_guest_cannot_fetch_enterprise_data(): void
    {
        $response = $this->getJson('/admin/enterprise/data');
        $response->assertStatus(401);
    }

    public function test_authorized_user_can_update_general_information(): void
    {
        $user = $this->createAuthorizedUser();
        $enterprise = $this->ensureEnterpriseExists();

        $payload = [
            'op' => 1,
            'id' => $enterprise->id,
            'razon_social' => 'Empresa Test Actualizada SAC',
            'nombre_comercial' => 'ETA',
            'ruc' => '20123456789',
            'email' => 'actualizado@test.com',
            'pais' => 'Perú',
            'codigo_pais' => '+51',
            'telefono' => '987654321',
            'slogan' => 'Cuidando tu salud',
            'mision' => 'Misión de prueba',
            'vision' => 'Visión de prueba',
        ];

        $response = $this->actingAs($user)->postJson('/admin/enterprise/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('empresa', [
            'id' => $enterprise->id,
            'razon_social' => 'Empresa Test Actualizada SAC',
            'email' => 'actualizado@test.com',
        ]);
    }

    public function test_authorized_user_can_upload_representative_photo(): void
    {
        Storage::fake('public');

        $user = $this->createAuthorizedUser();
        $enterprise = $this->ensureEnterpriseExists();

        $file = UploadedFile::fake()->image('representante.jpg', 200, 200);

        $response = $this->actingAs($user)->postJson('/admin/enterprise/store', [
            'op' => 2,
            'id' => $enterprise->id,
            'foto_representante' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);
        $response->assertJsonStructure(['image_url']);
    }

    public function test_authorized_user_can_upload_logo(): void
    {
        Storage::fake('public');

        $user = $this->createAuthorizedUser();
        $enterprise = $this->ensureEnterpriseExists();

        $file = UploadedFile::fake()->image('logo.png', 600, 200);

        $response = $this->actingAs($user)->postJson('/admin/enterprise/store', [
            'op' => 3,
            'id' => $enterprise->id,
            'logo' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);
        $response->assertJsonStructure(['image_url']);
    }

    public function test_authorized_user_can_upload_mini_logo(): void
    {
        Storage::fake('public');

        $user = $this->createAuthorizedUser();
        $enterprise = $this->ensureEnterpriseExists();

        $file = UploadedFile::fake()->image('minilogo.png', 64, 64);

        $response = $this->actingAs($user)->postJson('/admin/enterprise/store', [
            'op' => 4,
            'id' => $enterprise->id,
            'logo_mini' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);
        $response->assertJsonStructure(['image_url']);
    }

    public function test_authorized_user_can_upload_prescription_background(): void
    {
        Storage::fake('public');

        $user = $this->createAuthorizedUser();
        $enterprise = $this->ensureEnterpriseExists();

        $file = UploadedFile::fake()->image('receta.jpg', 794, 1123);

        $response = $this->actingAs($user)->postJson('/admin/enterprise/store', [
            'op' => 5,
            'id' => $enterprise->id,
            'logo_receta' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);
        $response->assertJsonStructure(['image_url']);
    }

    public function test_store_without_file_returns_422(): void
    {
        $user = $this->createAuthorizedUser();
        $enterprise = $this->ensureEnterpriseExists();

        // op=2 without any file
        $response = $this->actingAs($user)->postJson('/admin/enterprise/store', [
            'op' => 2,
            'id' => $enterprise->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => false]);
    }

    public function test_invalid_op_returns_400(): void
    {
        $user = $this->createAuthorizedUser();
        $enterprise = $this->ensureEnterpriseExists();

        $response = $this->actingAs($user)->postJson('/admin/enterprise/store', [
            'op' => 99,
            'id' => $enterprise->id,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['status' => false]);
    }

    public function test_user_without_update_permission_cannot_store(): void
    {
        $role = Role::findOrCreate('solo_ver_empresa', 'web');
        Permission::findOrCreate('empresa_acceder', 'web');
        $role->givePermissionTo(['empresa_acceder']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $enterprise = $this->ensureEnterpriseExists();

        $response = $this->actingAs($user)->postJson('/admin/enterprise/store', [
            'op' => 1,
            'id' => $enterprise->id,
            'razon_social' => 'Intento no autorizado',
        ]);

        $response->assertStatus(403);
    }
}
