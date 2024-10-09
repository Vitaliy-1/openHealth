<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Declaration extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'declaration_number',
        'start_date',
        'end_date',
        'signed_at',
        'person',
        'employee',
        'division',
        'legal_entity',
        'status',
        'scope',
        'declaration_request_id',
        'inserted_at',
        'updated_at',
        'reason',
        'reason_description',
        'person_id',
        'employee_id',
        'division_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_at' => 'datetime',
        'person' => 'array',
        'employee' => 'array',
        'division' => 'array',
        'legal_entity' => 'array',
        'inserted_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Define a relationship with the Person model.
     *
     * @return BelongsTo
     */

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Define a relationship with the Employee model.
     *
     * @return BelongsTo
     */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Define a relationship with the Division model.
     *
     * @return BelongsTo
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }


    /**
     * Get the full name attribute for the person.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return ($this->person['first_name'] ?? '') . ' ' . ($this->person['last_name'] ?? '') . ' ' . ($this->person['second_name'] ?? '');
    }
}

