<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentValidate extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'id_historia'               => 'required',
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
            'id_historia.required'      => 'El campo Historia es requerido',
            'dni.required'              => 'El campo DNI es requerido',
            'sintomas.required'         => 'El campo Síntomas es requerido',
            'plan.required'             => 'El campo Plan es requerido',
            'tratamiento.required'      => 'El campo Tratamiento es requerido',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'id_historia'               => trim(strip_tags($this->id_historia)),
            'dni'                       => trim(strip_tags($this->dni)),
            'sintomas'                  => trim(strip_tags($this->sintomas)),
            'diagnostico'               => trim(strip_tags($this->diagnostico)),
            'plan'                      => trim(strip_tags($this->plan)),
            'tratamiento'               => trim(strip_tags($this->tratamiento)),
            'recomendaciones'           => trim(strip_tags($this->recomendaciones)),
        ]);
    }
}
