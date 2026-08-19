<?php

namespace App\Models;

use Database\Factories\VitalSignFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalSign extends Model
{
    /** @use HasFactory<VitalSignFactory> */
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'systolic_blood_pressure',
        'diastolic_blood_pressure',
        'heart_rate',
        'body_temperature',
        'blood_oxygen',
        'respiratory_rate',
        'weight',
        'height',
        'blood_glucose',
        'measured_at',
    ];

    protected function casts(): array
    {
        return [
            'measured_at' => 'datetime',
            'body_temperature' => 'decimal:1',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
            'blood_glucose' => 'decimal:1',
            'systolic_blood_pressure' => 'integer',
            'diastolic_blood_pressure' => 'integer',
            'heart_rate' => 'integer',
            'blood_oxygen' => 'integer',
            'respiratory_rate' => 'integer',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
