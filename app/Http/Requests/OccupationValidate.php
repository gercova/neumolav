<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OccupationValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'descripcion'               => 'required|max:100|unique:ocupaciones,descripcion,'.$this->id ?? NULL,
        ];
    }

    public function messages(): array {
        return [
            'descripcion.required'      => 'El campo descripción es requerido.',
            'descripcion.max'           => 'El campo descripción no puede tener más de 100 caracteres.',
            'descripcion.unique'        => 'La descripción ya existe.',
        ];
    }
}
