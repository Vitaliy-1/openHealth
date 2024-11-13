<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository
{

    public function createOrUpdate($data)
    {
        return Employee::updateOrCreate(
            [
                'uuid' => $data['id']
            ],
            $data
        );
    }

}
