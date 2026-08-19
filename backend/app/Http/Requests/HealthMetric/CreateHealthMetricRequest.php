<?php

namespace App\Http\Requests\HealthMetric;

use App\Enums\HealthMetricType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateHealthMetricRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'metric_type' => ['required', new Enum(HealthMetricType::class)],
            'value' => ['required', 'numeric', 'min:0'],
            'secondary_value' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:20'],
            'measured_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
