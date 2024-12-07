<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'number',
    ];

    public function phoneable()
    {
        return $this->morphTo();
    }
}
