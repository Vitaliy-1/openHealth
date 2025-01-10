<?php

namespace App\Models\Person;

use App\Models\Employee\Employee;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends BasePerson
{
    public function __construct()
    {
        parent::__construct();
        $this->mergeFillable(['death_date']);
    }

    protected $table = 'persons';

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function personRequest(): HasOne
    {
        return $this->hasOne(PersonRequest::class);
    }
}
