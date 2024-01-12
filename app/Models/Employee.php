<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;


    protected $fillable = [
        'employee_uuid',
        'person_id',
        'legal_entity_id',
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
    ];

    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function legalEntity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LegalEntities::class);
    }

}
