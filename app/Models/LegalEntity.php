<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Relation;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalEntity extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'addresses',
        'archive',
        'beneficiary',
        'edrpou',
        'email',
        'is_active',
        'kveds',
        'legal_form',
        'mis_verified',
        'name',
        'nhs_verified',
        'owner_property_type',
        'phones',
        'public_name',
        'receiver_funds_code',
        'short_name',
        'status',
        'accreditation',
        'license',
        'type',
        'website',
    ];

    protected $casts = [
        'addresses' => 'json',
        'phones' => 'array',
        'archive' => 'json',
        'kveds' => 'array',
        'license' => 'array',
        'accreditation'=>'array',
    ];


    protected $attributes = [
        'is_active' => false,
        'owner_property_type' => '',
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Employee::class,'legal_entity_id','id');
    }

    public function setAddressesAttribute($value)
    {
        $this->attributes['addresses'] = json_encode($value);
    }

    public function setKvedsAttribute($value){
        $this->attributes['kveds'] = json_encode($value);
    }


    public function division(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(Division::class);
    }

}
