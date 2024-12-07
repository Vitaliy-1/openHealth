<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    use HasFactory;


    protected $fillable = [
        'speciality',
        'speciality_officio',
        'level',
        'qualification_type',
        'attestation_name',
        'attestation_date',
        'valid_to_date',
        'certificate_number'
    ];
}
