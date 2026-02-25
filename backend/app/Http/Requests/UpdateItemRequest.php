<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => 'sometimes|required|string|max:255',
            'is_completed' => 'sometimes|boolean',
            'priority'     => 'sometimes|in:low,medium,high',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'El título del ítem es obligatorio.',
            'title.max'            => 'El título no puede superar los 255 caracteres.',
            'is_completed.boolean' => 'El estado de completado debe ser verdadero o falso.',
            'priority.in'          => 'La prioridad debe ser low, medium o high.',
        ];
    }
}
