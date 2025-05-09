<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperImmunization
 */
class Immunization extends Model
{
    use HasCamelCasing;

    protected $guarded = [];

    protected $appends = ['explanation'];

    protected $hidden = [
        'id',
        'encounter_id',
        'vaccine_code_id',
        'context_id',
        'performer_id',
        'report_origin_id',
        'site_id',
        'route_id',
        'created_at',
        'updated_at'
    ];

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function context(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'context_id');
    }

    public function vaccineCode(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'vaccine_code_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'performer_id');
    }

    public function reportOrigin(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'report_origin_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'site_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'route_id');
    }

    public function doseQuantity(): HasOne
    {
        return $this->hasOne(ImmunizationDoseQuantity::class, 'immunization_id');
    }

    public function explanations(): HasMany
    {
        return $this->hasMany(ImmunizationExplanation::class, 'immunization_id');
    }

    protected function explanation(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'reasons' => $this->explanations()
                    ->with(['reasons.coding'])
                    ->get()
                    ->pluck('reasons')
                    ->flatten()
                    ->filter()
                    ?->toArray() ?: [['text' => null, 'coding' => [['system' => 'eHealth/reason_explanations', 'code' => '']]]],
                'reasonsNotGiven' => $this->explanations()
                    ->with(['reasonsNotGiven.coding'])
                    ->get()
                    ->pluck('reasonsNotGiven')
                    ->flatten()
                    ->filter()
                    ->first()
                    ?->toArray() ?: ['text' => null, 'coding' => [['system' => 'eHealth/reason_not_given_explanations', 'code' => '']]]
            ]
        );
    }

    public function vaccinationProtocols(): HasMany
    {
        return $this->hasMany(ImmunizationVaccinationProtocol::class, 'immunization_id');
    }
}
