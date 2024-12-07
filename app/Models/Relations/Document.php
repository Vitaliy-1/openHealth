<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'number',
        'issued_by',
        'issued_at',
    ];

    public function documentable(){
        return $this->morphTo();
    }
}
