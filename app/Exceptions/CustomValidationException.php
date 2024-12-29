<?php

namespace App\Exceptions;

use Exception;

class CustomValidationException extends Exception
{
    /**
     * Custom type of the validation error
     *
     * @var $type
     */
    public $type;

    public function __construct($message, $type)
    {
        parent::__construct($message);

        $this->type = $type;
    }
}
