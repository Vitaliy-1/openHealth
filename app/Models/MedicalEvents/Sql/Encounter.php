<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin IdeHelperEncounter
 */
class Encounter extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'id',
        'person_id',
        'visit_id',
        'episode_id',
        'class_id',
        'type_id',
        'priority_id',
        'performer_id',
        'division_id',
        'created_at',
        'updated_at',
    ];

    public function period(): MorphOne
    {
        return $this->morphOne(Period::class, 'periodable');
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'visit_id');
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'episode_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Coding::class, 'class_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'type_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'priority_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'performer_id');
    }

    public function reasons(): BelongsToMany
    {
        return $this->belongsToMany(CodeableConcept::class, 'encounter_reason');
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(EncounterDiagnose::class)->with(['condition.type.coding', 'role.coding']);
    }

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(CodeableConcept::class, 'encounter_action');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'division_id');
    }
}
