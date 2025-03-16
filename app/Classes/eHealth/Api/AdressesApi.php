<?php

namespace App\Classes\eHealth\Api;


use App\Classes\eHealth\Request;

class AdressesApi
{

    public const URL_REGIONS = '/api/uaddresses/regions';
    public const URL_DISTRICTS = '/api/uaddresses/districts';
    public const URL_SETTLEMENTS = '/api/uaddresses/settlements';
    public const URL_STREETS = '/api/uaddresses/streets';


    //Get all regions
    public static function _regions(): array
    {
        $params = [
            'page' => 1,
            'page_size' => 50,
        ];

        return (array) new Request('get', self::URL_REGIONS, $params,false)->sendRequest();
    }

    //Search Districts
    public static function _districts(string $region, string $search): array
    {
        $params = [
            'page' => 1,
            'page_size' => 10,
            'region' => $region,
            'name' => $search,
        ];

        return (array) new Request('get', self::URL_DISTRICTS, $params,false)->sendRequest();
    }

    //Search settlements
    public static function _settlements(string $region,string $district, string $search): array
    {
        $params = [
            'page' => 1,
            'page_size' => 10,
            'region' => $region,
            'district' => $district,
            'name' => $search,
        ];

        return (array) new Request('get', self::URL_SETTLEMENTS, $params,false)->sendRequest();
    }


    //Search Street by settlement_id
    public static function _streets(string $settlement_id ,string $street_type, string $search): array
    {
        $params = [
            'page' => 1,
            'page_size' => 10,
            'settlement_id' => $settlement_id,
            'type'=> $street_type,
            'name' => $search,
        ];

        return (array) new Request('get', self::URL_STREETS, $params,false)->sendRequest();
    }
}
