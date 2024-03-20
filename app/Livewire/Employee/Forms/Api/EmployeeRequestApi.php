<?php

namespace App\Livewire\Employee\Forms\Api;

use App\Classes\eHealth\Api\EmployeeApi;
use Carbon\Carbon;

class EmployeeRequestApi extends EmployeeApi
{



    public static function createEmployeeRequest($uuid,$data):array
    {
        $params = self::createEmployeeRequestBuilder($uuid,$data);

        return self::_create($params);
    }

    public static function createEmployeeRequestBuilder($uuid,$data):array
    {
        if (!isset($data['employee']['tax_id'])) {
            $data['employee']['no_tax_id'] = true;
        }

        return [
            'legal_entity_id' => $uuid,
            'position'=> $data['employee']['position'],
            'division_id'=> $data['role'][0]['division_id'],
            'start_date'=> Carbon::parse($data['employee']['start_date'])->format('Y-m-d') ?? '',
            'employee_type'=> $data['role'][0]['employee_type'],
            'party'=> $data['employee'],
            'doctor'=> [
                 'educations'=> $data['educations'],
                 'specialities'=> $data['specialities'],
                 'qualifications'=> $data['qualifications'],
                 'science_degree'=> $data['science_degree'],
             ],
            'inserted_at'=> Carbon::now()->format('Y-m-d H:i:s'),
        ];

    }


}
