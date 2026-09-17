<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class QuickPatientValidate extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'id_td'            => 'required|integer|exists:tipo_documento,id',
            'dni'              => 'required|string|unique:historias,dni',
            'nombres'          => 'required|string|max:150',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'id_sexo'          => 'required|string|in:M,F',
            'telefono'         => 'required|string|min:7|max:11',
            'fecha_cita'       => 'required|date',
            'hora_cita'        => 'nullable',
            'motivo'           => 'nullable|string|max:255',
            'id_tipo_atencion' => 'nullable|integer|exists:tipos_atencion,id',
        ];
    }
    
    public function messages(): array {
        return [
            'dni.required'                  => 'El DNI / documento es obligatorio.',
            'dni.unique'                    => 'El DNI ya se encuentra registrado en el sistema.',
            'nombres.required'              => 'Los nombres del paciente son obligatorios.',
            'fecha_nacimiento.required'     => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'id_sexo.required'              => 'El sexo es obligatorio.',
            'telefono.required'             => 'El teléfono/celular es obligatorio.',
            'fecha_cita.required'           => 'La fecha de la cita es obligatoria.',
        ];
    }
}
