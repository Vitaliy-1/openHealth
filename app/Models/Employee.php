<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;


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


    public function getUuid()
    {
        return $this->uuid;
    }


    //Scopes for employees type
    public function scopeDoctor($query){
        return $query->where('employee_type', 'DOCTOR');
    }

    //Get employee full name Split
    public function getFullNameAttribute()
    {
        return $this->party['first_name'] . ' ' . $this->party['last_name'] . ' ' . $this->party['second_name'];
    }
}
