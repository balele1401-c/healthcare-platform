<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'record_number' => $this->record_number,
            'patient_id' => $this->patient_id,
            'patient_name' => $this->whenLoaded('patient', fn () => $this->patient->user?->name),
            'doctor_id' => $this->doctor_id,
            'doctor_name' => $this->whenLoaded('doctor', fn () => $this->doctor->user?->name),
            'doctor_specialty' => $this->whenLoaded('doctor', fn () => $this->doctor->specialty?->name),
            'appointment_id' => $this->appointment_id,
            'visit_date' => $this->visit_date?->format('Y-m-d'),
            'chief_complaint' => $this->chief_complaint,
            'symptoms' => $this->symptoms,
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'follow_up_date' => $this->follow_up_date?->format('Y-m-d'),
            'allergies' => $this->allergies,
            'medical_history' => $this->medical_history,
            'clinical_notes' => $this->clinical_notes,
            'facility' => $this->facility,
            'created_at' => $this->created_at?->toIso8601String(),
            'vital_signs' => new VitalSignResource($this->whenLoaded('vitalSigns')),
            'prescriptions' => PrescriptionResource::collection($this->whenLoaded('prescriptions')),
        ];
    }
}
