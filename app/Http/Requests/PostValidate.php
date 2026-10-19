<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostValidate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $postId = $this->filled('postId') ? $this->input('postId') : null;

        return [
            'titulo' => [
                'required',
                'string',
                'max:150',
                Rule::unique('post', 'titulo')->ignore($postId),
            ],
            'type_id' => 'required',
            'img' => $postId
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'descrip_img' => 'required|string|max:250',
            'resumen' => 'required',
            'contenido' => 'required|string',
            'categories' => 'required|string|max:250',
            'meta_content' => 'required|string|max:250',
            'key_words' => 'required|string|max:250',
            'etiquetas' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio',
            'titulo.unique' => 'Ya existe una publicación con este título',
            'type_id.required' => 'El tipo de publicación es obligatorio',
            'img.required' => 'La imagen destacada es obligatoria para nuevas publicaciones',
            'img.image' => 'El archivo debe ser una imagen válida',
            'img.max' => 'La imagen no debe superar los 2MB',
            'descrip_img.required' => 'La descripción de la imagen es obligatoria',
            'resumen.required' => 'El resumen es obligatorio',
            'contenido.required' => 'El contenido es obligatorio',
            'categories.required' => 'Las categorías son obligatorias',
            'categories.string' => 'Las categorías deben ser una cadena de texto',
            'categories.max' => 'Las categorías no pueden exceder los 250 caracteres',
            'meta_content.required' => 'El meta-content es obligatorio',
            'meta_content.string' => 'El meta-content debe ser una cadena de texto',
            'meta_content.max' => 'El meta-content no puede exceder los 250 caracteres',
            'key_words.required' => 'Las palabras clave son obligatorias',
            'key_words.string' => 'Las palabras clave deben ser una cadena de texto',
            'key_words.max' => 'Las palabras clave no pueden exceder los 250 caracteres',
            'etiquetas.required' => 'Las etiquetas son obligatorias',
            'etiquetas.string' => 'Las etiquetas deben ser una cadena de texto',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'titulo' => trim(strip_tags((string) $this->titulo)),
            'type_id' => trim(strip_tags((string) $this->type_id)),
            'categories' => trim(strip_tags((string) ($this->categories ?? $this->categorias ?? ''))),
            'meta_content' => trim(strip_tags((string) $this->meta_content)),
            'key_words' => trim(strip_tags((string) $this->key_words)),
            'etiquetas' => trim(strip_tags((string) $this->etiquetas)),
        ]);
    }
}
