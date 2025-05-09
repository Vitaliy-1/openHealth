<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperImmunizationVaccinationProtocol
 */
class ImmunizationVaccinationProtocol extends Model
{
    use HasCamelCasing;

    protected $guarded = [];

    protected $hidden = [
        'id',
        'immunization_id',
        'authority_id',
        'created_at',
        'updated_at'
    ];

    public function immunization(): BelongsTo
    {
        return $this->belongsTo(Immunization::class);
    }

    public function authority(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'authority_id');
    }

    public function targetDiseases(): BelongsToMany
    {
        return $this->belongsToMany(
            CodeableConcept::class,
            'immunization_vaccination_protocol_target_diseases',
            'vaccination_protocol_id',
            'codeable_concept_id'
        );
    }
}
