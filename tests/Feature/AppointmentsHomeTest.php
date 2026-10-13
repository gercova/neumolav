<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppointmentsHomeTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/appointments/home');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/appointments/home');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_appointments_home_view(): void
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('controles', 'web');
        Permission::findOrCreate('control_acceder', 'web');
        $role->givePermissionTo(['controles', 'control_acceder']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/appointments/home');
        $response->assertStatus(200);
        $response->assertSee('id="vue-appointments"', false);
    }

    public function test_authorized_user_can_fetch_list_histories(): void
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('controles', 'web');
        Permission::findOrCreate('control_acceder', 'web');
        $role->givePermissionTo(['controles', 'control_acceder']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->postJson('/admin/appointments/list-histories', [
            'jtStartIndex' => 0,
            'jtPageSize' => 10,
            'search' => null,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'Result',
            'Records',
            'TotalRecordCount',
        ]);
        $response->assertJson([
            'Result' => 'OK',
        ]);
    }
}
