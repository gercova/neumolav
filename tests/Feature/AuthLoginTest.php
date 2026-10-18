<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Enterprise::firstOrCreate(
            ['id' => 1],
            [
                'nombre_comercial' => 'Neumotar Test Clinic',
                'razon_social' => 'Neumotar SAC',
                'ruc' => '20123456789',
                'email' => 'contacto@neumotar.test',
                'descripcion' => 'Especialistas en enfermedades respiratorias',
            ]
        );
    }

    public function test_guest_can_access_login_page_with_spa_container(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertSee('id="vue-login"', false);
        $response->assertSee('window.__LOGIN_STATE__', false);
    }

    public function test_authenticated_user_is_redirected_from_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(\App\Providers\RouteServiceProvider::HOME);
    }

    public function test_login_validation_requires_email_and_password(): void
    {
        $response = $this->postJson('/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_validation_rejects_invalid_email_format(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'not-an-email',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_validation_rejects_short_password(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'valid@neumotar.com',
            'password' => '123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_login_fails_for_non_existent_user(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'nonexistent_'.uniqid().'@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => false,
            'message' => 'Credenciales inválidas.',
        ]);
        $this->assertFalse(Auth::check());
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'doctor_'.uniqid().'@example.com',
            'password' => Hash::make('correct_password'),
        ]);

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'wrong_password_123',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => false,
            'message' => 'Credenciales inválidas.',
        ]);
        $this->assertFalse(Auth::check());
    }

    public function test_successful_login_returns_expected_json_and_authenticates(): void
    {
        $password = 'SecretPass123!';
        $user = User::factory()->create([
            'name' => 'Dr. Martin Test',
            'email' => 'doctor_test_'.uniqid().'@example.com',
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => $password,
            'remember' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'Inicio de sesión exitoso.',
            'redirect' => route('home'),
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ]);
        $response->assertJsonStructure([
            'status',
            'message',
            'redirect',
            'permissions',
            'csrf_token',
            'token',
            'user' => ['id', 'name', 'email', 'username'],
        ]);

        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());
        $this->assertDatabaseHas('user_last_login', [
            'id_user' => $user->id,
        ]);
    }

    public function test_logout_invalidates_session_and_returns_json(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'redirect' => route('login'),
        ]);
        $this->assertFalse(Auth::check());
    }
}
