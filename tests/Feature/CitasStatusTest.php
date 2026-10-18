<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\History;
use App\Models\User;
use Tests\TestCase;

class CitasStatusTest extends TestCase
{
    private function getOrCreateTestCita(): Cita
    {
        $history = History::first();
        if (! $history) {
            $history = History::create([
                'dni' => '99'.rand(100000, 999999),
                'nombres' => 'PACIENTE TEST ESTADO',
                'id_estado' => 1,
                'id_ocupacion' => 1,
                'id_departamento' => 1,
                'id_provincia' => 1,
                'id_distrito' => 1,
                'id_sexo' => 1,
                'id_tipo_documento' => 1,
            ]);
        }

        return Cita::create([
            'id_historia' => $history->id,
            'id_tipo_atencion' => 1,
            'fecha_cita' => now()->toDateString(),
            'hora_cita' => '09:00:00',
            'id_estado' => 1, // PENDIENTE
        ]);
    }

    public function test_guest_cannot_update_appointment_status(): void
    {
        $response = $this->postJson('/admin/citas/1/status', [
            'id_estado' => 6,
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_update_status_to_en_espera(): void
    {
        $user = User::factory()->create();
        $cita = $this->getOrCreateTestCita();

        $response = $this->actingAs($user)->postJson("/admin/citas/{$cita->id}/status", [
            'id_estado' => 7, // EN ESPERA
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'id_estado' => 7,
        ]);
    }

    public function test_user_can_update_status_to_atendido(): void
    {
        $user = User::factory()->create();
        $cita = $this->getOrCreateTestCita();

        $response = $this->actingAs($user)->postJson("/admin/citas/{$cita->id}/status", [
            'id_estado' => 6, // ATENDIDO
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'id_estado' => 6,
        ]);
    }

    public function test_user_can_update_status_to_cancelado(): void
    {
        $user = User::factory()->create();
        $cita = $this->getOrCreateTestCita();

        $response = $this->actingAs($user)->postJson("/admin/citas/{$cita->id}/status", [
            'id_estado' => 3, // CANCELADO
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'id_estado' => 3,
        ]);
    }

    public function test_user_can_update_status_to_confirmado(): void
    {
        $user = User::factory()->create();
        $cita = $this->getOrCreateTestCita();

        $response = $this->actingAs($user)->postJson("/admin/citas/{$cita->id}/status", [
            'id_estado' => 2, // CONFIRMADO
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'type' => 'success',
        ]);

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'id_estado' => 2,
        ]);
    }

    public function test_invalid_status_returns_422(): void
    {
        $user = User::factory()->create();
        $cita = $this->getOrCreateTestCita();

        $response = $this->actingAs($user)->postJson("/admin/citas/{$cita->id}/status", [
            'id_estado' => 999, // Inexistente
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => false,
            'type' => 'error',
            'messages' => 'Estado no válido.',
        ]);
    }

    public function test_nonexistent_cita_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/admin/citas/9999999/status', [
            'id_estado' => 1,
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'status' => false,
            'type' => 'error',
            'messages' => 'Cita no encontrada.',
        ]);
    }
}
