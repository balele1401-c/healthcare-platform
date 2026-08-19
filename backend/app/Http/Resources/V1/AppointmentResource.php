<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'patient_id' => $this->patient_id,
            'patient_name' => $this->whenLoaded('patient', fn () => $this->patient->user?->name),
            'doctor_id' => $this->doctor_id,
            'doctor_name' => $this->whenLoaded('doctor', fn () => $this->doctor->user?->name),
            'doctor_specialty' => $this->whenLoaded('doctor', fn () => $this->doctor->specialty?->name),
            'doctor_photo' => $this->whenLoaded('doctor', fn () => $this->doctor->profile_photo ?? $this->doctor->user?->avatar_url),
            'appointment_date' => $this->appointment_date?->format('Y-m-d'),
            'appointment_time' => (string) $this->appointment_time,
            'status' => $this->status?->value ?? (string) $this->status,
            'consultation_type' => $this->consultation_type?->value ?? (string) $this->consultation_type,
            'facility' => $this->facility,
            'consultation_fee' => (float) $this->consultation_fee,
            'service_fee' => (float) $this->service_fee,
            'total_amount' => (float) $this->total_amount,
            'notes' => $this->notes,
            'cancellation_reason' => $this->cancellation_reason,
            'created_at' => $this->created_at?->toIso8601String(),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'payment' => new PaymentResource($this->whenLoaded('payment')),
        ];
    }
}
