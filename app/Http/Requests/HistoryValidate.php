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
            'cirugias'                      => 'nullable|string',
            'transfuciones'                 => 'nullable|string',
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
            'id_ct'                         => 'integer',
            'cig'                           => 'numeric',
            'aniosfum'                      => 'numeric',
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

    public function messages() {
        return [
            'id_td.required'                => 'El campo Tipo de documento es obligatorio',
            'dni.required'                  => 'El campo DNI es obligadorio.',
            'dni.digits'                    => 'El campo DNI solo debe tener 8 digitos.',
            'dni.unique'                    => 'El campo DNI debe ser único.',
            'dni.size'                      => 'El campo DNI solo debe tener 9 digitos.',
            'nombres.required'              => 'El campo Nombres es obligadorio.',
            'nombres.string'                => 'El campo Nombres de ser de tipo cadena.',
            'fecha_nacimiento.required'     => 'El campo Fecha Nacimiento es obligatorio.',
            'fecha_nacimiento.date'         => 'El campo Fecha Nacimiento debe de ser de tipo fecha.',
            'id_sexo.required'              => 'El campo Sexo es obligatorio.',
            'telefono.required'             => 'El campo Teléfono es obligatorio.',
            'telefono.digits'               => 'El campo Teléfono debe tener 9 digitos.',
            'id_gs.required'                => 'El campo Grupo Sanguíneo es obligatorio.',
            'ubigeo_residencia.required'    => 'El campo Ubigeo de Residencia es obligatorio.',
            'id_gi.required'                => 'El campo Grado Instrucción es obligatorio.',
            'id_ocupacion.required'         => 'El campo Ocupación es obligatorio.',
            'id_estado.required'            => 'El campo Estado Civil es obligatorio.',
            'id_estado.integer'             => 'El campo Estado Civil debe ser un número entero.',
            'id_ct.integer'                 => 'El campo Consumo Tabaco debe ser entero',
            'cig.numeric'                   => 'El campo Cigarros debe ser númerico',
            'aniosfum.numeric'              => 'El campo Años debe ser númerico',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'id_td'                         => trim(strip_tags($this->id_td)),
            'dni'                           => trim(strip_tags($this->dni)),
            'nombres'                       => trim(strip_tags($this->nombres)),
            'fecha_nacimiento'              => trim(strip_tags($this->fecha_nacimiento)),
            'id_sexo'                       => trim(strip_tags($this->id_sexo)),
            'telefono'                      => trim(strip_tags($this->telefono)),
            'id_gs'                         => trim(strip_tags($this->id_gs)),
            'ubigeo_residencia'             => trim(strip_tags($this->ubigeo_residencia)),
            'id_gi'                         => trim(strip_tags($this->id_gi)),
            'id_ocupacion'                  => trim(strip_tags($this->id_ocupacion)),
            'id_estado'                     => trim(strip_tags($this->id_estado)),
            'cirugias'                      => trim(strip_tags($this->cirugias)),
            'transfuciones'                 => trim(strip_tags($this->transfuciones)),
            'traumatismos'                  => trim(strip_tags($this->traumatismos)),
            'hospitalizaciones'             => trim(strip_tags($this->hospitalizaciones)),
            'drogas'                        => trim(strip_tags($this->drogas)),
            'antecedentes'                  => trim(strip_tags($this->antecedentes)),
            'estadobasal'                   => trim(strip_tags($this->estadobasal)),
            'medicacion'                    => trim(strip_tags($this->medicacion)),
            'animales'                      => trim(strip_tags($this->animales)),
            'consumoagua'                   => trim(strip_tags($this->consumoagua)),
            'alimentacion'                  => trim(strip_tags($this->alimentacion)),
            'otros'                         => trim(strip_tags($this->otros)),
            'asmabronquial'                 => trim(strip_tags($this->asmabronquial)),
            'epoc'                          => trim(strip_tags($this->epoc)),
            'epid'                          => trim(strip_tags($this->epid)),
            'tuberculosis'                  => trim(strip_tags($this->tuberculosis)),
            'cancerpulmon'                  => trim(strip_tags($this->cancerpulmon)),
            'efusionpleural'                => trim(strip_tags($this->efusionpleural)),
            'neumonias'                     => trim(strip_tags($this->neumonias)),
            'tabaquismo'                    => trim(strip_tags($this->tabaquismo)),
            'id_ct'                         => trim(strip_tags($this->id_ct)),
            'cig'                           => trim(strip_tags($this->cig)),
            'aniosfum'                      => trim(strip_tags($this->aniosfum)),
            'contactotbc'                   => trim(strip_tags($this->contactotbc)),
            'exposicionbiomasa'             => trim(strip_tags($this->exposicionbiomasa)),
            'motivoconsulta'                => trim(strip_tags($this->motivoconsulta)),
            'sintomascardinales'            => trim(strip_tags($this->sintomascardinales)),
            'te'                            => trim(strip_tags($this->te)),
            'fi'                            => trim(strip_tags($this->fi)),
            'c'                             => trim(strip_tags($this->c)),
            'relatocronologico'             => trim(strip_tags($this->relatocronologico)),
        ]);
    }
}