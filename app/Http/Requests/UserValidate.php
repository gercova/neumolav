<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'name'                  => 'required|string|max:255',
            'biografia'             => 'nullable|string|max:255',
            'specialty'             => 'required',
            'role_id'               => 'required',
            'avatar'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cmp'                   => 'nullable|string|max:20',
            'rne'                   => 'nullable|string|max:20',
            'firma_digital'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array {
        return [
            'name.required'                     => 'El campo Nombre es requerido.',
            'name.string'                       => 'El campo Nombre debe ser un texto.',
            'name.max'                          => 'El campo Nombre no puede tener más de 255 caracteres.',
            'biografia.string'                  => 'El campo Biografía debe ser un texto.',
            'biografia.max'                     => 'El campo Biografía no puede tener más de 255 caracteres.',
            'specialty.required'                => 'El campo Especialidad es requerido.',
            'role_id.required'                  => 'El campo Rol es requerido.',
            'avatar.image'                      => 'El campo avatar solo acepta estos formatos jpeg, png, jpg, gif',
            'avatar.max'                        => 'Límite de la imagen excedida',
            'cmp.max'                           => 'El número de CMP no debe superar los 20 caracteres.',
            'rne.max'                           => 'El número de RNE no debe superar los 20 caracteres.',
            'firma_digital.image'               => 'La firma digital debe ser una imagen válida (PNG con fondo transparente recomendado).',
            'firma_digital.max'                 => 'El archivo de firma no debe superar los 2MB.',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'name'          => trim(strip_tags($this->name)),
            'biografia'     => trim(strip_tags($this->biografia)),
            'specialty'     => trim(strip_tags($this->specialty)),
            'role_id'       => trim(strip_tags($this->role_id)),
            'cmp'           => $this->cmp ? trim(strip_tags($this->cmp)) : null,
            'rne'           => $this->rne ? trim(strip_tags($this->rne)) : null,
        ]);
    }
}
