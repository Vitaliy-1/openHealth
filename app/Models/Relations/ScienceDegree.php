<?php

namespace App\Models\Relations;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperScienceDegree
 */
class ScienceDegree extends Model
{
    use HasFactory;
    use HasCamelCasing;

    protected $hidden = [
        'id',
        'science_degreeable_id',
        'science_degreeable_type',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'country',
        'city',
        'institution_name',
        'issued_date',
        'degree',
        'diploma_number',
        'speciality',
    ];


    public function scienceDegreeable(): MorphTo
    {
        return $this->morphTo();
    }
}
