<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class DeclarationApi extends Request
{

    const URL = '/api/declarations';

    public static function _getList($data = []): array
    {
        return (new Request('get', self::URL, $data))->sendRequest();
    }


}
