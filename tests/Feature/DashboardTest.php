<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_without_permission_receives_forbidden(): void
    {
        $role = Role::findOrCreate('sin_permiso', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_dashboard_view(): void
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('dashboard', 'web');
        Permission::findOrCreate('reportes', 'web');
        $role->givePermissionTo(['dashboard', 'reportes']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('id="vue-statistics"', false);
    }

    public function test_authorized_user_can_fetch_annual_data(): void
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('dashboard', 'web');
        Permission::findOrCreate('reportes', 'web');
        $role->givePermissionTo(['dashboard', 'reportes']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->getJson('/admin/dashboard/histories/2026');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_authorized_user_can_fetch_monthly_detail(): void
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('dashboard', 'web');
        Permission::findOrCreate('reportes', 'web');
        $role->givePermissionTo(['dashboard', 'reportes']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->getJson('/admin/dashboard/monthlyDetail/2026/1');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'year',
            'month',
            'monthName',
            'daysInMonth',
            'categories',
            'series' => [
                'Historias' => ['name', 'data'],
                'Exámenes' => ['name', 'data'],
                'Citas' => ['name', 'data'],
            ],
            'allSeries',
        ]);
        $this->assertSame('Enero', $response->json('monthName'));
        $this->assertSame(31, $response->json('daysInMonth'));
        $this->assertCount(31, $response->json('categories'));
        $this->assertCount(31, $response->json('series.Historias.data'));
    }

    public function test_monthly_detail_rejects_invalid_month(): void
    {
        $role = Role::findOrCreate('administrador', 'web');
        Permission::findOrCreate('dashboard', 'web');
        Permission::findOrCreate('reportes', 'web');
        $role->givePermissionTo(['dashboard', 'reportes']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->getJson('/admin/dashboard/monthlyDetail/2026/13');
        $response->assertStatus(422);
    }
}
