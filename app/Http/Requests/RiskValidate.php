<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiskValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'dni'                               => 'required|digits:8',
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
            'dni.digits'                        => 'El campo DNI debe tener 8 dígitos.',
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
}
