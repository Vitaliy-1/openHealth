<?php

namespace App\Models\Koatuu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoatuuLevel1 extends Model
{
    use HasFactory;

    protected $table = 'koatuu_level1';

    protected $fillable = [
        'id',
        'name',
    ];

    /**
     * type of primary key
     *
     * @var string
     */
    protected $keyType = 'string';

    public function koatuu_level2()
    {
        return $this->hasMany(KoatuuLevel2::class, 'koatuu_level1_id', 'id');
    }

    public function koatuu_level3()
    {
        return $this->hasMany(KoatuuLevel3::class, 'koatuu_level1_id', 'id');
    }


}
