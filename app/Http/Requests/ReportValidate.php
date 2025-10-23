<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'dni'                   => 'required|digits:8',
            'antecedentes'          => 'required|string',
            'historial_enfermedad'  => 'required|string',
            'examen_fisico'         => 'required|string',
            'examen_complementario' => 'required|string',
            'sugerencia'            => 'required|string',
            'tratamiento'           => 'required|string',
        ];
    }

    public function messages(): array {
        return [
            'dni.required'                      => 'El campo DNI es requerido',
            'dni.digits'                        => 'El campo DNI debe tener 8 dígitos',
            'antecedentes.required'             => 'El campo Antecedentes es requerido',
            'historial_enfermedad.required'     => 'El campo Historial de Enfermed ad es requerido',
            'examen_fisico.required'            => 'El campo Examen Físico es requerido',
            'examen_complementario.required'    => 'El campo Examen Complementario es requerido',
            'sugerencia.required'               => 'El campo Sugerencia es requerido',
            'tratamiento.required'              => 'El campo Tratamiento es requerido',
        ];
    }

    /*protected function prepareForValidation(): void {
        $this->merge([
            'dni'                   => trim(strip_tags($this->dni)),
            'antecedentes'          => trim(strip_tags($this->antecedentes)),
            'historial_enfermedad'  => trim(strip_tags($this->historial_enfermedad)),
            'examen_fisico'         => trim(strip_tags($this->examen_fisico)),
            'examen_complementario' => trim(strip_tags($this->examen_complementario)),
            'sugerencia'            => trim(strip_tags($this->sugerencia)),
            'tratamiento'           => trim(strip_tags($this->tratamiento)),
        ]);
    }*/
}
