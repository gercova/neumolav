<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleValidate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id ?: $this->roleId;

        return [
            'id' => 'nullable|integer',
            'roleId' => 'nullable|integer',
            'name' => 'required|string|max:125|unique:roles,name,'.($id ?? 'NULL').',id',
            'guard_name' => 'required|string|max:125',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El campo Nombre del rol es requerido',
            'name.unique' => 'El nombre del rol ya existe',
            'name.string' => 'El campo Nombre del rol debe ser un string',
            'guard_name.required' => 'El campo Nombre de Guardia es requerido',
            'guard_name.string' => 'El campo Nombre de Guardia debe ser un string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->id ?: $this->roleId;

        $this->merge([
            'id' => $id ? (int) $id : null,
            'roleId' => $id ? (int) $id : null,
            'name' => trim(strip_tags((string) $this->name)),
            'guard_name' => $this->guard_name ? trim(strip_tags((string) $this->guard_name)) : 'web',
        ]);
    }
}
