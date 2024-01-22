<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class DivisionApi extends Request
{

    private string $get = '/divisions';

    public function __construct()
    {
        self::setApiVersion('');
    }

    public function getDivisions($params = []): array
    {
        return Request::get($this->get, $params);
    }


}
