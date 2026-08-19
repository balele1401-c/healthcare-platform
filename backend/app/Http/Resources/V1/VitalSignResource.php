<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VitalSignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'systolic_blood_pressure' => $this->systolic_blood_pressure,
            'diastolic_blood_pressure' => $this->diastolic_blood_pressure,
            'blood_pressure_formatted' => ($this->systolic_blood_pressure && $this->diastolic_blood_pressure)
                ? "{$this->systolic_blood_pressure}/{$this->diastolic_blood_pressure} mmHg"
                : null,
            'heart_rate' => $this->heart_rate,
            'body_temperature' => $this->body_temperature !== null ? (float) $this->body_temperature : null,
            'blood_oxygen' => $this->blood_oxygen,
            'respiratory_rate' => $this->respiratory_rate,
            'weight' => $this->weight !== null ? (float) $this->weight : null,
            'height' => $this->height !== null ? (float) $this->height : null,
            'blood_glucose' => $this->blood_glucose !== null ? (float) $this->blood_glucose : null,
            'measured_at' => $this->measured_at?->toIso8601String(),
        ];
    }
}
