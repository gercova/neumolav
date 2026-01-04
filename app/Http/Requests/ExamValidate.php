<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamValidate extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'id_historia'           => 'required',
            'dni' 		            => 'required',
            'id_tipo' 	            => 'required|integer',
            'ta'                    => 'nullable|string',
            'fc'                    => 'nullable|string',
            'rf'                    => 'nullable|string',
            'so2'                   => 'nullable|string',
            'peso'                  => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'talla'                 => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'imc'                   => 'nullable|string',
            'pym'                   => 'nullable|string',
            'typ'                   => 'nullable|string',
            'cv'                    => 'nullable|string',
            'abdomen'               => 'nullable|string',
            'hemolinfopoyetico'     => 'nullable|string',
            'tcs'                   => 'nullable|string',
            'neurologico'           => 'nullable|string',
            'hemograma'             => 'nullable|string',
            'bioquimico'            => 'nullable|string',
            'perfilhepatico'        => 'nullable|string',
            'perfilcoagulacion'     => 'nullable|string',
            'perfilreumatologico'   => 'nullable|string',
            'orina'                 => 'nullable|string',
            'sangre'                => 'nullable|string',
            'esputo'                => 'nullable|string',
            'heces'                 => 'nullable|string',
            'lcr'                   => 'nullable|string',
            'citoquimico'           => 'nullable|string',
            'adalp'                 => 'nullable|string',
            'paplp'                 => 'nullable|string',
            'bclp'                  => 'nullable|string',
            'cgchlp'                => 'nullable|string',
            'cbklp'                 => 'nullable|string',
            'bkdab'                 => 'nullable|string',
            'bkcab'                 => 'nullable|string',
            'cgchab'                => 'nullable|string',
            'papab'                 => 'nullable|string',
            'bcab'                  => 'nullable|string',
            'pulmon'                => 'nullable|string',
            'pleurabpp'             => 'nullable|string',
            'funcionpulmonar'       => 'nullable|string',
            'medicinanuclear'       => 'nullable|string',
            'plandiag'              => 'nullable|string',
            'plan'                  => 'nullable|string',
            'otros'                 => 'nullable|string',
        ];
    }

    public function messages(): array {
        return [
            'id_historia.required'      => 'El campo Historia es requerido',
            'dni.required'              => 'El campo DNI es requerido',
            'id_tipo.required'          => 'El campo tipo de examen es requerido',
            'id_tipo.integer'           => 'El campo tipo de examen debe ser un número',
            'peso.regex'                => 'El campo PESO solo puede contener números y decimales',
            'peso.required'             => 'El campo PESO es requerido',
            'talla.regex'               => 'El campo TALLA solo puede contener números y decimales',
            'talla.required'            => 'El campo TALLA es requerido',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'id_historia'           => trim(strip_tags($this->id_historia)),
            'dni' 		            => trim(strip_tags($this->dni)),
            'id_tipo' 	            => trim(strip_tags($this->id_tipo)),
            'ta'                    => trim(strip_tags($this->ta)),
            'fc'                    => trim(strip_tags($this->fc)),
            'rf'                    => trim(strip_tags($this->rf)),
            'so2'                   => trim(strip_tags($this->so2)),
            'peso'                  => trim(strip_tags($this->peso)),
            'talla'                 => trim(strip_tags($this->talla)),
            'imc'                   => trim(strip_tags($this->imc)),
            'pym'                   => trim(strip_tags($this->pym)),
            'typ'                   => trim(strip_tags($this->typ)),
            'cv'                    => trim(strip_tags($this->cv)),
            'abdomen'               => trim(strip_tags($this->abdomen)),
            'hemolinfopoyetico'     => trim(strip_tags($this->hemolinfopoyetico)),
            'tcs'                   => trim(strip_tags($this->tcs)),
            'neurologico'           => trim(strip_tags($this->neurologico)),
            'hemograma'             => trim(strip_tags($this->hemograma)),
            'bioquimico'            => trim(strip_tags($this->bioquimico)),
            'perfilhepatico'        => trim(strip_tags($this->perfilhepatico)),
            'perfilcoagulacion'     => trim(strip_tags($this->perfilcoagulacion)),
            'perfilreumatologico'   => trim(strip_tags($this->perfilreumatologico)),
            'orina'                 => trim(strip_tags($this->orina)),
            'sangre'                => trim(strip_tags($this->sangre)),
            'esputo'                => trim(strip_tags($this->esputo)),
            'heces'                 => trim(strip_tags($this->heces)),
            'lcr'                   => trim(strip_tags($this->lcr)),
            'citoquimico'           => trim(strip_tags($this->citoquimico)),
            'adalp'                 => trim(strip_tags($this->adalp)),
            'paplp'                 => trim(strip_tags($this->paplp)),
            'bclp'                  => trim(strip_tags($this->bclp)),
            'cgchlp'                => trim(strip_tags($this->cgchlp)),
            'cbklp'                 => trim(strip_tags($this->cbklp)),
            'bkdab'                 => trim(strip_tags($this->bkdab)),
            'bkcab'                 => trim(strip_tags($this->bkcab)),
            'cgchab'                => trim(strip_tags($this->cgchab)),
            'papab'                 => trim(strip_tags($this->papab)),
            'bcab'                  => trim(strip_tags($this->bcab)),
            'pulmon'                => trim(strip_tags($this->pulmon)),
            'pleurabpp'             => trim(strip_tags($this->pleurabpp)),
            'funcionpulmonar'       => trim(strip_tags($this->funcionpulmonar)),
            'medicinanuclear'       => trim(strip_tags($this->medicinanuclear)),
            'plandiag'              => trim(strip_tags($this->plandiag)),
            'plan'                  => trim(strip_tags($this->plan)),
            'otros'                 => trim(strip_tags($this->otros)),
        ]);
    }
}
