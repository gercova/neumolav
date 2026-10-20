<?php

namespace Tests\Feature;

use App\Models\Occupation;
use App\Models\Specialty;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SpecialtiesHomeTest extends TestCase
{
    private function createAuthorizedUser(): User
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('especialidades', 'web');
        Permission::findOrCreate('especialidad_acceder', 'web');
        Permission::findOrCreate('especialidad_ver', 'web');
        Permission::findOrCreate('especialidad_crear', 'web');
        Permission::findOrCreate('especialidad_actualizar', 'web');
        Permission::findOrCreate('especialidad_borrar', 'web');

        $role->givePermissionTo([
            'especialidades',
            'especialidad_acceder',
            'especialidad_ver',
            'especialidad_crear',
            'especialidad_actualizar',
            'especialidad_borrar',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/specialties');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso_specialties', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/specialties');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_specialties_view(): void
    {
        $user = $this->createAuthorizedUser();

        $response = $this->actingAs($user)->get('/admin/specialties');
        $response->assertStatus(200);
        $response->assertSee('id="vue-specialties"', false);
        $response->assertSee('window.__SPECIALTIES_STATE__', false);
    }

    public function test_authorized_user_can_fetch_specialties_options(): void
    {
        $user = $this->createAuthorizedUser();
        $occ = Occupation::first() ?? Occupation::create(['descripcion' => 'Médico Asistencial', 'estado' => 1]);

        $response = $this->actingAs($user)->getJson('/admin/specialties/options');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'occupations' => [
                '*' => ['id', 'descripcion'],
            ],
        ]);
    }

    public function test_authorized_user_can_fetch_specialties_list_paginated(): void
    {
        $user = $this->createAuthorizedUser();
        $occ = Occupation::first() ?? Occupation::create(['descripcion' => 'Médico Auditor', 'estado' => 1]);

        $uniqueDesc = 'Especialidad_Test_'.rand(10000, 99999);
        $spec = Specialty::create([
            'id_ocupacion' => $occ->id,
            'descripcion' => $uniqueDesc,
            'detalle' => 'Detalle de prueba para listado',
            'estado' => 1,
        ]);

        $response = $this->actingAs($user)->getJson('/admin/specialties/list?pageSize=10&page=1');
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
            ],
        ]);
        $response->assertJsonFragment([
            'id' => $spec->id,
            'descripcion' => $uniqueDesc,
        ]);
    }

    public function test_authorized_user_can_search_specialties(): void
    {
        $user = $this->createAuthorizedUser();
        $occ = Occupation::first() ?? Occupation::create(['descripcion' => 'Neumólogo Principal', 'estado' => 1]);

        $uniqueDesc = 'Espec_Srch_'.rand(10000, 99999);
        Specialty::create([
            'id_ocupacion' => $occ->id,
            'descripcion' => $uniqueDesc,
            'detalle' => 'Detalle de prueba para búsqueda',
            'estado' => 1,
        ]);

        $response = $this->actingAs($user)->getJson('/admin/specialties/list?pageSize=10&search='.$uniqueDesc);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'descripcion' => $uniqueDesc,
        ]);
    }

    public function test_authorized_user_can_filter_specialties_by_occupation(): void
    {
        $user = $this->createAuthorizedUser();
        $occ = Occupation::create(['descripcion' => 'Cargo Especial '.rand(1000, 9999), 'estado' => 1]);

        $uniqueDesc = 'Espec_Filter_'.rand(10000, 99999);
        $spec = Specialty::create([
            'id_ocupacion' => $occ->id,
            'descripcion' => $uniqueDesc,
            'detalle' => 'Detalle filtro ocupacion',
            'estado' => 1,
        ]);

        $response = $this->actingAs($user)->getJson('/admin/specialties/list?pageSize=10&occupation='.$occ->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $spec->id,
            'id_ocupacion' => $occ->id,
        ]);
    }

    public function test_authorized_user_can_show_specialty(): void
    {
        $user = $this->createAuthorizedUser();
        $occ = Occupation::first() ?? Occupation::create(['descripcion' => 'Cargo Show', 'estado' => 1]);

        $spec = Specialty::create([
            'id_ocupacion' => $occ->id,
            'descripcion' => 'Espec_Show_'.rand(1000, 9999),
            'detalle' => 'Detalle de prueba para show',
            'estado' => 1,
        ]);

        $response = $this->actingAs($user)->getJson('/admin/specialties/'.$spec->id);
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $spec->id,
            'descripcion' => $spec->descripcion,
            'detalle' => $spec->detalle,
        ]);
    }

    public function test_authorized_user_can_store_new_specialty(): void
    {
        $user = $this->createAuthorizedUser();
        $occ = Occupation::first() ?? Occupation::create(['descripcion' => 'Cargo Store', 'estado' => 1]);

        $uniqueDesc = 'Nueva Especialidad '.rand(10000, 99999);
        $payload = [
            'id_ocupacion' => $occ->id,
            'descripcion' => $uniqueDesc,
            'detalle' => 'Detalle nueva especialidad',
        ];

        $response = $this->actingAs($user)->postJson('/admin/specialties/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('especialidades', [
            'descripcion' => $uniqueDesc,
            'id_ocupacion' => $occ->id,
        ]);
    }

    public function test_authorized_user_can_update_existing_specialty(): void
    {
        $user = $this->createAuthorizedUser();
        $occ = Occupation::first() ?? Occupation::create(['descripcion' => 'Cargo Update', 'estado' => 1]);

        $spec = Specialty::create([
            'id_ocupacion' => $occ->id,
            'descripcion' => 'Antes_Update_'.rand(1000, 9999),
            'detalle' => 'Detalle antes',
            'estado' => 1,
        ]);

        $updatedDesc = 'Despues_Update_'.rand(1000, 9999);
        $payload = [
            'id' => $spec->id,
            'id_ocupacion' => $occ->id,
            'descripcion' => $updatedDesc,
            'detalle' => 'Detalle actualizado',
        ];

        $response = $this->actingAs($user)->postJson('/admin/specialties/store', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('especialidades', [
            'id' => $spec->id,
            'descripcion' => $updatedDesc,
        ]);
    }

    public function test_store_validates_required_and_unique_fields(): void
    {
        $user = $this->createAuthorizedUser();

        // 1. Campos obligatorios
        $response = $this->actingAs($user)->postJson('/admin/specialties/store', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['id_ocupacion', 'descripcion', 'detalle']);

        // 2. Unicidad de descripción
        $occ = Occupation::first() ?? Occupation::create(['descripcion' => 'Cargo Unique', 'estado' => 1]);
        $existing = Specialty::create([
            'id_ocupacion' => $occ->id,
            'descripcion' => 'Espec_Unica_'.rand(1000, 9999),
            'detalle' => 'Detalle',
            'estado' => 1,
        ]);

        $dupResponse = $this->actingAs($user)->postJson('/admin/specialties/store', [
            'id_ocupacion' => $occ->id,
            'descripcion' => $existing->descripcion,
            'detalle' => 'Otro detalle',
        ]);
        $dupResponse->assertStatus(422);
        $dupResponse->assertJsonValidationErrors(['descripcion']);
    }

    public function test_authorized_user_can_delete_specialty(): void
    {
        $user = $this->createAuthorizedUser();
        $occ = Occupation::first() ?? Occupation::create(['descripcion' => 'Cargo Delete', 'estado' => 1]);

        $spec = Specialty::create([
            'id_ocupacion' => $occ->id,
            'descripcion' => 'Espec_Del_'.rand(1000, 9999),
            'detalle' => 'Detalle borrado',
            'estado' => 1,
        ]);

        $response = $this->actingAs($user)->deleteJson('/admin/specialties/'.$spec->id);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);

        $this->assertSoftDeleted('especialidades', [
            'id' => $spec->id,
        ]);
    }

    public function test_authorized_user_can_search_specialties_for_select2_limited_to_five(): void
    {
        $user = $this->createAuthorizedUser();
        $occ = Occupation::first() ?? Occupation::create(['descripcion' => 'Ocupacion Test', 'estado' => 1]);

        $prefix = 'Select2Test_'.rand(1000, 9999);
        for ($i = 1; $i <= 7; $i++) {
            Specialty::create([
                'id_ocupacion' => $occ->id,
                'descripcion' => "{$prefix}_{$i}",
                'detalle' => "Detalle {$i}",
                'estado' => 1,
            ]);
        }

        $response = $this->actingAs($user)->getJson("/admin/specialties/search?q={$prefix}");
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertCount(5, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('value', $data[0]);
        $this->assertArrayHasKey('label', $data[0]);
        $this->assertArrayHasKey('descripcion', $data[0]);
    }

    public function test_authorized_user_can_filter_specialties_by_specialty_id(): void
    {
        $user = $this->createAuthorizedUser();
        $occ = Occupation::first() ?? Occupation::create(['descripcion' => 'Ocupacion Test Filter', 'estado' => 1]);

        $specA = Specialty::create([
            'id_ocupacion' => $occ->id,
            'descripcion' => 'Espec_A_'.rand(1000, 9999),
            'detalle' => 'Detalle A',
            'estado' => 1,
        ]);

        $specB = Specialty::create([
            'id_ocupacion' => $occ->id,
            'descripcion' => 'Espec_B_'.rand(1000, 9999),
            'detalle' => 'Detalle B',
            'estado' => 1,
        ]);

        $response = $this->actingAs($user)->getJson("/admin/specialties/list?specialty_id={$specA->id}");
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $specA->id,
            'descripcion' => $specA->descripcion,
        ]);

        $response->assertJsonMissing([
            'descripcion' => $specB->descripcion,
        ]);
    }
}
