<?php

namespace App\Livewire\Division\Api;

use App\Classes\eHealth\Api\DivisionApi;

class DivisionRequestApi extends DivisionApi
{


    public function __construct()
    {
        parent::__construct();
    }

    public static  function getDivisionRequest($params = []):array
    {
        return self::_get($params);
    }

    public static function createDivisionRequest($data):array
    {
        return self::_create($data);
    }

    public static function updateDivisionRequest($id, $data):array
    {
        return self::_update($id, $data);
    }

    public static function deactivateDivisionRequest($id):array
    {
        return self::_deactivate($id);
    }

    public static function activateDivisionRequest($id):array
    {
        return self::_activate($id);
    }

}
