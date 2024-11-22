<?php

namespace App\Models\Relations;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Party extends Model
{
    use HasFactory;

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

    public function getAttribute($key)
    {
        $camelKey = lcfirst(str_replace('_', '', ucwords($key, '_')));

        return parent::getAttribute($camelKey) ?? parent::getAttribute($key);
    }

}
