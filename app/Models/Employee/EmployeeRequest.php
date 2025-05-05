<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperEmployeeRequest
 */
class EmployeeRequest extends BaseEmployee
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function ($employeeRequest) {
            if (empty($employeeRequest->uuid)) {
                $employeeRequest->uuid = (string) Str::uuid();
            }
        });
    }
}
