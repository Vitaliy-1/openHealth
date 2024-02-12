<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class HealthcareServiceApi extends Request
{
     public const URL = '/healthcare_services';

    public function __construct()
    {
        self::setApiVersion('');
    }

    public static function  _get($params = []): array
    {
        return self::get(self::URL, $params);
    }

    public static function _getById($id,$params = []): array
    {
        return self::get(self::URL.'/'.$id, $params);
    }

    public static  function _create($data): array
    {
        return self::post(self::URL, $data);
    }

    public static  function _update($id, $data): array
    {
        return self::patch(self::URL . '/' . $id, $data);
    }

    public static  function _activate($id): array
    {
        return self::patch(self::URL . '/' . $id);
    }

    public static  function _deactivate($id): array
    {
        return self::patch(self::URL  . '/' .  $id . '/actions/deactivate');
    }


}
