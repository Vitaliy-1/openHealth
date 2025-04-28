<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperCondition
 */
class Condition extends Model
{
    protected $guarded = [];

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
}
