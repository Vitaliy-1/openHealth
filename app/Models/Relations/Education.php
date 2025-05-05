<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperEducation
 */
class Education extends Model
{
    use HasFactory, HasCamelCasing;

    protected $table = 'educations';

    protected $fillable = [
        'country',
        'city',
        'institution_name',
        'speciality',
        'degree',
        'issued_date',
        'diploma_number',
        'educationable_id',
        'educationable_type',
    ];

    public function educationable(): MorphTo
    {
        return $this->morphTo();
    }
}
