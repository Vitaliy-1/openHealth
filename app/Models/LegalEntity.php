<?php

namespace App\Models;

use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRequest;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Relations\Address;
use App\Models\Relations\Phone;

/**
 * @mixin IdeHelperLegalEntity
 */
class LegalEntity extends Model
{
    use HasCamelCasing,
        HasFactory;

    public const string TYPE_PRIMARY_CARE = 'PRIMARY_CARE';

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
        'nhs_comment',
        'nhs_reviewed',
        'nhs_verified',
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
        'residence_address' => 'array',
        'inserted_at' => 'datetime',
        'updated_at' => 'datetime',
        'id' => 'string',
        'inserted_by' => 'string',
        'updated_by' => 'string',
    ];

    protected $with = [
        'licenses',
        'address',
        'phones'
    ];

    protected $attributes = [
        'is_active' => false,
    ];

    public null|object $owner;

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function employeesRequest(): HasMany
    {
        return $this->hasMany(EmployeeRequest::class);
    }

    public function setAddressesAttribute($value)
    {
        $this->attributes['addresses'] = json_encode($value);
    }

    public function setKvedsAttribute($value)
    {
        $this->attributes['kveds'] = json_encode($value);
    }

    public function division(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function contract(): HasMany
    {
        return $this->hasMany(Contract::class, 'legal_entity_id', 'id');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    // Get Legal Entity UUID
    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getClientId(): string
    {
        return $this->client_id;
    }

    // Get Owner Legal Entity
    public function getOwner(): ?object
    {
        return $this->employees()->where('employee_type', 'OWNER')->first();
    }

    public function getActiveDivisions(): Collection
    {
        return $this->division()->has('healthcareService')->where('status', 'ACTIVE')->get();
    }

    public function getEdr(): array
    {
        return $this->edr;
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }
}
