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
        'created_at',
        'updated_at',
        'documentable_id',
        'documentable_type'
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
