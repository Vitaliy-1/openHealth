<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class ContractApi extends Request
{

    public const URL = 'contract_requests';

    public static function _create_initialize($contract_type = []): array
    {
        return self::post(self::URL.'/'.$contract_type);
    }

    public static function create_request($data,$id,$contract_type = 'PMD_1')
    {
        return self::post(self::URL.'/'.$contract_type.'/'.$id, $data);
    }


}
