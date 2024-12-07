<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'institution_name',
        'speciality',
        'issued_date',
        'certificate_number',
        'valid_to',
        'additional_info',
    ];

    public function qualificationable()
    {
        return $this->morphTo();
    }
}
