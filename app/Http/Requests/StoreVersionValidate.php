<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVersionValidate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('configuracion_crear') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'version' => [
                'required',
                'string',
                'regex:/^v?[0-9]+\.[0-9]+\.[0-9]+$/',
                Rule::unique('app_versions', 'version'),
            ],
            'version_type' => [
                'required',
                'string',
                Rule::in(['MAJOR', 'MINOR', 'PATCH']),
            ],
            'summary' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'commit_hash' => [
                'nullable',
                'string',
                'max:40',
            ],
            'classes_changed' => ['nullable', 'array'],
            'methods_changed' => ['nullable', 'array'],
            'views_changed' => ['nullable', 'array'],
            'scripts_changed' => ['nullable', 'array'],
            'dependencies_changed' => ['nullable', 'array'],
            'updates' => ['nullable', 'array'],
            'stats' => ['nullable', 'array'],
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'version.required' => 'El número de versión es obligatorio.',
            'version.regex' => 'El formato debe cumplir con SemVer (ejemplo: 3.4.0 o v3.4.0).',
            'version.unique' => 'Esta versión ya ha sido registrada previamente.',
            'version_type.required' => 'El tipo de versión SemVer es obligatorio.',
            'version_type.in' => 'El tipo de versión debe ser MAJOR, MINOR o PATCH.',
            'summary.required' => 'El resumen de la versión es obligatorio.',
            'summary.max' => 'El resumen no puede superar los 255 caracteres.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $version = trim((string) $this->input('version'));
        $version = ltrim($version, 'vV');

        $this->merge([
            'version' => $version,
            'version_type' => strtoupper(trim((string) $this->input('version_type', 'PATCH'))),
            'summary' => trim(strip_tags((string) $this->input('summary'))),
            'description' => $this->filled('description') ? trim((string) $this->input('description')) : null,
        ]);
    }
}
