<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\HasPersonalAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;
    use HasPersonalAttributes;


    protected $fillable = [
        'uuid',
        'legal_entity_uuid',
        'division_uuid',
        'person_id',
        'legal_entity_id',
        'status',
        'position',
        'start_date',
        'end_date',
        'employee_type',
        'party',
        'doctor',
    ];

    protected $casts = [
        'party' => 'array',
        'doctor' => 'array',
        'speciality' => 'array',
        'status' => Status::class,
    ];

    protected $attributes = [
        'doctor' => '{}',
    ];


    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function legalEntity(): BelongsTo
    {
        return $this->belongsTo(LegalEntity::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function declarations(): HasMany
    {
        return $this->hasMany(Declaration::class);
    }

    //Scopes for employees type
    public function scopeDoctor($query)
    {
        return $query->where('employee_type', 'DOCTOR');
    }

}
