<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'appointment_date' => ['nullable', 'date', 'after_or_equal:today'],
            'appointment_time' => ['nullable', 'date_format:H:i,H:i:s'],
            'doctor_schedule_id' => ['nullable', 'exists:doctor_schedules,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
