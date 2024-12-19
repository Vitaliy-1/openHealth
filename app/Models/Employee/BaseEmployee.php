<?php

namespace App\Models\Employee;

use App\Enums\Status;
use App\Models\Declaration;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Models\Relations\Education;
use App\Models\Relations\Party;
use App\Models\Relations\Qualification;
use App\Models\Relations\ScienceDegree;
use App\Models\Relations\Speciality;
use App\Traits\HasPersonalAttributes;
use Carbon\Carbon;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property string $uuid
 * @property string $legal_entity_uuid
 * @property string $division_uuid
 * @property int $person_id
 * @property Status $status
 * @property string $position
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string $employee_type
 */
class BaseEmployee extends Model
{
    use HasFactory;
    use HasPersonalAttributes;
    use HasCamelCasing;


    protected $hidden = ['id'];

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
    ];

    protected $casts = [
        'status'    => Status::class,
        'startDate' => 'datetime',
        'endDate'   => 'datetime',
    ];

    protected array $prettyAttributes = [
        'startDate',
        'endDate',
        'status',
        'position',
        'employeeType',
    ];

    protected $with = [
        'party',
        'party.phones',
        'party.documents',
        'qualifications',
        'educations',
        'specialities',
        'scienceDegrees'
    ];


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



}
