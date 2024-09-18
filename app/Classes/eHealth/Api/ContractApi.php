<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class ContractApi extends Request
{

    public const URL = '/api/contract_requests';

    public static function _create_initialize($contract_type = 'capitation'): array
    {
        return (new Request('POST', self::URL.'/'.$contract_type, []))->sendRequest();
    }

    public static function create_request($data,$param,$contract_type = 'capitation'): array
    {
        return (new Request('POST',self::URL.'/'.$contract_type.'/'.$param['id'], $data))->sendRequest();
    }


}
