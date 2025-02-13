<?php

namespace App\Models\Relations;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperEducation
 */
class Education extends Model
{
    use HasFactory;
    use HasCamelCasing;

    protected $fillable = [
        'country',
        'city',
        'institutionName',
        'issuedDate',
        'diplomaNumber',
        'degree',
        'speciality',
    ];

    protected $table = 'educations';//TODO: Перевірити чому laravel підтягую назву таблиці education

    public function educationable(): MorphTo
    {
        return $this->morphTo();
    }
}
