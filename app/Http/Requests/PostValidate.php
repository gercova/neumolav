<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostValidate extends FormRequest {
    
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'titulo'        => 'required|string|max:150|unique:post,titulo,'.$this->postId,
            'type_id'       => 'required',
            'img'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'descrip_img'   => 'required|string|max:250',
            'resumen'       => 'required',
            'contenido'     => 'required|string',
            'categories'    => 'required|string|max:250',
            'meta_content'  => 'required|string|max:250',
            'key_words'     => 'required|string|max:250',
            'etiquetas'     => 'required|string',
        ];
    }

    public function messages(): array {
        return [
            'titulo.required'       => 'El título es obligatorio',
            'type_id.required'      => 'El tipo de publicación es obligatorio',
            'img.required'          => 'La imagen es obligatoria',
            'descrip_img.required'  => 'La descripción de la imagen es obligatoria',
            'contenido.required'    => 'El contenido es obligatorio',
            'categories.required'   => 'Las categorias son obligatorias',
            'categories.string'     => 'Las categorias deben ser una cadena de texto',
            'categories.max'        => 'Las categorias no pueden exceder los 250 caracteres',
            'meta_content.required' => 'El meta-content es obligatorio',
            'meta_content.string'   => 'El meta-content debe ser una cadena de texto',
            'meta_content.max'      => 'El meta-content no puede exceder los 250 caracteres',
            'key_words.required'    => 'Las palabras clave son obligatorias',
            'key_words.string'      => 'Las palabras clave deben ser una cadena de texto',
            'key_words.max'         => 'Las palabras clave no pueden exceder los 250 caracteres',
            'etiquetas.required'    => 'Las etiquetas son obligatorias',
            'etiquetas.string'      => 'Las etiquetas deben ser una cadena de texto',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'titulo'        => trim(strip_tags($this->titulo)),
            'type_id'       => trim(strip_tags($this->type_id)),
            'categories'    => trim(strip_tags($this->categorias)),
            'meta_content'  => trim(strip_tags($this->meta_content)),
            'key_words'     => trim(strip_tags($this->key_words)),
            'etiquetas'     => trim(strip_tags($this->etiquetas)),
        ]);
    }
}
