<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class HealthcareServiceApi extends Request
{
    public const URL = '/api/healthcare_services';

    public static function  _get($params = []): array
    {
        return  (new Request('get', self::URL, $params))->sendRequest();
    }

    public static function _getById($id,$params = []): array
    {
        return (new Request('get', self::URL.'/'.$id, $params))->sendRequest();
    }

    public static  function _create($params): array
    {
        return (new Request('post', self::URL, $params))->sendRequest();

    }

    public static  function _update($id, $params): array
    {
        return (new Request('patch', self::URL. '/' . $id, $params))->sendRequest();

    }

    public static  function _activate($id): array
    {
        return (new Request('patch', self::URL. '/' . $id . '/actions/activate', []))->sendRequest();

    }

    public static  function _deactivate($id): array
    {
        return (new Request('patch', self::URL  . '/' .  $id . '/actions/deactivate', []))->sendRequest();

    }
}
