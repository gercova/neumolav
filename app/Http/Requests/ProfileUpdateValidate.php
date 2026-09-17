<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateValidate extends FormRequest {

    public function authorize(): bool {
        return auth()->check();
    }

    public function rules(): array {
        return [
            'name'                  => 'required|string|max:255',
            'biografia'             => 'nullable|string|max:500',
            'specialty'             => 'nullable',
            'cmp'                   => 'nullable|string|max:20',
            'rne'                   => 'nullable|string|max:20',
            'avatar'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'firma_digital'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array {
        return [
            'name.required'         => 'El campo Nombre es requerido.',
            'name.string'           => 'El campo Nombre debe ser un texto.',
            'name.max'              => 'El campo Nombre no puede tener más de 255 caracteres.',
            'biografia.string'      => 'El campo Biografía debe ser un texto.',
            'biografia.max'         => 'El campo Biografía no puede tener más de 500 caracteres.',
            'avatar.image'          => 'El avatar solo acepta estos formatos: jpeg, png, jpg, gif.',
            'avatar.max'            => 'Límite de la imagen excedido (máximo 2MB).',
            'cmp.max'               => 'El número de CMP no debe superar los 20 caracteres.',
            'rne.max'               => 'El número de RNE no debe superar los 20 caracteres.',
            'firma_digital.image'   => 'La firma digital debe ser una imagen válida (PNG con fondo transparente recomendado).',
            'firma_digital.max'     => 'El archivo de firma no debe superar los 2MB.',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'name'          => trim(strip_tags($this->name)),
            'biografia'     => $this->biografia ? trim(strip_tags($this->biografia)) : null,
            'cmp'           => $this->cmp ? trim(strip_tags($this->cmp)) : null,
            'rne'           => $this->rne ? trim(strip_tags($this->rne)) : null,
        ]);
    }
}
