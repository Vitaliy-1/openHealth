<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperAddress
 */
class Address extends Model
{
    protected $hidden = [
        'id',
        'addressable_id',
        'addressable_type',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'type',
        'country',
        'area',
        'region',
        'settlement',
        'settlement_type',
        'settlement_id',
        'street_type',
        'street',
        'building',
        'apartment',
        'zip',
        'addressable_id',
        'addressable_type'
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
