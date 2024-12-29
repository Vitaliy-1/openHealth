<?php

namespace App\Models\Relations;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    use HasCamelCasing;


    protected $fillable = [
        'type',
        'number',
        'issuedBy',
        'issuedAt',
    ];

    public function documentable(){
        return $this->morphTo();
    }
}
