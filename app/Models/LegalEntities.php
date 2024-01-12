<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalEntities extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_entities_uuid',
        'name',
        'short_name',
        'public_name',
        'type',
        'owner_property_type',
        'legal_form',
        'edrpou',
        'kveds',
        'addresses',
        'phones',
        'email',
        'licenses',
        'is_active',
        'mis_verified',
        'nhs_verified',
        'website',
        'beneficiary',
        'receiver_funds_code',
        'archive',
    ];

    protected $casts = [
        'addresses' => 'array',
        'phones' => 'array',
        'archive' => 'array',
        'kveds' => 'array',
        'licenses' => 'array',
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Employee::class,'legal_entity_id','id');
    }

    public static function saveOrUpdate(array $condition,array $attributes): object
    {
        return self::updateOrCreate($condition, $attributes);
    }



}
