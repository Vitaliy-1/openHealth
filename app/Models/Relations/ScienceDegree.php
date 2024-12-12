<?php

namespace App\Models\Relations;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScienceDegree extends Model
{
    use HasFactory;
    use HasCamelCasing;

    protected $fillable = [
        'country',
        'city',
        'institution_name',
        'issued_date',
        'diploma_number',
        'speciality',
        'issued_date'
    ];

    public function science_degreeable()
    {
        return $this->morphTo();
    }
}
