<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        $userId = $this->input('userId');
        return [
            'name'                  => 'required|string|max:255',
            'biografia'             => 'nullable|string|max:255',
            'specialty'             => 'required',
            'role_id'               => 'required',
            'avatar'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'avatar.image'                      => 'El campo avatar solo acpeta estos formatos jpeg, png, jpg, gif',
            'avatar.max'                        => 'Límite de la imagen excedida',
        ];
    }
}
