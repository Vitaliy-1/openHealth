<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperPhone
 */
class Phone extends Model
{
    protected $hidden = [
        'id',
        'phoneable_type',
        'phoneable_id',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'type',
        'number',
        'phoneable_type',
        'phoneable_id'
    ];

    public function phoneable(): MorphTo
    {
        return $this->morphTo();
    }
}
