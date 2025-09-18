<?php

declare(strict_types=1);

namespace App\Models\Relations;

use App\Models\User;
use App\Models\Employee\Employee;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee\EmployeeRequest;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin IdeHelperParty
 */
class Party extends Model
{
    use HasCamelCasing;

    protected $fillable = [
        'uuid',
        'last_name',
        'first_name',
        'second_name',
        'email',
        'birth_date',
        'gender',
        'user_id',
        'tax_id',
        'no_tax_id',
        'about_myself',
        'working_experience',
        'declaration_count',
        'declaration_limit',
    ];

    protected $casts = [
        'birth_date' => 'date:Y-m-d',
    ];

    public $timestamps = false;

    /**
     * Get the party's full name.
     * This is an accessor, allowing you to use it like a property: $party->fullName
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        $fullName = trim($this->last_name . ' ' . $this->first_name);

        if (!empty($this->second_name)) {
            $fullName .= ' ' . $this->second_name;
        }

        return $fullName;
    }

    /**
     * Get the user that owns the party.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'party_id');
    }

    public function employeeRequests(): HasMany
    {
        return $this->hasMany(EmployeeRequest::class, 'party_id');
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
