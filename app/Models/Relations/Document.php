<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = [
        'type',
        'number',
        'issued_by',
        'issued_at',
        'expiration_date'
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
