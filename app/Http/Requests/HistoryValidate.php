<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HistoryValidate extends FormRequest
{
    public function authorize(){
        return true;
    }

    public function rules(): array {
        return [
            'id_td'                         => 'required',
            'dni'                           => 'required|digits:8|unique:historias,dni,'.$this->id ?? NULL,
            'nombres'                       => 'required|string',
            'fecha_nacimiento'              => 'required|date',
            'id_sexo'                       => 'required',
            'telefono'                      => 'required|digits:9',
            'id_gs'                         => 'required',
            'ubigeo_residencia'             => 'required',
            'id_gi'                         => 'required',
            'id_ocupacion'                  => 'required',
            'id_estado'                     => 'required',
            'cirugias'                      => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'transfuciones'                 => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'traumatismos'                  => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'hospitalizaciones'             => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'drogas'                        => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'antecedentes'                  => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'estadobasal'                   => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'medicacion'                    => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'animales'                      => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'consumoagua'                   => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'alimentacion'                  => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'otros'                         => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'asmabronquial'                 => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'epoc'                          => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'epid'                          => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'tuberculosis'                  => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'cancerpulmon'                  => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'efusionpleural'                => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'neumonias'                     => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'tabaquismo'                    => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'id_ct'                         => 'integer',
            'cig'                           => 'float',
            'aniosfum'                      => 'float',
            'contactotbc'                   => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'exposicionbiomasa'             => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'motivoconsulta'                => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'sintomascardinales'            => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'te'                            => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'fi'                            => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'c'                             => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
            'relatocronologico'             => 'nullable|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s.,-]+$/',
        ];
    }

    public function messages() {
        return [
            'id_td.required'                => 'El campo Tipo de documento es obligatorio',
            'dni.required'                  => 'El campo DNI es obligadorio.',
            'dni.digits'                    => 'El campo DNI solo debe tener 8 digitos.',
            'dni.unique'                    => 'El campo DNI debe ser único.',
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
            'cirugias.regex'                => 'El campo Cirugias solo puede contener letras, números, puntos, comas y guiones.',
            'transfuciones'                 => 'El campo Transfuciones solo puede contener letras, números, puntos, comas y guiones.',
            'traumatismos'                  => 'El campo Traumatismos solo puede contener letras, números, puntos, comas y guiones.',
            'hospitalizaciones'             => 'El campo Hospitalizaciones solo puede contener letras, números, puntos, comas y guiones.',
            'drogas'                        => 'El campo Drogas solo puede contener letras, números, puntos, comas y guiones.',
            'antecedentes'                  => 'El campo Antecedentess solo puede contener letras, números, puntos, comas y guiones.',
            'estadobasal'                   => 'El campo Estado Basal solo puede contener letras, números, puntos, comas y guiones.',
            'medicacion'                    => 'El campo Medicación solo puede contener letras, números, puntos, comas y guiones.',
            'animales'                      => 'El campo Animales solo puede contener letras, números, puntos, comas y guiones.',
            'consumoagua'                   => 'El campo Consumo Agua solo puede contener letras, números, puntos, comas y guiones.',
            'alimentacion'                  => 'El campo Alimentación solo puede contener letras, números, puntos, comas y guiones.',
            'otros'                         => 'El campo Otros solo puede contener letras, números, puntos, comas y guiones.',
            'asmabronquial'                 => 'El campo Asma Bronquial solo puede contener letras, números, puntos, comas y guiones.',
            'epoc'                          => 'El campo Epoc solo puede contener letras, números, puntos, comas y guiones.',
            'epid'                          => 'El campo Epid solo puede contener letras, números, puntos, comas y guiones.',
            'tuberculosis'                  => 'El campo Tuberculosis solo puede contener letras, números, puntos, comas y guiones.',
            'cancerpulmon'                  => 'El campo Cancer Pulmon solo puede contener letras, números, puntos, comas y guiones.',
            'efusionpleural'                => 'El campo Efusión Pleural solo puede contener letras, números, puntos, comas y guiones.',
            'neumonias'                     => 'El campo Neumonias solo puede contener letras, números, puntos, comas y guiones.',
            'tabaquismo'                    => 'El campo Tabaquismo solo puede contener letras, números, puntos, comas y guiones.',
            'id_ct'                         => 'El campo Consumo Tabaco debe ser entero',
            'cig'                           => 'El campo Cigarros debe ser númerico',
            'aniosfum'                      => 'El campo Años debe ser númerico',
            'contactotbc'                   => 'El campo Contacto TBC solo puede contener letras, números, puntos, comas y guiones.',
            'exposicionbiomasa'             => 'El campo Exposición Biomasa solo puede contener letras, números, puntos, comas y guiones.',
            'motivoconsulta'                => 'El campo Motivo Consulta solo puede contener letras, números, puntos, comas y guiones.',
            'sintomascardinales'            => 'El campo Sintomas Cardinales solo puede contener letras, números, puntos, comas y guiones.',
            'te'                            => 'El campo TE solo puede contener letras, números, puntos, comas y guiones.',
            'fi'                            => 'El campo FI solo puede contener letras, números, puntos, comas y guiones.',
            'c'                             => 'El campo C solo puede contener letras, números, puntos, comas y guiones.',
            'relatocronologico'             => 'El campo Relato Cronológico solo puede contener letras, números, puntos, comas y guiones.',
        ];
    }
    
    public function prepareForValidation() {
        $this->merge([
            'dni'           => strtoupper($this->dni),
            'id_tipo'       => intval($this->id_tipo),
            'so2'           => floatval($this->so2),
            'peso'          => floatval($this->peso),
            'talla'         => floatval($this->talla),  
            'id_ct'         => intval($this->id_ct),
            'cig'           => intval($this->cig),
            'aniosfum'      => intval($this->aniosfum),
            'id_sexo'       => intval($this->id_sexo),
            'id_gs'         => intval($this->id_gs),
            'id_gi'         => intval($this->id_gi),
            'id_ocupacion'  => intval($this->id_ocupacion),
            'id_estado'     => intval($this->id_estado)
        ]);
    }
}