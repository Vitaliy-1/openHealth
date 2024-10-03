<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function legalEntity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LegalEntity::class);
    }

    public function division(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function party(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function getUuid()
    {
        return $this->uuid;
    }

}
