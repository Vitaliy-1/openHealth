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
        'institutionName',
        'issuedDate',
        'degree',
        'diplomaNumber',
        'speciality',
        'issuedDate'
    ];

    public function science_degreeable()
    {
        return $this->morphTo();
    }
}
