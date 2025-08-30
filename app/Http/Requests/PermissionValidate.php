<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionValidate extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'name'          => 'required|unique:permissions,name,'.$this->id ?? NULL,
            'description'   => 'nullable|string',
            'guard_name'    => 'required|string',
        ];
    }

    public function messages(): array {
        return [
            'name.required'         => 'El campo Nombre es requerido',
            'name.unique'           => 'El campo Nombre ya existe',
            'description.string'    => 'El campo Descripción debe ser un string',
            'guard_name.required'   => 'El campo Nombre de Guardia es requerido',
            'guard_name.string'     => 'El campo Nombre de Guardia debe ser un string',
        ];    
    }
}
