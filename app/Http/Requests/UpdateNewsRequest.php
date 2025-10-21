<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Editor'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('news', 'slug')->ignore($this->route('news'))
            ],
            'summary' => 'sometimes|required|string|max:500',
            'content' => 'sometimes|required|string',
            'image_url' => 'nullable|url|max:255',
            'news_category_id' => 'sometimes|required|exists:news_categories,id',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'slug.required' => 'El slug es obligatorio.',
            'slug.unique' => 'Este slug ya está en uso.',
            'summary.required' => 'El resumen es obligatorio.',
            'content.required' => 'El contenido es obligatorio.',
            'news_category_id.required' => 'La categoría es obligatoria.',
            'news_category_id.exists' => 'La categoría seleccionada no existe.',
            'image_url.url' => 'La URL de la imagen debe ser válida.'
        ];
    }
}
