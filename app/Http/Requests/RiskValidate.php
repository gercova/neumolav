<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiskValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'dni'                               => 'required',
            'motivo'                            => 'required|string',
            'antecedente'                       => 'required|string',
            'sintomas'                          => 'required|string',
            'examen_fisico'                     => 'required|string',
            'examen_complementario'             => 'required|string',
            'riesgo_neumologico'                => 'required|string',
            'sugerencia'                        => 'required|string',
        ];
    }

    public function messages(): array {
        return [
            'dni.required'                      => 'El campo DNI es requerido',
            'motivo.required'                   => 'El campo Motivo es requerido.',
            'motivo.string'                     => 'El campo Motivo debe ser una cadena de texto.',
            'antecedente.required'              => 'El campo Antecedente es requerido.',
            'antecedente.string'                => 'El campo Antecedente debe ser una cadena de texto.',
            'sintomas.required'                 => 'El campo Sintomas es requerido.',
            'sintomas.string'                   => 'El campo Sintomas debe ser una cadena de texto.',
            'examen_fisico.required'            => 'El campo Examen Físico es requerido.',
            'examen_fisico.string'              => 'El campo Examen Físico debe ser una cadena de texto.',
            'examen_complementario.required'    => 'El campo Examen Complementario es requerido.',
            'examen_complementario.string'      => 'El campo Examen Complementario debe ser una cadena de texto.',
            'riesgo_neumologico.required'       => 'El campo Riesgo Neumológ ico es requerido.',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'dni'           => trim(strip_tags($this->dni)),
            'motivo'        => trim(strip_tags($this->motivo)),
            'antecedente'   => trim(strip_tags($this->antecedente)),
            'sintomas'      => trim(strip_tags($this->sintomas)),
            'examen_fisico' => trim(strip_tags($this->examen_fisico)),
            'examen_complementario' => trim(strip_tags($this->examen_complementario)),
            'riesgo_neumologico' => trim(strip_tags($this->riesgo_neumologico)),
        ]);
    }
}
