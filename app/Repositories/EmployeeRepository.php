<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository
{

    /**
     * @param $data
     * @return Employee
     */
    public function createOrUpdate($data): Employee
    {
        return Employee::updateOrCreate(
            [
                'uuid' => $data['id']
            ],
            $data
        );
    }

}
