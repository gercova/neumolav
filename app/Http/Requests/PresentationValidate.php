<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresentationValidate extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'descripcion'           => 'required|max:100|unique:droga_presentacion,descripcion,'.$this->id ?? NULL,
            'aka'                   => 'required|string|max:25',
        ];
    }

    public function messages(): array {
        return [
            'descripcion.required'  => 'El campo Descripción es requerido.',
            'descripcion.max'       => 'El campo Descripción no puede tener más de 100 caracteres.',
            'descripcion.unique'    => 'La Descripción ya existe.',
            'aka.required'          => 'El campo Alias es requerido.',
            'aka.string'            => 'El campo Alias debe ser un texto.',
            'aka.max'               => 'El campo Alias no puede tener más de :max caracteres.',
            'aka.unique'            => 'El Alias ya existe.',
        ];
    }
}
