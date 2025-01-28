<?php

namespace App\Models;

use App\Casts\Division\Location;
use App\Casts\Division\WorkingHours;
use App\Models\Relations\Address;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin IdeHelperDivision
 */
class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'external_id',
        'name',
        'type',
        'mountain_group',
        'location',
        'phones',
        'email',
        'working_hours',
        'is_active',
        'legal_entity_id',
        'status',
        'healthcare_services'
    ];

    protected $casts = [
        'location' => Location::class,
        'healthcare_services' => 'array',
        'phones' => 'array',
        'working_hours' => WorkingHours::class,
        'is_active' => 'boolean',
    ];

    public $attributes = [
        'is_active' => false,
        'mountain_group' => false,
        'uuid' => 'string'
    ];

    public function legalEntity(): HasOne
    {
        return $this->hasOne(LegalEntity::class);
    }

    public function healthcareService(): HasMany
    {
        return $this->hasMany(HealthcareService::class);
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
