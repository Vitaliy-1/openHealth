<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'number',
        'issuedBy',
        'issuedAt',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
