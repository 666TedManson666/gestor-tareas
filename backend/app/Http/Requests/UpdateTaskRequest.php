<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status'      => 'sometimes|required|in:pending,inProgress,done',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.max'      => 'El título no puede superar los 255 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.in'       => 'El estado debe ser pending, inProgress o done.',
        ];
    }
}
