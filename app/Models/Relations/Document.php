<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Model;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperDocument
 */
class Document extends Model
{
    use HasCamelCasing;

    protected $hidden = [
        'id',
        'createdAt',
        'updatedAt',
        'documentable_id',
        'documentable_type'
    ];

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'type',
        'number',
        'issuedBy',
        'issuedAt',
        'expirationDate'
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
