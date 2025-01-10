<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuthenticationMethod extends Model
{
    protected $fillable = [
        'type',
        'phone_number',
        'value',
        'alias'
    ];

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }
}
