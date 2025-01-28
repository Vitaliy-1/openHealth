<?php

namespace App\Models;

use App\Enums\Declaration\DeclarationStatus;
use App\Models\Employee\Employee;
use App\Models\Person\Person;
use App\Traits\FormTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperDeclaration
 */
class Declaration extends Model
{
    use HasFactory,FormTrait;

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
        'legal_entity' => 'object',
        'inserted_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => DeclarationStatus::class, // enum
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
     * Get the FullNameAttribute for the Declaration person.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        // Use the 'person' array to get the first, middle, and last names
        return ($this->person['first_name'] ?? '') . ' ' . ($this->person['last_name'] ?? '') . ' ' . ($this->person['second_name'] ?? '');
    }

    /**
     * Get BirthDateAttribute for the Declaration person.
     * @return string
     */
    public function getBirthDateAttribute(): string
    {
        return humanFormatDate($this->person['birth_date'] ?? '');
    }

    /**
     * Get StartDateAttributeS for the Declaration person.
     * @return string
     */
    public function getStartDateDeclarationAttribute()
    {
        return humanFormatDate($this->start_date ?? '');
    }

    /**
     * Get EndDateAttribute for the Declaration person.
     * @return string
     */
    public function getEndDateDeclarationAttribute(): string
    {
        return humanFormatDate($this->end_date ?? '');
    }

    /**
     * Get Full name Doctor for the Declaration person.
     * @return string
     */

    public function getDoctorFullNameAttribute(): string {
        return ($this->employee['party']['first_name'] ?? '') . ' ' . ($this->employee['party']['last_name'] ?? '') . ' ' . ($this->employee['party']['second_name'] ?? '');
    }

    public function getPhoneAttribute(): string
    {
        return ($this->person['phones'][0]['number'] ?? '');
    }
}

