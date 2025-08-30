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
            // 'username'              => 'required|string|unique:users,username,'.$userId,
            // 'email'                 => 'required|string|email|max:255|unique:users,email,'.$userId,
            'biografia'             => 'nullable|string|max:255',
            'specialty'             => 'required',
            //'password'              => 'string|min:8|confirmed', 
            //'password_confirmation' => 'string|min:8',
            'role_id'               => 'required',
            'avatar'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array {
        return [
            'name.required'                     => 'El campo Nombre es requerido.',
            'name.string'                       => 'El campo Nombre debe ser un texto.',
            'name.max'                          => 'El campo Nombre no puede tener más de 255 caracteres.',
            // 'username.required'                 => 'El campo Nombre de Usuario es requerido.',
            // 'username.string'                   => 'El campo Nombre de Usuario debe ser un texto.',
            // 'username.unique'                   => 'El campo Nombre de Usuario ya existe.',
            // 'email.required'                    => 'El campo Correo Electrónico es requerido.',
            // 'email.string'                      => 'El campo Correo Electrónico debe ser un texto.',
            // 'email.email'                       => 'El campo Correo Electrónico debe ser un correo electrónico .',
            // 'email.max'                         => 'El campo Correo Electrónico no puede tener más de 255 caracteres.',
            // 'email.unique'                      => 'El campo Correo Electrónico ya existe.',
            'biografia.string'                  => 'El campo Biografía debe ser un texto.',
            'biografia.max'                     => 'El campo Biografía no puede tener más de 255 caracteres.',
            'specialty.required'                => 'El campo Especialidad es requerido.',
            //'password.required'                 => 'El campo Contraseña es requerido.',
            'password.string'                   => 'El campo Contraseña debe ser un texto.',
            'password.min'                      => 'El campo Contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'                => 'El campo Confirmar Contraseña no coincide con la Contraseña.',
            //'password_confirmation.required'    => 'El campo Confirmar Contraseña es requerido.',
            'password_confirmation.string'      => 'El campo Confirmar Contraseña debe ser un texto.',
            'password_confirmation.min'         => 'El campo Confirmar Contraseña debe tener al menos 8 caracteres.',
            'role_id.required'                  => 'El campo Rol es requerido.',
            'avatar.image'                      => 'El campo avatar solo acpeta estos formatos jpeg, png, jpg, gif',
            'avatar.max'                        => 'Límite de la imagen excedida',
        ];
    }
}
