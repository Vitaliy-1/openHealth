<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperQualification
 */
class Qualification extends Model
{
    use HasFactory;
    use HasCamelCasing;

    protected $fillable = [
        'type',
        'institution_name',
        'speciality',
        'issued_date',
        'certificate_number',
        'valid_to',
        'additional_info',
    ];

    public function qualificationable(): MorphTo
    {
        return $this->morphTo();
    }
}
