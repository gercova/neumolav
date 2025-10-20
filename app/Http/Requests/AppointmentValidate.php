<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentValidate extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'dni'                       => 'required',
            'sintomas'                  => 'required|string',
            'diagnostico'               => 'nullable|string',
            'plan'                      => 'required|string',
            'tratamiento'               => 'required|string',
            'recomendaciones'           => 'nullable|string',
        ];
    }

    public function messages(): array {
        return [
            'dni.required'              => 'El campo DNI es requerido',
            'sintomas.required'         => 'El campo Síntomas es requerido',
            'sintomas.regex'            => 'El campo Síntomas solo puede contener letras, números, espacios y los siguientes caracteres :[]/,#-()_',
            'diagnostico.regex'         => 'El campo Diagnóstico solo puede contener letras, números, espacios y los siguientes caracteres :[]/,#-()_',
            'plan.required'             => 'El campo Plan es requerido',
            'plan.regex'                => 'El campo Plan solo puede contener letras, números, espacios y los siguientes caracteres :[]/,#-()_',
            'tratamiento.required'      => 'El campo Tratamiento es requerido',
            'tratamiento.regex'         => 'El campo Tratamiento solo puede contener letras, números, espacios y los siguientes caracteres :[]/,#-()_',
            'recomendaciones.regex'     => 'El campo Recomendaciones solo puede contener letras, números, espacios y los siguientes caracteres :[]/,#-()_',
        ];
    }

    /*protected function prepareForValidation(): void {
        $this->merge([
            'dni'                       => trim(($this->dni), FILTER_SANITIZE_NUMBER_INT),
            'sintomas'                  => trim(strip_tags($this->sintomas)),
            'diagnostico'               => trim(strip_tags($this->diagnostico)),
            'plan'                      => trim(strip_tags($this->plan)),
            'tratamiento'               => trim(strip_tags($this->tratamiento)),
            'recomendaciones'           => $this->recomendaciones ? trim(strip_tags($this->recomendaciones)) : null,
        ]);
    }*/  
}
