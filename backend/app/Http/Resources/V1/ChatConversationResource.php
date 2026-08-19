<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'patient_name' => $this->whenLoaded('patient', fn () => $this->patient->user?->name),
            'patient_photo' => $this->whenLoaded('patient', fn () => $this->patient->profile_photo ?? $this->patient->user?->avatar_url),
            'doctor_id' => $this->doctor_id,
            'doctor_name' => $this->whenLoaded('doctor', fn () => $this->doctor->user?->name),
            'doctor_specialty' => $this->whenLoaded('doctor', fn () => $this->doctor->specialty?->name),
            'doctor_photo' => $this->whenLoaded('doctor', fn () => $this->doctor->profile_photo ?? $this->doctor->user?->avatar_url),
            'appointment_id' => $this->appointment_id,
            'status' => $this->status,
            'last_message_at' => $this->last_message_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'messages' => ChatMessageResource::collection($this->whenLoaded('messages')),
        ];
    }
}
