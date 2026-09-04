<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HistoryValidate extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'id_td'                         => 'required',
            'dni'                           => [
                'required',
                'string',
                Rule::unique('historias', 'dni')->ignore($this->id),
                Rule::when($this->id_td == 1, ['digits:8']),
                Rule::when($this->id_td == 3, ['min:8', 'max:12']),
                Rule::when($this->id_td == 4, ['min:6', 'max:15']),
            ],
            'nombres'                       => 'required|string|max:150|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/u',
            'fecha_nacimiento'              => 'required|date|before_or_equal:today|after:1900-01-01',
            'id_sexo'                       => 'required',
            'telefono'                      => 'required|string|min:7|max:11',
            'id_gs'                         => 'required',
            'ubigeo_residencia'             => 'required',
            'ubigeo_nacimiento'             => 'nullable|string',
            'ubigeo_extranjero'             => 'nullable|string|max:255',
            'extranjero'                    => 'nullable|string|max:255',
            'id_gi'                         => 'required',
            'id_ocupacion'                  => 'required',
            'id_estado'                     => 'required',
            'cirugias'                      => 'nullable|string',
            'transfusiones'                 => 'nullable|string',
            'traumatismos'                  => 'nullable|string',
            'hospitalizaciones'             => 'nullable|string',
            'drogas'                        => 'nullable|string',
            'antecedentes'                  => 'nullable|string',
            'estadobasal'                   => 'nullable|string',
            'medicacion'                    => 'nullable|string',
            'animales'                      => 'nullable|string',
            'consumoagua'                   => 'nullable|string',
            'alimentacion'                  => 'nullable|string',
            'otros'                         => 'nullable|string',
            'asmabronquial'                 => 'nullable|string',
            'epoc'                          => 'nullable|string',
            'epid'                          => 'nullable|string',
            'tuberculosis'                  => 'nullable|string',
            'cancerpulmon'                  => 'nullable|string',
            'efusionpleural'                => 'nullable|string',
            'neumonias'                     => 'nullable|string',
            'tabaquismo'                    => 'nullable|string',
            'id_ct'                         => 'nullable|integer',
            'cig'                           => 'nullable|numeric',
            'aniosfum'                      => 'nullable|numeric',
            'result'                        => 'nullable|numeric',
            'contactotbc'                   => 'nullable|string',
            'exposicionbiomasa'             => 'nullable|string',
            'motivoconsulta'                => 'nullable|string',
            'sintomascardinales'            => 'nullable|string',
            'te'                            => 'nullable|string',
            'fi'                            => 'nullable|string',
            'c'                             => 'nullable|string',
            'relatocronologico'             => 'nullable|string',
        ];
    }

    public function messages(): array {
        return [
            'id_td.required'                => 'El campo Tipo de documento es obligatorio.',
            'dni.required'                  => 'El campo Documento/DNI es obligatorio.',
            'dni.digits'                    => 'El DNI debe contener exactamente 8 dígitos.',
            'dni.unique'                    => 'El número de documento ya se encuentra registrado en otra historia.',
            'dni.min'                       => 'El número de documento no cumple con la longitud mínima.',
            'dni.max'                       => 'El número de documento excede la longitud permitida.',
            'nombres.required'              => 'El campo Nombres es obligatorio.',
            'nombres.string'                => 'El campo Nombres debe ser una cadena de texto.',
            'nombres.max'                   => 'El campo Nombres no debe exceder los 150 caracteres.',
            'nombres.regex'                 => 'El formato del nombre contiene caracteres no válidos.',
            'fecha_nacimiento.required'     => 'El campo Fecha de Nacimiento es obligatorio.',
            'fecha_nacimiento.date'         => 'El campo Fecha de Nacimiento debe ser una fecha válida.',
            'fecha_nacimiento.before_or_equal' => 'La Fecha de Nacimiento no puede ser una fecha futura.',
            'fecha_nacimiento.after'        => 'La Fecha de Nacimiento debe ser posterior al 01/01/1900.',
            'id_sexo.required'              => 'El campo Sexo es obligatorio.',
            'telefono.required'             => 'El campo Celular/Teléfono es obligatorio.',
            'telefono.min'                  => 'El Celular/Teléfono debe tener al menos 7 dígitos.',
            'telefono.max'                  => 'El Celular/Teléfono no debe superar los 11 dígitos.',
            'id_gs.required'                => 'El campo Grupo Sanguíneo es obligatorio.',
            'ubigeo_residencia.required'    => 'El campo Lugar de Residencia es obligatorio.',
            'id_gi.required'                => 'El campo Grado de Instrucción es obligatorio.',
            'id_ocupacion.required'         => 'El campo Ocupación es obligatorio.',
            'id_estado.required'            => 'El campo Estado Civil es obligatorio.',
            'id_ct.integer'                 => 'El consumo de tabaco debe ser un valor válido.',
            'cig.numeric'                   => 'El campo Cigarros x día debe ser numérico.',
            'aniosfum.numeric'              => 'El campo Años fumando debe ser numérico.',
            'result.numeric'                => 'El campo Resultado debe ser numérico.',
        ];
    }

    protected function prepareForValidation(): void {
        $inputs = $this->all();
        $cleaned = [];

        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                $cleaned[$key] = trim(strip_tags($value));
            } else {
                $cleaned[$key] = $value;
            }
        }

        $this->merge($cleaned);
    }
}