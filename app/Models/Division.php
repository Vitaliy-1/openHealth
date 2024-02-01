<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'external_id',
        'name',
        'type',
        'mountaint_group',
        'location',
        'addresses',
        'phones',
        'email',
        'working_hours',
        'is_active',
        'legal_entity_id',
        'status',
        'healthcare_services'
    ];

    protected $casts = [
        'location' => 'json',
        'addresses' => 'json',
        'healthcare_services' => 'json',
        'phones' => 'json',
        'working_hours' => 'json',
        'is_active' => 'boolean',
    ];

    public $attributes = [
        'is_active' => false,
        'mountaint_group' => false,
    ];

    public function legalEntity()
    {
        return $this->hasOne(LegalEntity::class);
    }

    public function healthcare_service()
    {
        return $this->hasMany(HealthcareService::class);
    }


}
