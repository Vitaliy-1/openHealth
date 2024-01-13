<?php

namespace App\Models\Koatuu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoatuuLevel3 extends Model
{
    use HasFactory;

    protected $table = 'koatuu_level3';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'koatuu_level2_id',
        'koatuu_level2_type',
        'koatuu_level1_id',
        'type',
        ];


    public function koatuu_level1()
    {
        return $this->belongsTo(KoatuuLevel1::class, 'koatuu_level1_id', 'id');
    }

    public function koatuu_level2()
    {
        return $this->belongsTo(KoatuuLevel2::class, 'koatuu_level2_id', 'id');
    }
}
