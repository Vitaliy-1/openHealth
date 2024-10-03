<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LegalEntity extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'accreditation',
        'archive',
        'beneficiary',
        'edr',
        'edr_verified',
        'edrpou',
        'email',
        'inserted_at',
        'inserted_by',
        'is_active',
        'license',
        'nhs_comment',
        'nhs_reviewed',
        'nhs_verified',
        'phones',
        'receiver_funds_code',
        'residence_address',
        'status',
        'type',
        'updated_at',
        'updated_by',
        'website',
        'client_id',
        'client_secret',
    ];

    protected $casts = [
        'accreditation' => 'array',
        'archive' => 'array',
        'edr' => 'array',
        'license' => 'array',
        'phones' => 'array',
        'residence_address' => 'array',
        'inserted_at' => 'datetime',
        'updated_at' => 'datetime',
        'id' => 'string',
        'inserted_by' => 'string',
        'updated_by' => 'string',
    ];

    protected $attributes = [
        'is_active' => false,
    ];

    public null|object $owner;

    public function employees(): \Illuminate\Database\Eloquent\Relations\HasMany
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

    public function contract(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contract::class,'legal_entity_id','id');
    }



    public function getId():int
    {
        return $this->id;
    }


    // Get Legal Entity UUID
    public function getUuid():string
    {
        return $this->uuid;
    }

    public function getClientId():string
    {
        return $this->client_id;
    }


    // Get Owner Legal Entity
    public function getOwner():object
    {
            return $this->employees->where('employee_type', 'OWNER')->first();
    }

    public function getActiveDivisions():Collection
    {
        return $this->division()->has('healthcareService')->where('status', 'ACTIVE')->get();
    }

    public function getEdr():array
    {
        return $this->edr;
    }



}
