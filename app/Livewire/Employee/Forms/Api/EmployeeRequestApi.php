<?php

namespace App\Livewire\Employee\Forms\Api;

use App\Classes\eHealth\Api\EmployeeApi;
use Carbon\Carbon;

class EmployeeRequestApi extends EmployeeApi
{


    public static function getEmployees($legal_entity_id):array
    {
        $params = [
            'legal_entity_id' => $legal_entity_id ,
            'page' => 1,
            'page_size' => 300
        ];

        return self::_get($params);
    }

    public static function createEmployeeRequest($data):array
    {
        return self::_create($data);
    }

    public static function getEmployeeById($id): array
    {
        return self::_getById($id);
    }


    public static function createEmployeeRequestBuilder($uuid,$data):array
    {
        if (!isset($data['employee']['tax_id'])) {
            $data['employee']['no_tax_id'] = true;
        }
        return [
            'legal_entity_id' => $uuid,
            'position'=> $data['employee']['position'],
            'start_date'=> $data['employee']['start_date'],
            'employee_type'=> $data['employee']['employee_type'],
            'party'=> $data['employee'],
            'doctor'=> [
                 'educations'=> $data['educations'] ?? [],
                 'specialities'=> $data['specialities'] ?? [],
                 'qualifications'=> $data['qualifications'] ?? [],
                 'science_degree'=> $data['science_degree'] ?? [],
             ],
            'inserted_at'=> Carbon::now()->format('Y-m-d H:i:s'),
        ];

    }




    public static function dismissedEmployeeRequest($id):array
    {
        return self::_dismissed($id);
    }

    public static function getEmployeeRequestsList():array
    {
        $data = [
            'status' => 'APPROVED',
        ];
        return self::_getRequestList($data);

    }

    public static function getEmployeeRequestById($id):array
    {

        return self::_getRequestById($id);

    }




}
