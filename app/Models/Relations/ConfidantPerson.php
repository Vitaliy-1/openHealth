<?php

namespace App\Models\Relations;

use App\Models\Person\PersonRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin IdeHelperConfidantPerson
 */
class ConfidantPerson extends Model
{
    protected $table = 'confidant_persons';

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'person_request_id',
        'person_id',
        'documents_relationship',
        'person_uuid',
        'first_name',
        'last_name',
        'second_name',
        'gender',
        'birth_date',
        'birth_country',
        'birth_settlement',
        'tax_id',
        'birth_certificate'
    ];

    protected $casts = [
        'documents_relationship' => 'array'
    ];

    public function personRequest(): BelongsTo
    {
        return $this->belongsTo(PersonRequest::class);
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }
}
