<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DrugValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'id_categoria'              => 'required',
            'id_presentacion'           => 'required',
            'descripcion'               => 'required|string|max:50|unique:drogas,descripcion,'.$this->id ?? NULL,
            'detalle'                   => 'required|string|max:255',
        ];
    }

    public function messages() {
        return [
            'id_categoria.required'     => 'El campo Categoría es requerido.',
            'id_presentacion.required'  => 'El campo Presentación es requerido.',
            'descripcion.required'      => 'El campo Descripción es requerido.',
            'descripcion.unique'        => 'La Descripción ya existe.',
            'descripcion.max'           => 'La Descripción no puede exceder los 50 caracteres.',
            'detalle.required'          => 'El campo Detalle es requerido.',
            'detalle.max'               => 'El Detalle no puede exceder los 255 caracteres.',
        ];
    }

    public function prepareForValidation() {
        $this->merge([
            'id_categoria'      => intval($this->id_categoria),
            'id_presentacion'   => intval($this->id_presentacion),
            'descripcion'       => strtoupper($this->descripcion),
            'detalle'           => strtoupper($this->detalle),
        ]);
    }
}
