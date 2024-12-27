<?php

namespace App\Models\Relations;

use App\Models\Employee\BaseEmployee;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Party extends Model
{
    use HasFactory;
    use HasCamelCasing;


    protected $fillable = [
        'uuid',
        'lastName',
        'firstName',
        'secondName',
        'email',
        'birthDate',
        'gender',
        'taxId',
        'noTaxId',
        'aboutMyself',
        'workingExperience',
    ];

    public $timestamps = false;

    public function employees(): HasMany
    {
        return $this->hasMany(BaseEmployee::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

}
