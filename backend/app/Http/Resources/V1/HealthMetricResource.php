<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HealthMetricResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'metric_type' => $this->metric_type?->value ?? (string) $this->metric_type,
            'metric_label' => $this->metric_type?->label() ?? (string) $this->metric_type,
            'value' => (float) $this->value,
            'secondary_value' => $this->secondary_value !== null ? (float) $this->secondary_value : null,
            'unit' => $this->unit,
            'measured_at' => $this->measured_at?->toIso8601String(),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
