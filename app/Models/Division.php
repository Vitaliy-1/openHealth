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
        'location' => 'array',
        'addresses' => 'array',
        'healthcare_services' => 'array',
        'phones' => 'array',
        'working_hours' => 'array',
        'is_active' => 'boolean',
    ];

    public $attributes = [
        'is_active' => false,
        'mountaint_group' => false,
        'uuid' => 'string',
        'addresses' => '[]',
        'location' => '[]',
        'working_hours' =>  '[]',
    ];

    public function legalEntity()
    {
        return $this->hasOne(LegalEntity::class);
    }

    public function healthcareService()
    {
        return $this->hasMany(HealthcareService::class);
    }

    public function setLocationAttribute($value)
    {
        $this->attributes['location'] = $value ?: json_encode([]);
    }

}
