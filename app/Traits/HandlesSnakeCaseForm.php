<?php

namespace App\Traits;

use App\Traits\ArrayCaseCaster;
use Illuminate\Validation\ValidationException;

trait HandlesSnakeCaseForm
{
    /**
     * Calls validate() and transforms all keys into snake_case
     * @throws ValidationException
     */
    public function validatedSnakeCase(): array
    {
        return ArrayCaseCaster::toSnakeCase($this->validate());
    }

    /**
     * If only a specific key is needed
     * @throws ValidationException
     */
    public function validatedFieldSnakeCase(string $key)
    {
        return data_get($this->validatedSnakeCase(), $key);
    }
}
