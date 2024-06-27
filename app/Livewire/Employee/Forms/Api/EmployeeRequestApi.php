<?php

namespace App\Livewire\Employee\Forms\Api;

use App\Classes\eHealth\Api\EmployeeApi;
use Carbon\Carbon;

class EmployeeRequestApi extends EmployeeApi
{




    public static function getEmployees($id):array
    {
        $params = [
            'legal_entity_id' => $id ,
            'page' => 1,
            'page_size' => 300
        ];

        return self::_get($params);
    }

    public static function createEmployeeRequest($uuid,$data):array
    {
//        $params = self::createEmployeeRequestBuilder($uuid,$data);

        return self::_create($data);
    }


    public static function getEmployeeRolesById(){
        return self::_getRolesById();
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

}
