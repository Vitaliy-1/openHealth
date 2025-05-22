<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperEmployeeRequest
 */
class EmployeeRequest extends BaseEmployee
{
    use HasFactory;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->with = array_merge($this->with, ['revision', 'employee']);
        $this->fillable = array_merge($this->fillable, ['applied_at']);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($employeeRequest) {
            if (empty($employeeRequest->uuid)) {
                $employeeRequest->uuid = (string)Str::uuid();
            }
        });
    }
}
