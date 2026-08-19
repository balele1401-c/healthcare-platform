<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'prescription_code' => $this->prescription_code,
            'patient_id' => $this->patient_id,
            'patient_name' => $this->whenLoaded('patient', fn () => $this->patient->user?->name),
            'doctor_id' => $this->doctor_id,
            'doctor_name' => $this->whenLoaded('doctor', fn () => $this->doctor->user?->name),
            'doctor_specialty' => $this->whenLoaded('doctor', fn () => $this->doctor->specialty?->name),
            'medical_record_id' => $this->medical_record_id,
            'prescription_date' => $this->prescription_date?->format('Y-m-d'),
            'status' => $this->status?->value ?? (string) $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'items' => PrescriptionItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
