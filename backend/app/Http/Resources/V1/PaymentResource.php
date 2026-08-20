<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_reference' => $this->payment_reference,
            'reference' => $this->payment_reference,
            'appointment_id' => $this->appointment_id,
            'patient_id' => $this->patient_id,
            'patient_name' => $this->whenLoaded('patient', fn () => $this->patient?->user?->name),
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'status' => $this->status?->value ?? (string) $this->status,
            'status_label' => $this->status?->label() ?? 'Pending',
            'provider' => $this->provider ?? 'sandbox',
            'provider_reference' => $this->provider_reference,
            'checkout_url' => $this->checkout_url,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'expired_at' => $this->expired_at?->toIso8601String(),
            'refunded_at' => $this->refunded_at?->toIso8601String(),
            'refund_reason' => $this->refund_reason,
            'created_at' => $this->created_at?->toIso8601String(),
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
        ];
    }
}
