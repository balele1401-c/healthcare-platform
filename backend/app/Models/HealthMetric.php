<?php

namespace App\Models;

use App\Enums\HealthMetricType;
use Database\Factories\HealthMetricFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthMetric extends Model
{
    /** @use HasFactory<HealthMetricFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'metric_type',
        'value',
        'secondary_value',
        'unit',
        'measured_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'metric_type' => HealthMetricType::class,
            'value' => 'decimal:2',
            'secondary_value' => 'decimal:2',
            'measured_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
