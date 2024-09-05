<?php

namespace App\Livewire\Division\Api;

use App\Classes\eHealth\Api\DivisionApi;

class DivisionRequestApi extends DivisionApi
{



    public static  function getDivisionRequest($params = []):array
    {
        return self::_get($params);
    }

    public static function createDivisionRequest($data):array
    {
        $params = [
            'name' => $data['name'],
            'type' =>$data['type'],
            'email' => $data['email'],
            'phones' => [$data['phones']],
            'addresses' => [$data['addresses']],
//            'locations' => (object)$data['location'],
            ];

        return self::_create($params);
    }

    public static function updateDivisionRequest($id, $data):array
    {
        $params = [
            'name' => $data['name'],
            'type' =>$data['type'],
            'email' => $data['email'],
            'phones' => [$data['phones']],
            'addresses' => [$data['addresses']],
            //            'locations' => (object)$data['location'],
        ];
        return self::_update($id, $params);
    }

    public static function deactivateDivisionRequest($id):array
    {
        dd(self::_deactivate($id));
        return self::_deactivate($id);
    }

    public static function activateDivisionRequest($id):array
    {
        return self::_activate($id);
    }

    public static function syncDivisionRequest($legal_entity_id):array
    {
        return self::_sync($legal_entity_id);
    }
}
