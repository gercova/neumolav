<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnterpriseValidate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'razon_social'          => 'required|string|max:100|unique:enterprise,razon_social,'.$this->id ?? NULL,
            'nombre_comercial'      => 'required|string|max:100|unique:enterprise,nombre_comercial,'.$this->id ?? NULL,
            'ruc'                   => 'required|string|digits:11|unique:enterprise,ruc,'.$this->id ?? NULL,
            'email'                 => 'required|email|unique:enterprise,email,'.$this->id ?? '',
            'logo'                  => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
            'descripcion'           => 'required|string' ,
            'frase'                 => 'required|string',
            'mision'                => 'required|string',
            'vision'                => 'required|string',
            'ubigeo'                => 'required|numeric|digits:6',
            'direccion'             => 'required|string',
            'pais'                  => 'required|string',
            'codigo_pais'           => 'required|string|max:4',
            'telefono'              => 'required|digits:9',
            'pagina_web'            => 'required|string|unique:enterprise,pagina_web,'.$this->id ?? '',
            'representante_legal'   => 'required|string',
            'rubro'                 => 'required|string',
        ];
    }

    public function messages(): array {
        return [
            'razon_social.required'         => 'El campo razon social es requerido',
            'razon_social.max'              => 'El campo razon social no puede tener mas de 100 caracteres',
            'nombre_comercial.required'     => 'El campo nombre comercial es requerido',
            'nombre_comercial.max'          => 'El campo nombre comercial no puede tener mas de 100 caracteres',
            'ruc.required'                  => 'El campo RUC es requerido',
            'ruc.digits'                    => 'El campo RUC debe tener 11 digitos',
            'ruc.unique'                    => 'El campo RUC ya existe',
            'email.required'                => 'El campo email es requerido',
            'email.email'                   => 'El campo email debe ser un email',
            'email.unique'                  => 'El campo email ya existe',
            'descripcion.required'          => 'El campo descripcion es requerido',
            'descripcion.string'            => 'El campo descripcion debe ser un string',
            'frase.required'                => 'El campo frase es requerido',
            'frase.string'                  => 'El campo frase debe ser un string',
            'mision.required'               => 'El campo mision es requerido',
            'mision.string'                 => 'El campo mision debe ser un string',
            'vision.required'               => 'El campo vision es requerido',
            'vision.string'                 => 'El campo vision debe ser un string',
            'ubigeo.required'               => 'El campo ubigeo es requerido',
            'ubigeo.numeric'                => 'El campo ubigeo debe ser numerico',
            'ubigeo.digits'                 => 'El campo ubigeo debe tener 6 digitos',
            'direccion.required'            => 'El campo direccion es requerido',
            'direccion.string'              => 'El campo direccion debe ser un string',
            'pais.required'                 => 'El campo pais es requerido',
            'pais.string'                   => 'El campo pais debe ser un string',
            'codigo_pais.required'          => 'El campo codigo pais es requerido',
            'codigo_pais.string'            => 'El campo codigo pais debe ser un string',
            'codigo_pais.max'               => 'El campo codigo pais no puede tener mas de 4 caracteres',
            'telefono.required'             => 'El campo telefono es requerido',
            'telefono.digits'               => 'El campo telefono debe tener 9 digitos',
            'pagina_web.required'           => 'El campo pagina web es requerido',
            'pagina_web.string'             => 'El campo pagina web debe ser un string',
            'pagina_web.unique'             => 'El campo pagina web ya existe',
            'representante_legal.required'  => 'El campo representante legal es requerido',
            'representante_legal.string'    => 'El campo representante legal debe ser un string',
            'rubro.required'                => 'El campo rubro es requerido',
            'rubro.string'                  => 'El campo rubro debe ser un string',
        ];
    }
}
