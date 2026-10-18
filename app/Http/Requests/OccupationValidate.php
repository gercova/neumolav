<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OccupationValidate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->input('id');

        return [
            'descripcion' => 'required|string|max:100|unique:ocupaciones,descripcion,'.($id ?? 'NULL').',id,deleted_at,NULL',
            'estado' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'El campo descripción es requerido.',
            'descripcion.string' => 'El campo descripción debe ser un texto.',
            'descripcion.max' => 'El campo descripción no puede tener más de 100 caracteres.',
            'descripcion.unique' => 'La descripción ya existe.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'descripcion' => is_string($this->descripcion) ? trim(strip_tags($this->descripcion)) : $this->descripcion,
        ]);
    }
}
