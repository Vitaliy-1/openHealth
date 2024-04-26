<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'category' => 'json',
        'type' => 'json',
        'coverage_area' => 'json',
        'available_time' => 'json',
        'not_available' => 'json',
    ];

    protected  $attributes = [
        'status' => false,
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }


}
