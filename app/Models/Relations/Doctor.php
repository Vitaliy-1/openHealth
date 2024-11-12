<?php

namespace App\Models\Relations;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Doctor extends Model
{
    use HasFactory;


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function qualifications(): MorphMany
    {
        return $this->morphMany(Qualification::class, 'qualifiable');
    }

    public function educations(): MorphMany
    {
        return $this->morphMany(Education::class, 'educatable');
    }

    public function scienceDegrees(): MorphMany
    {
        return $this->morphMany(ScienceDegree::class, 'science_degreeable');
    }

    public function specialities(): MorphMany
    {
        return $this->morphMany(Speciality::class, 'specialityable');
    }
}
