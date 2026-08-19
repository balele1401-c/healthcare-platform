<?php

namespace App\Http\Requests\HealthMetric;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHealthMetricRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'value' => ['nullable', 'numeric', 'min:0'],
            'secondary_value' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:20'],
            'measured_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
