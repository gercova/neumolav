<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiagnosticValidate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->input('id');

        return [
            'descripcion' => 'required|string|max:50|unique:diagnosticos,descripcion,'.($id ?? 'NULL').',id,deleted_at,NULL',
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'El campo descripción es requerido',
            'descripcion.string' => 'El campo descripción debe ser un texto',
            'descripcion.max' => 'El campo descripción no puede tener más de 50 caracteres',
            'descripcion.unique' => 'El campo descripción ya existe',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'descripcion' => is_string($this->descripcion) ? trim(strip_tags($this->descripcion)) : $this->descripcion,
        ]);
    }
}
