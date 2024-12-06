<?php

namespace App\Models\Relations;

use App\Models\Employee;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Party extends Model
{
    use HasFactory;
    use HasCamelCasing;


    protected $fillable = [
        'uuid',
        'lasName',
        'firstName',
        'secondName',
        'email',
        'birthDate',
        'gender',
        'taxId',
        'noТaxId',
        'aboutMyself',
        'workingExperience',
    ];

    public $timestamps = false;

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
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
