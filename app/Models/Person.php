<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'last_name',
        'first_name',
        'second_name',
        'birth_date',
        'birth_country',
        'birth_settlement',
        'gender',
        'email',
        'tax_id',
        'invalid_tax_id',
        'is_active',
        'documents',
        'addresses',
        'phones',
        'emergency_contact',
        'patient_signed',
        'secret',
        'process_disclosure_data_consent',
        'preferred_way_communication',
        'authentication_methods',
        'uuid',
    ];

    protected $casts = [
        'documents' => 'array',
        'phones' => 'array',
        'educations' => 'array',
        'specialities' => 'array',
        'qualifications' => 'array',
        'science_degree' => 'array',
        'emergency_contact' => 'array',
        'confidant_person' => 'array',
        'authentication_methods' => 'array',
    ];


    protected $attributes = [
        'documents' => '{}',
        'tax_id' => '',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\hasOne
    {
        return $this->hasOne(User::class);
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Employee::class);
    }





}
