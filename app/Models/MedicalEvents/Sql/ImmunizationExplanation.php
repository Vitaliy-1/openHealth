<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperImmunizationExplanation
 */
class ImmunizationExplanation extends Model
{
    use HasCamelCasing;

    protected $guarded = [];

    protected $hidden = [
        'id',
        'immunization_id',
        'reasons_id',
        'reasons_not_given_id',
        'created_at',
        'updated_at'
    ];

    public function immunization(): BelongsTo
    {
        return $this->belongsTo(Immunization::class);
    }

    public function reasons(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'reasons_id');
    }

    public function reasonsNotGiven(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'reasons_not_given_id');
    }
}
