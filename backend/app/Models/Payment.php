<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_reference',
        'appointment_id',
        'patient_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'provider',
        'provider_reference',
        'provider_payment_id',
        'checkout_url',
        'idempotency_key',
        'paid_at',
        'expired_at',
        'refunded_at',
        'refund_reason',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
            'refunded_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Alias for payment_reference to support standard domain naming.
     */
    protected function reference(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->payment_reference,
            set: fn ($value) => ['payment_reference' => $value],
        );
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::PAID;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING || $this->status === PaymentStatus::PROCESSING;
    }

    public function isExpired(): bool
    {
        if ($this->status === PaymentStatus::EXPIRED) {
            return true;
        }

        return $this->expired_at && $this->expired_at->isPast() && ! $this->isPaid();
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, [PaymentStatus::PENDING, PaymentStatus::PROCESSING], true) && ! $this->isExpired();
    }

    public function canBeRefunded(): bool
    {
        return $this->status === PaymentStatus::PAID && ! $this->refunded_at;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [PaymentStatus::PENDING, PaymentStatus::PROCESSING], true);
    }

    public function markAsPaid(string $providerPaymentId = null, array $metadata = []): self
    {
        $this->update([
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
            'provider_payment_id' => $providerPaymentId ?? $this->provider_payment_id,
            'metadata' => array_merge($this->metadata ?? [], $metadata),
        ]);

        return $this;
    }

    public function markAsFailed(string $reason = null): self
    {
        $metadata = $this->metadata ?? [];
        if ($reason) {
            $metadata['failure_reason'] = $reason;
        }

        $this->update([
            'status' => PaymentStatus::FAILED,
            'metadata' => $metadata,
        ]);

        return $this;
    }

    public function markAsExpired(): self
    {
        $this->update([
            'status' => PaymentStatus::EXPIRED,
        ]);

        return $this;
    }

    public function markAsRefunded(string $reason, float $amount = null): self
    {
        $this->update([
            'status' => PaymentStatus::REFUNDED,
            'refunded_at' => now(),
            'refund_reason' => $reason,
        ]);

        return $this;
    }
}
