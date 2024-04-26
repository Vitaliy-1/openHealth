<?php

namespace App\Livewire\Contract\Forms\Api;

use App\Classes\eHealth\Api\ContractApi;

class ContractRequestApi extends ContractApi
{

    public static function initContractRequestApi($contract_type)
    {
        return self::_create_initialize($contract_type);
    }


    public static function contractRequestApi($data,$contract_id){
        $builder = self::create_request_builder($data);
        return self::create_request($data,$contract_id);

    }
    protected static function create_request_builder($data)
    {
        return $data;
    }

}
