<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperAuthenticationMethod
 */
class AuthenticationMethod extends Model
{
    protected $hidden = [
        'id',
        'authenticatable_type',
        'authenticatable_id',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
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
