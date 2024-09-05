<?php


namespace App\Classes\Cipher\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected array $errors;

    public function __construct(array $errors, $message = "", $code = 0, Exception $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
