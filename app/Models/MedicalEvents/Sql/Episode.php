<?php

declare(strict_types=1);

namespace App\Models\Encounter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin IdeHelperEpisode
 */
class Episode extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'id',
        'encounter_id',
        'episode_type_id',
        'managing_organization_id',
        'care_manager_id',
        'created_at',
        'updated_at',
    ];

    public function period(): MorphOne
    {
        return $this->morphOne(Period::class, 'periodable');
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class, 'encounter_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Coding::class, 'episode_type_id');
    }

    public function managingOrganization(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'managing_organization_id');
    }

    public function careManager(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'care_manager_id');
    }
}
