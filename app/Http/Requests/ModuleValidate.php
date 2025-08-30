<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'descripcion'           => 'required|string|unique:module,descripcion,'.$this->moduleId,
            'detalle'               => 'required|string',
            'icono'                 => 'required|string',
        ];
    }

    public function messages(): array {
        return [
            'descripcion.required'   => 'El campo descripción es obligatorio',
            'descripcion.string'     => 'El campo descripción debe ser texto',
            'descripcion.unique'     => 'El campo descripción ya existe',
            'detalle.required'       => 'El campo detalle es obligatorio',
            'detalle.string'         => 'El campo detalle debe ser texto',
            'icono.required'         => 'El campo icono es obligatorio',
        ];
    }
}
