<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperDocument
 */
class Document extends Model
{
    protected $hidden = [
        'id',
        'documentable_type',
        'documentable_id',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'documentable_type',
        'documentable_id',
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
