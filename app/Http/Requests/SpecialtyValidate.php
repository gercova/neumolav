<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpecialtyValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'id_ocupacion'          => 'required',
            'descripcion'           => 'required|string|unique:especialidades,descripcion,'.$this->id ?? NULL,
            'detalle'               => 'required|string',
        ];
    }

    public function messages(): array {
        return [
            'id_ocupacion.required' => 'El campo Cargo es obligatorio.',
            'descripcion.required'  => 'El campo Descripción es obligatorio.',
            'descripcion.unique'    => 'El campo Descripción ya existe.',
            'detalle.required'      => 'El campo Detalle es obligatorio.',
            'detalle.string'        => 'El campo Detalle debe ser una cadena de texto.',
        ];
    }
}
