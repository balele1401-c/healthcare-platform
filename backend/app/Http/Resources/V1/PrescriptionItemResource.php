<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'medicine_id' => $this->medicine_id,
            'medicine_name' => $this->whenLoaded('medicine', fn () => $this->medicine->name),
            'generic_name' => $this->whenLoaded('medicine', fn () => $this->medicine->generic_name),
            'strength' => $this->whenLoaded('medicine', fn () => $this->medicine->strength),
            'dosage_form' => $this->whenLoaded('medicine', fn () => $this->medicine->dosage_form),
            'dosage' => $this->dosage,
            'frequency' => $this->frequency,
            'duration' => $this->duration,
            'instructions' => $this->instructions,
            'quantity' => (int) $this->quantity,
            'refills_available' => (int) $this->refills_available,
            'medicine' => new MedicineResource($this->whenLoaded('medicine')),
        ];
    }
}
