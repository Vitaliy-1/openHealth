<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperClinicalImpressionFinding
 */
class ClinicalImpressionFinding extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'id',
        'clinical_impression_id',
        'item_reference_id',
        'created_at',
        'updated_at'
    ];

    public function itemReference(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'item_reference_id');
    }
}
