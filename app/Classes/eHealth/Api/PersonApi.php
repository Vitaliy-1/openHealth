<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class PersonApi extends Request
{


    public const URL = '/api/stats/parties';

    public static function _getAuthMethod(): array
    {



        $data = [
            'party_id' => '843081c3-90f8-43cf-9cdb-8abdf5b0b507',
        ];

        return (new Request('get', self::URL,$data,true))->sendRequest();


    }

}
