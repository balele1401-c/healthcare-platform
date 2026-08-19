<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class CreateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:5000'],
            'message_type' => ['nullable', 'string', 'in:text,image,document'],
            'attachment_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}
