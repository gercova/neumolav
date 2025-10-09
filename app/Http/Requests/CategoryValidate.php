<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryValidate extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'descripcion'           => 'required|string|max:50|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/|unique:droga_categoria,descripcion,'.$this->id,
            'detalle'               => 'required|string|max:255|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
        ];
    }

    public function messages() {
        return [
            'descripcion.required'  => 'La descripción es requerida',
            'descripcion.string'    => 'La descripción debe ser un texto',
            'descripcion.max'       => 'La descripción no puede tener más de 255 caracteres',
            'descripcion.regex'     => 'La descripción solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'descripcion.unique'    => 'La descripción ya existe',
            'detalle.required'      => 'El detalle es requerido',
            'detalle.string'        => 'El detalle debe ser un texto',
            'detalle.max'           => 'El detalle no puede tener más de 255 caracteres',
            'detalle.regex'         => 'El detalle solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'descripcion' => trim(strip_tags($this->descripcion)),
            'detalle'     => trim(strip_tags($this->detalle)),
        ]);
    }
}