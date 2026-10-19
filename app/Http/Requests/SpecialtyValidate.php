<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpecialtyValidate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->input('id', $this->input('specialtyId'));

        return [
            'id_ocupacion' => 'required',
            'descripcion' => [
                'required',
                'string',
                Rule::unique('especialidades', 'descripcion')->ignore($id),
            ],
            'detalle' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id_ocupacion.required' => 'El campo Cargo es obligatorio.',
            'descripcion.required' => 'El campo Descripción es obligatorio.',
            'descripcion.unique' => 'El campo Descripción ya existe.',
            'detalle.required' => 'El campo Detalle es obligatorio.',
            'detalle.string' => 'El campo Detalle debe ser una cadena de texto.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id_ocupacion' => trim(strip_tags((string) $this->id_ocupacion)),
            'descripcion' => trim(strip_tags((string) $this->descripcion)),
            'detalle' => trim(strip_tags((string) $this->detalle)),
        ]);
    }
}
