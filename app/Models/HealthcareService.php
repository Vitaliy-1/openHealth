<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperHealthcareService
 */
class HealthcareService extends Model
{
    use HasFactory;

    protected $fillable = [
        'speciality_type',
        'providing_condition',
        'license_id',
        'category',
        'type',
        'comment',
        'coverage_area',
        'available_time',
        'not_available',
        'status',
    ];

    protected $casts = [
        'category' => 'json',
        'type' => 'json',
        'coverage_area' => 'json',
        'available_time' => 'json',
        'not_available' => 'json',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function getHealthcareCategoryAttribute()
    {
        return $this->category['coding'][0]['code'] ?? '';
    }
}
