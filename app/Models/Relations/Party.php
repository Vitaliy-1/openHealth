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
        'last_name',
        'first_name',
        'second_name',
        'email',
        'birth_date',
        'gender',
        'tax_id',
        'no_tax_id',
        'about_myself',
        'working_experience',
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
