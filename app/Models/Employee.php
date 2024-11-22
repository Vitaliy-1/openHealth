<?php

namespace App\Models;

use App\Enums\Status;
use App\Models\Relations\Education;
use App\Models\Relations\Party;
use App\Models\Relations\Qualification;
use App\Models\Relations\ScienceDegree;
use App\Models\Relations\Speciality;
use App\Traits\HasPersonalAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
    ];

    protected $casts = [
        'status' => Status::class,
    ];


    protected $with = [
        'party',
        'party.phones',
        'party.documents',
        'qualifications',
        'educations',
        'specialities',
        'scienceDegrees',
    ];

    public function getAttribute($key)
    {
        $camelKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
        return parent::getAttribute($camelKey) ?? parent::getAttribute($key);
    }


    //TODO: Подивитись чи використовувати person в Employee
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

    public function educations(): morphMany
    {
        return $this->morphMany(Education::class, 'educationable');
    }

    public function scienceDegrees():morphMany
    {
        return $this->morphMany(ScienceDegree::class, 'science_degreeable');
    }


    public function qualifications(): morphMany
    {
        return $this->morphMany(Qualification::class, 'qualificationable');
    }

    public function specialities(): morphMany
    {
        return $this->morphMany(Speciality::class, 'specialityable');
    }


    public function scopeRelated($query){
        $query->with([ 'party', 'legalEntity', 'division', 'educations', 'scienceDegrees', 'qualifications', 'specialities']);
    }


    public function scopeDoctor($query)
    {
        return $query->where('employee_type', 'DOCTOR');
    }

}
