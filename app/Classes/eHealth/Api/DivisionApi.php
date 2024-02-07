<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class DivisionApi extends Request
{

    private string $endpoint = '/divisions';



    public function __construct()
    {
        self::setApiVersion('');
    }

    public function _get($params = []): array
    {
        return Request::get($this->endpoint, $params);
    }

    public  function _create($data): array
    {
        return Request::post($this->endpoint, $data);
    }

    public  function _update($id, $data): array
    {
        return Request::patch($this->endpoint . '/' . $id, $data);
    }

    public  function _deactive($id): array
    {
        return Request::put($this->endpoint . '/' . $id);
    }

    public  function _activate($id): array
    {
        return Request::patch($this->endpoint . '/' . $id);
    }


}
