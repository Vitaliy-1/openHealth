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
        'party.birth_date' => 'datestamp',
        'doctor' => 'array',
        'speciality' => 'array',
    ];

    protected $attributes = [
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

}
