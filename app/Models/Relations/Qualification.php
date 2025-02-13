<?php

namespace App\Models\Relations;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperQualification
 */
class Qualification extends Model
{
    use HasFactory;
    use HasCamelCasing;

    protected $fillable = [
        'type',
        'institutionName',
        'speciality',
        'issuedDate',
        'certificateNumber',
        'validTo',
        'additionalInfo',
    ];

    public function qualificationable(): MorphTo
    {
        return $this->morphTo();
    }
}
