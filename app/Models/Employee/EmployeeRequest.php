<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Casts\EHealthDateCast;
use App\Enums\Employee\RequestStatus;
use App\Models\Revision;
use App\Models\Relations\ScienceDegree;
use App\Models\Relations\Education;
use App\Models\Relations\Qualification;
use App\Models\Relations\Speciality;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Represents a request to create or modify an employee.
 * Inherits common properties from BaseEmployee.
 */
class EmployeeRequest extends BaseEmployee
{
    protected $table = 'employee_requests';

    /**
     * The attributes that are mass assignable.
     * Extends the list from the parent BaseEmployee class.
     */
    protected $fillable = [
        'uuid',
        'legal_entity_uuid',
        'division_uuid',
        'legal_entity_id',
        'status',
        'position',
        'start_date',
        'end_date',
        'party_id',
        'employee_type',
        'user_id',
        'division_id',
        'inserted_at',
        'applied_at',
        'employee_id',
        'email',
    ];

    /**
     * The attributes that should be cast.
     * Extends the casts from the parent BaseEmployee class.
     */
    protected $casts = [
        'status' => RequestStatus::class,
        'start_date' => EHealthDateCast::class,
        'end_date' => EHealthDateCast::class,
        'applied_at' => 'datetime',
    ];

    // --- REQUEST-SPECIFIC RELATIONS ---

    /**
     * The employee this request is associated with (can be null for new employees).
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function revision(): MorphOne
    {
        return $this->morphOne(Revision::class, 'revisionable');
    }

    public function scienceDegree(): MorphOne
    {
        return $this->morphOne(ScienceDegree::class, 'science_degreeable');
    }

    public function educations(): MorphMany
    {
        return $this->morphMany(Education::class, 'educationable');
    }

    public function qualifications(): MorphMany
    {
        return $this->morphMany(Qualification::class, 'qualificationable');
    }

    public function specialities(): MorphMany
    {
        return $this->morphMany(Speciality::class, 'specialityable');
    }

    // --- TEMPORARY SCOPES (to be removed after controller refactoring) ---

    public function scopeEmployeeInstance(Builder $query, int $userId, string $legalEntityUUID, array $roles, bool $isInclude = false): void
    {
        $query->where('user_id', $userId)
            ->where('legal_entity_uuid', $legalEntityUUID)
            ->when(
                $isInclude,
                fn ($q) => $q->whereIn('employee_type', $roles),
                fn ($q) => $q->whereNotIn('employee_type', $roles)
            );
    }

    /**
     * Scope a query to only include pending (NEW) requests.
     */
    public function scopePending(Builder $query): void
    {
        $query->where('status', RequestStatus::NEW);
    }
}
