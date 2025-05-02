<?php

namespace App\Models\Relations;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperSpeciality
 */
class Speciality extends Model
{
    use HasFactory;
    use HasCamelCasing;

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
}
