<?php

namespace App\Models\Relations;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin IdeHelperSpeciality
 */
class Speciality extends Model
{
    use HasFactory;
    use HasCamelCasing;

    protected $hidden = [
        'id',
        'specialityable_id',
        'specialityable_type',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'speciality',
        'speciality_officio',
        'level',
        'qualification_type',
        'attestation_name',
        'attestation_date',
        'valid_to_date',
        'certificate_number'
    ];

    public function specialityable(): MorphTo
    {
        return $this->morphTo();
    }

    public function qualifications(): MorphMany
    {
        return $this->morphMany(Qualification::class, 'qualificationable');
    }
}
