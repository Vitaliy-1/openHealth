<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'addressable_id',
        'addressable_type'
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
        'zip'
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
