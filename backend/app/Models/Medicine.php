<?php

namespace App\Models;

use Database\Factories\MedicineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    /** @use HasFactory<MedicineFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'generic_name',
        'description',
        'dosage_form',
        'strength',
        'manufacturer',
        'status',
    ];

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
