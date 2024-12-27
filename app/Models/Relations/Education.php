<?php

namespace App\Models\Relations;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function educationable()
    {
        return $this->morphTo();
    }

}
