<?php

namespace App\Models;

use App\Models\Relations\Address;
use App\Models\Relations\AuthenticationMethod;
use App\Models\Relations\ConfidantPerson;
use App\Models\Relations\Document;
use App\Models\Relations\Phone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PersonRequest extends Model
{
    protected $fillable = [
        'uuid',
        'status',
        'first_name',
        'last_name',
        'second_name',
        'birth_date',
        'birth_country',
        'birth_settlement',
        'gender',
        'email',
        'no_tax_id',
        'tax_id',
        'secret',
        'unzr',
        'emergency_contact',
        'patient_signed',
        'process_disclosure_data_consent',
        'authorize_with'
    ];

    protected $casts = [
        'emergency_contact' => 'array'
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    public function authenticationMethod(): HasOne
    {
        return $this->hasOne(AuthenticationMethod::class);
    }

    public function confidantPerson(): HasOne
    {
        return $this->hasOne(ConfidantPerson::class);
    }
}
