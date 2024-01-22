<?php

namespace App\Models\Koatuu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoatuuLevel2 extends Model
{
    use HasFactory;

    protected $table = 'koatuu_level2';

    protected $keyType = 'string';

    protected $fillable = [
            'id',
            'name',
            'koatuu_level1_id',
    ];


    public function koatuu_level3()
    {
        return $this->hasMany(KoatuuLevel3::class, 'koatuu_level2_id', 'id');
    }

    public function koatuu_level1()
    {
        return $this->belongsTo(KoatuuLevel1::class, 'koatuu_level1_id', 'id');
    }


}
