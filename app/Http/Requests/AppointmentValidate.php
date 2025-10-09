<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentValidate extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'dni'                       => 'required|digits:8',
            'sintomas'                  => 'required|string|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'diagnostico'               => 'nullable|string|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'plan'                      => 'required|string|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'tratamiento'               => 'required|string|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'recomendaciones'           => 'nullable|string|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
        ];
    }

    public function messages(): array {
        return [
            'dni.required'              => 'El campo DNI es requerido',
            'dni.digits'                => 'El campo DNI debe tener 8 dígitos',
            'sintomas.required'         => 'El campo Síntomas es requerido',
            'sintomas.regex'            => 'El campo Síntomas solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'diagnostico.regex'         => 'El campo Diagnóstico solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'plan.required'             => 'El campo Plan es requerido',
            'plan.regex'                => 'El campo Plan solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'tratamiento.required'      => 'El campo Tratamiento es requerido',
            'tratamiento.regex'         => 'El campo Tratamiento solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'recomendaciones.regex'     => 'El campo Recomendaciones solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'dni'                       => filter_var(trim($this->dni), FILTER_SANITIZE_NUMBER_INT),
            'sintomas'                  => trim(strip_tags($this->sintomas)),
            'diagnostico'               => trim(strip_tags($this->diagnostico)),
            'plan'                      => trim(strip_tags($this->plan)),
            'tratamiento'               => trim(strip_tags($this->tratamiento)),
            'recomendaciones'           => $this->recomendaciones ? trim(strip_tags($this->recomendaciones)) : null,
        ]);
    }   
}
