<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Models\User;
use App\Enums\Status;
use App\Models\Division;
use App\Models\Declaration;
use App\Models\LegalEntity;
use App\Models\Relations\Party;
use App\Models\Relations\Education;
use App\Models\Relations\Speciality;
use App\Models\Relations\Qualification;
use App\Models\Relations\ScienceDegree;
use Illuminate\Database\Eloquent\Model;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperBaseEmployee
 */
class BaseEmployee extends Model
{
    use HasFactory;
    use HasCamelCasing;

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
        'division_id'
    ];

    protected $casts = [
        'status' => Status::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    protected array $prettyAttributes = [
        'start_date',
        'end_date',
        'status',
        'position',
        'employee_type'
    ];

    protected $with = [
        'user',
        'party',
        'party.phones',
        'party.documents',
        'qualifications',
        'educations',
        'specialities',
        'scienceDegrees'
    ];

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            optional($this->party)->first_name ?? '',
            optional($this->party)->last_name ?? '',
            optional($this->party)->second_name?? '',
        ]));
    }

    public function getPhoneAttribute(): string
    {
        return optional(optional($this->party)->phones)->first()->number ?? '';
    }

    public function getBirthDateAttribute(): string
    {
        return humanFormatDate(optional($this->party)->birth_date ?? '');
    }

    public function getEmailAttribute(): string
    {
        return optional($this->party)->email ?? '';
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function declarations(): HasMany
    {
        return $this->hasMany(Declaration::class);
    }

    public function educations(): MorphMany
    {
        return $this->morphMany(Education::class, 'educationable');
    }

    public function scienceDegrees(): MorphMany
    {
        return $this->morphMany(ScienceDegree::class, 'science_degreeable');
    }

    public function qualifications(): MorphMany
    {
        return $this->morphMany(Qualification::class, 'qualificationable');
    }

    public function specialities(): MorphMany
    {
        return $this->morphMany(Speciality::class, 'specialityable');
    }

    public function scopeDoctor($query)
    {
        return $query->where('employee_type', 'DOCTOR');
    }

    public function scopeShowEmployee($query, $id)
    {
        $employeeData = $query->findOrFail($id);
        foreach ($this->prettyAttributes as $attribute) {
            $employeeData->party->{$attribute} = $employeeData->{$attribute} ?? '';
        }
        $employeeData->documents = $employeeData->party->documents()->get()->toArray() ?? [];

        return $employeeData->toArray();
    }

    /**
     * Scope a query to get an employee data depends on user id and it's legal entity relation
     *
     * @param Builder $query
     * @param int $userId User's ID from MIS DB table 'users'
     * @param string $legalEntityUUID UUID of the LegalEntity when user (with $userId) is belongs to
     * @param array $roles Specify ROLEs that shouldn't be inclued in query
     *
     * @return void
     */
    public function scopeEmployeeInstance(Builder $query, int $userId, string $legalEntityUUID, array $roles, bool $isInclude = false): void
    {
        $query->where('user_id', $userId)
            ->where('legal_entity_uuid', $legalEntityUUID);

            if ($isInclude) {
                $query->whereIn('employee_type', $roles);
            } else {
                $query->whereNotIn('employee_type', $roles);
            }
    }
}
