<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationValidate extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'name'      => 'required|regex:/^[a-zA-Z\s]+$/',
            'email'     => 'required|email',
            'phone'     => 'required|regex:/^[0-9]+$/',
            'message'   => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\s]+$/',
        ];
    }

    public function messages(): array {
        return [
            'name.required'     => 'El nombre es requerido',
            'name.regex'        => 'El nombre solo puede contener letras y espacios',
            'email.required'    => 'El correo electrónico es requerido',
            'phone.required'    => 'El teléfono es requerido',
            'phone.regex'       => 'El teléfono solo puede contener números',
            'message.string'    => 'El mensaje debe ser una cadena de texto',
            'message.max'       => 'El mensaje no puede exceder los 255 caracteres',
            'message.regex'     => 'El mensaje solo puede contener letras, números y espacios',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'name'      => trim(strip_tags($this->name)),
            'email'     => filter_var(trim($this->email), FILTER_SANITIZE_EMAIL),
            'phone'     => filter_var(trim($this->phone), FILTER_SANITIZE_NUMBER_INT),
            'message'   => $this->message ? trim(strip_tags($this->message)) : null
        ]);
    }
}
