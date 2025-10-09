<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HistoryValidate extends FormRequest
{
    public function authorize(){
        return true;
    }

    public function rules(): array {
        return [
            'id_td'                         => 'required',
            'dni'                           => [
                'required',
                'unique:historias,dni,'.$this->id,
                Rule::when($this->id_td === 1, [
                    'digits:8',
                    Rule::unique('historias', 'dni')->ignore($this->id),
                ]),
                Rule::when($this->id_td === 2, [
                    'size:9',
                    Rule::unique('historias', 'dni')->ignore($this->id),
                ]),
            ],
            'nombres'                       => 'required|string|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'fecha_nacimiento'              => 'required|date',
            'id_sexo'                       => 'required',
            'telefono'                      => 'required|digits:9',
            'id_gs'                         => 'required',
            'ubigeo_residencia'             => 'required',
            'id_gi'                         => 'required',
            'id_ocupacion'                  => 'required',
            'id_estado'                     => 'required',
            'cirugias'                      => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'transfuciones'                 => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'traumatismos'                  => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'hospitalizaciones'             => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'drogas'                        => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'antecedentes'                  => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'estadobasal'                   => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'medicacion'                    => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'animales'                      => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'consumoagua'                   => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'alimentacion'                  => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'otros'                         => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'asmabronquial'                 => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'epoc'                          => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'epid'                          => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'tuberculosis'                  => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'cancerpulmon'                  => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'efusionpleural'                => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'neumonias'                     => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'tabaquismo'                    => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'id_ct'                         => 'integer',
            'cig'                           => 'numeric',
            'aniosfum'                      => 'numeric',
            'contactotbc'                   => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'exposicionbiomasa'             => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'motivoconsulta'                => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'sintomascardinales'            => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'te'                            => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'fi'                            => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'c'                             => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
            'relatocronologico'             => 'nullable|regex:/^(?:[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\/,#\-\(\)\.])+$/',
        ];
    }

    public function messages() {
        return [
            'id_td.required'                => 'El campo Tipo de documento es obligatorio',
            'dni.required'                  => 'El campo DNI es obligadorio.',
            'dni.digits'                    => 'El campo DNI solo debe tener 8 digitos.',
            'dni.unique'                    => 'El campo DNI debe ser único.',
            'dni.size'                      => 'El campo DNI solo debe tener 9 digitos.',
            'nombres.required'              => 'El campo Nombres es obligadorio.',
            'nombres.string'                => 'El campo Nombres de ser de tipo cadena.',
            'nombres.regex'                 => 'El campo Nombres solo puede contener letras, números, espacios, puntos, comas y guiones.',
            'fecha_nacimiento.required'     => 'El campo Fecha Nacimiento es obligatorio.',
            'fecha_nacimiento.date'         => 'El campo Fecha Nacimiento debe de ser de tipo fecha.',
            'id_sexo.required'              => 'El campo Sexo es obligatorio.',
            //'id_sexo.in'                    => 'El campo Sexo solo puede contener F o M.',
            'telefono.required'             => 'El campo Teléfono es obligatorio.',
            'telefono.digits'               => 'El campo Teléfono debe tener 9 digitos.',
            'id_gs.required'                => 'El campo Grupo Sanguíneo es obligatorio.',
            'ubigeo_residencia.required'    => 'El campo Ubigeo de Residencia es obligatorio.',
            'id_gi.required'                => 'El campo Grado Instrucción es obligatorio.',
            'id_ocupacion.required'         => 'El campo Ocupación es obligatorio.',
            'id_estado.required'            => 'El campo Estado Civil es obligatorio.',
            'id_estado.integer'             => 'El campo Estado Civil debe ser un número entero.',
            'cirugias.regex'                => 'El campo Cirugias solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'transfuciones'                 => 'El campo Transfuciones solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'traumatismos'                  => 'El campo Traumatismos solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'hospitalizaciones'             => 'El campo Hospitalizaciones solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'drogas'                        => 'El campo Drogas solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'antecedentes'                  => 'El campo Antecedentess solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'estadobasal'                   => 'El campo Estado Basal solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'medicacion'                    => 'El campo Medicación solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'animales'                      => 'El campo Animales solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'consumoagua'                   => 'El campo Consumo Agua solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'alimentacion'                  => 'El campo Alimentación solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'otros'                         => 'El campo Otros solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'asmabronquial'                 => 'El campo Asma Bronquial solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'epoc'                          => 'El campo Epoc solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'epid'                          => 'El campo Epid solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'tuberculosis'                  => 'El campo Tuberculosis solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'cancerpulmon'                  => 'El campo Cancer Pulmon solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'efusionpleural'                => 'El campo Efusión Pleural solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'neumonias'                     => 'El campo Neumonias solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'tabaquismo'                    => 'El campo Tabaquismo solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'id_ct'                         => 'El campo Consumo Tabaco debe ser entero',
            'cig'                           => 'El campo Cigarros debe ser númerico',
            'aniosfum'                      => 'El campo Años debe ser númerico',
            'contactotbc'                   => 'El campo Contacto TBC solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'exposicionbiomasa'             => 'El campo Exposición Biomasa solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'motivoconsulta'                => 'El campo Motivo Consulta solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'sintomascardinales'            => 'El campo Sintomas Cardinales solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'te'                            => 'El campo TE solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'fi'                            => 'El campo FI solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'c'                             => 'El campo C solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
            'relatocronologico'             => 'El campo Relato Cronológico solo puede contener letras, números, espacios y los siguientes caracteres: /,#-()',
        ];
    }
}