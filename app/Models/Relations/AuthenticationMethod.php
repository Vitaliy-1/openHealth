<?php

namespace App\Models\Relations;

use App\Models\PersonRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthenticationMethod extends Model
{
    protected $fillable = [
        'type',
        'phone_number',
        'value',
        'alias'
    ];

    public function personRequest(): BelongsTo
    {
        return $this->belongsTo(PersonRequest::class);
    }
}
