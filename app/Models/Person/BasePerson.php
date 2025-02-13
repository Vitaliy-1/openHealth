<?php

namespace App\Models\Person;

use App\Models\Relations\Address;
use App\Models\Relations\AuthenticationMethod;
use App\Models\Relations\ConfidantPerson;
use App\Models\Relations\Document;
use App\Models\Relations\Phone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin IdeHelperBasePerson
 */
class BasePerson extends Model
{
    protected $hidden = [
        'id'
    ];

    protected $fillable = [
        'uuid',
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
        'process_disclosure_data_consent'
    ];

    protected $casts = [
        'emergency_contact' => 'array'
    ];

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    public function authenticationMethod(): MorphMany
    {
        return $this->morphMany(AuthenticationMethod::class, 'authenticatable');
    }

    public function confidantPerson(): HasOne
    {
        return $this->hasOne(ConfidantPerson::class);
    }
}
