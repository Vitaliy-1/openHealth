<?php

namespace App\Models;

use App\Models\Relations\Phone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Person extends Model
{
    protected $table = 'persons';

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'second_name',
        'birth_date',
        'gender',
        'tax_id',
        'birth_settlement',
        'birth_country'
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    public function personRequest(): HasOne
    {
        return $this->hasOne(PersonRequest::class);
    }
}
