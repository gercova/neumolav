<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmoduleValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'module_id'             => 'required|integer',
            'descripcion'           => 'required|string|unique:sub_module,descripcion,'.$this->submoduleId,
            'nombre'                => 'required|string|unique:sub_module,nombre,'.$this->submoduleId,
            'detalle'               => 'required|string',
            'icono'                 => 'required|string',
        ];
    }

    public function messages() {
        return [
            'module_id.required'    => 'El módulo es requerido',
            'module_id.integer'     => 'El módulo es inválido',
            'descripcion.required'  => 'La descripción es requerida',
            'descripcion.string'    => 'La descripción es inválida',
            'descripcion.unique'    => 'La descripción ya existe',
            'nombre.required'       => 'El Nombre es requerido',
            'nombre.unique'         => 'El Nombre ya existe',
            'detalle.required'      => 'El Detalle es requerido',
            'detalle.string'        => 'El detalle es inválido',
            'icono.required'        => 'El icono es requerido',
            'icono.string'          => 'El icono es inválido',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'module_id'     => trim(strip_tags($this->module_id)),
            'descripcion'   => trim(strip_tags($this->descripcion)),
            'nombre'        => trim(strip_tags($this->nombre)),
            'detalle'       => trim(strip_tags($this->detalle)),
        ]);
    }
}
