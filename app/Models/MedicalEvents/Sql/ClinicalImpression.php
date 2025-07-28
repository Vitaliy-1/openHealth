<?php

declare(strict_types=1);

namespace App\Models\MedicalEvents\Sql;

use Carbon\CarbonImmutable;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin IdeHelperClinicalImpression
 */
class ClinicalImpression extends Model
{
    use HasCamelCasing;

    protected $guarded = [];

    protected $hidden = [
        'id',
        'encounter_internal_id',
        'code_id',
        'encounter_id',
        'assessor_id',
        'previous_id',
        'created_at',
        'updated_at'
    ];

    protected $appends = [
        'effective_period_start_date',
        'effective_period_start_time',
        'effective_period_end_date',
        'effective_period_end_time'
    ];

    protected function effectivePeriodStartDate(): Attribute
    {
        return Attribute::make(
            get: fn () => isset($this->effectivePeriod['start'])
                ? CarbonImmutable::parse($this->effectivePeriod['start'])->toDateString()
                : null
        );
    }

    protected function effectivePeriodStartTime(): Attribute
    {
        return Attribute::make(
            get: fn () => isset($this->effectivePeriod['start'])
                ? CarbonImmutable::parse($this->effectivePeriod['start'])->toTimeString()
                : null
        );
    }

    protected function effectivePeriodEndDate(): Attribute
    {
        return Attribute::make(
            get: fn () => isset($this->effectivePeriod['end'])
                ? CarbonImmutable::parse($this->effectivePeriod['end'])->toDateString()
                : null
        );
    }

    protected function effectivePeriodEndTime(): Attribute
    {
        return Attribute::make(
            get: fn () => isset($this->effectivePeriod['end'])
                ? CarbonImmutable::parse($this->effectivePeriod['end'])->toTimeString()
                : null
        );
    }

    public function code(): BelongsTo
    {
        return $this->belongsTo(CodeableConcept::class, 'code_id');
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'encounter_id');
    }

    public function effectivePeriod(): MorphOne
    {
        return $this->morphOne(Period::class, 'periodable');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'assessor_id');
    }

    public function previous(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'previous_id');
    }

    public function problems(): BelongsToMany
    {
        return $this->belongsToMany(Identifier::class, 'clinical_impression_problems');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(ClinicalImpressionFinding::class);
    }

    public function supportingInfo(): BelongsToMany
    {
        return $this->belongsToMany(Identifier::class, 'clinical_impression_supporting_info');
    }
}
