<?php

namespace App\Models\Relations;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function qualificationable()
    {
        return $this->morphTo();
    }
}
