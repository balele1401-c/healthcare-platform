<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor_id' => $this->doctor_id,
            'day_of_week' => (int) $this->day_of_week,
            'start_time' => (string) $this->start_time,
            'end_time' => (string) $this->end_time,
            'consultation_type' => $this->consultation_type?->value ?? (string) $this->consultation_type,
            'facility' => $this->facility,
            'slot_duration_minutes' => (int) $this->slot_duration_minutes,
            'max_patients' => (int) $this->max_patients,
            'is_available' => (bool) $this->is_available,
        ];
    }
}
