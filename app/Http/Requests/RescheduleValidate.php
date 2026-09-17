<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleValidate extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'fecha_cita' => 'required|date',
            'hora_cita'  => 'nullable',
            'motivo'     => 'nullable|string|max:255',
        ];
    }

    public function messages(): array {
        return [
            'fecha_cita.required' => 'La nueva fecha es requerida.',
            'fecha_cita.date'     => 'La nueva fecha no es válida.',
        ];
    }
}
