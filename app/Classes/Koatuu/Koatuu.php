<?php

namespace App\Classes\Koatuu;

use App\Models\Koatuu\KoatuuLevel1;
use App\Models\Koatuu\KoatuuLevel2;
use App\Models\Koatuu\KoatuuLevel3;

class Koatuu
{

    public function getKoatuuLevel1()
    {
        return KoatuuLevel1::all();
    }


    public function getKoatuuLevel2($area,$region)
    {
        return KoatuuLevel2::where('name', $area)
            ->first()
            ->koatuu_level2()
            ->where('name', 'ilike', '%' . $region . '%')
            ->take(5)->get();
    }

    public function getKoatuuLevel3($area_id,$region,$settlement)
    {
        return KoatuuLevel3::where('area_id', $area_id)
            ->where('name', 'ilike', '%' . $region . '%')
            ->first()
            ->koatuu_level3()
            ->where('name', 'ilike', '%' . $settlement . '%')
            ->take(5)->get();
    }

}
