<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionValidate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer',
            'name' => 'required|string|max:191|unique:permissions,name,'.($this->id ?? 'NULL').',id',
            'description' => 'nullable|string|max:255',
            'guard_name' => 'required|string|max:191',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El campo Nombre es requerido',
            'name.unique' => 'El campo Nombre ya existe',
            'name.string' => 'El campo Nombre debe ser un string',
            'description.string' => 'El campo Descripción debe ser un string',
            'guard_name.required' => 'El campo Nombre de Guardia es requerido',
            'guard_name.string' => 'El campo Nombre de Guardia debe ser un string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $id = $this->id ?: $this->permissionId;

        $this->merge([
            'id' => $id ? (int) $id : null,
            'name' => trim(strip_tags((string) $this->name)),
            'description' => $this->description ? trim(strip_tags((string) $this->description)) : null,
            'guard_name' => $this->guard_name ? trim(strip_tags((string) $this->guard_name)) : 'web',
        ]);
    }
}
