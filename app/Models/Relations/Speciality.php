<?php

namespace App\Models\Relations;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSpeciality
 */
class Speciality extends Model
{
    use HasFactory;
    use HasCamelCasing;

    protected $fillable = [
        'speciality',
        'specialityOfficio',
        'level',
        'qualificationType',
        'attestationName',
        'attestationDate',
        'validToDate',
        'certificateNumber'
    ];
}
