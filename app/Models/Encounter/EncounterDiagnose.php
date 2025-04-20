<?php

declare(strict_types=1);

namespace App\Models\Encounter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperEncounterDiagnose
 */
class EncounterDiagnose extends Model
{
    protected $table = 'encounter_diagnose';
    protected $guarded = [];

    protected $hidden = [
        'id',
        'encounter_id',
        'condition_id',
        'role_id',
        'created_at',
        'updated_at',
    ];

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'condition_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'role_id');
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }
}
