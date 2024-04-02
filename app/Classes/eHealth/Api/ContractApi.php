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

}
