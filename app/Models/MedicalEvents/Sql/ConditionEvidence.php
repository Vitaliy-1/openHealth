<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperConditionEvidence
 */
class ConditionEvidence extends Model
{
    protected $guarded = [];

    protected $table = 'condition_evidences';

    protected $hidden = [
        'id',
        'condition_id',
        'codes_id',
        'details_id',
        'created_at',
        'updated_at'
    ];

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    public function codes(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'codes_id');
    }
}
