<?php

declare(strict_types=1);

namespace App\Models\Encounter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperCoding
 */
class Coding extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'id',
        'codeable_type',
        'codeable_id',
        'created_at',
        'updated_at',
    ];

    public function codeable(): MorphTo
    {
        return $this->morphTo();
    }
}
