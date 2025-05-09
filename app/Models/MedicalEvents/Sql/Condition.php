<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperCondition
 */
class Condition extends Model
{
    use HasCamelCasing;

    protected $guarded = [];

    protected $appends = ['evidences'];

    protected $hidden = [
        'id',
        'encounter_id',
        'asserter_id',
        'report_origin_id',
        'context_id',
        'code_id',
        'severity_id',
        'created_at',
        'updated_at',
    ];

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function asserter(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'asserter_id');
    }

    public function reportOrigin(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'report_origin_id');
    }

    public function context(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'context_id');
    }

    public function code(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'code_id');
    }

    public function severity(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'severity_id');
    }

    public function evidencesRelation(): HasMany
    {
        return $this->hasMany(ConditionEvidence::class, 'condition_id');
    }

    public function evidences(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'codes' => $this->evidencesRelation()
                    ->with(['codes.coding'])
                    ->get()
                    ->map(fn (ConditionEvidence $evidence) => $evidence->codes->toArray())
                    ->toArray()
            ]
        );
    }
}
