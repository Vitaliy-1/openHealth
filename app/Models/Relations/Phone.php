<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Phone extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'number'
    ];

    public function phoneable(): MorphTo
    {
        return $this->morphTo();
    }
}
