<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{

    protected $fillable = [
        'uuid',
        'last_name',
        'first_name',
        'second_name',
        'person_id',
        'email',
        'birth_date',
        'gender',
        'tax_id',
        'no_tax_id',
        'documents',
        'phones',
        'educations',
        'qualifications',
        'specialities',
        'science_degree',
        'about_myself',
        'working_experience',
        'inserted_at',
        'inserted_by',
        'updated_at',
        'updated_by',

    ];

    protected $casts = [
        'documents' => 'array',
        'phones' => 'array',
        'educations' => 'array',
        'qualifications' => 'array',
        'specialities' => 'array',
        'science_degree' => 'array',
    ];

    public function employees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
