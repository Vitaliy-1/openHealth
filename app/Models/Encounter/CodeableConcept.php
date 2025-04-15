<?php

declare(strict_types=1);

namespace App\Models\Encounter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperCodeableConcept
 */
class CodeableConcept extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'id',
        'pivot',
        'codeable_conceptable_type',
        'codeable_conceptable_id',
        'created_at',
        'updated_at',
    ];

    public function codeableConceptable(): MorphTo
    {
        return $this->morphTo();
    }

    public function coding(): MorphMany
    {
        return $this->morphMany(Coding::class, 'codeable');
    }

    public function encounters(): BelongsToMany
    {
        return $this->belongsToMany(Encounter::class, 'encounter_reason');
    }
}
