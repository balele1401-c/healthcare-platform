<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->whenLoaded('user', fn () => $this->user->name),
            'email' => $this->whenLoaded('user', fn () => $this->user->email),
            'phone' => $this->whenLoaded('user', fn () => $this->user->phone),
            'avatar_url' => $this->profile_photo ?? $this->whenLoaded('user', fn () => $this->user->avatar_url),
            'specialty' => new SpecialtyResource($this->whenLoaded('specialty')),
            'specialty_name' => $this->whenLoaded('specialty', fn () => $this->specialty->name),
            'license_number' => $this->license_number,
            'biography' => $this->biography,
            'education' => $this->education,
            'experience_years' => (int) $this->experience_years,
            'consultation_fee' => (float) $this->consultation_fee,
            'facility' => $this->facility,
            'rating' => (float) $this->rating,
            'review_count' => (int) $this->review_count,
            'status' => $this->status,
            'schedules' => DoctorScheduleResource::collection($this->whenLoaded('schedules')),
        ];
    }
}
