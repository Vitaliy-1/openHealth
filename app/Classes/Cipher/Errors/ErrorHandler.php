<?php

namespace App\Classes\Cipher\Errors;

use App\Classes\Cipher\Exceptions\ApiException;

class ErrorHandler
{
    public static function handleError(array $error): array
    {
        if (isset($error['message']) && $error['message']) {
            $errorMessages = $error['message'] . ' ' . ($error['failureCause'] ?? 'No description');
        } else {
            $errorMessages =  "No valid error information provided.";
        }
        return ['errors' => $errorMessages];
    }

    public static function throwError(array $errorData): void
    {
        throw new ApiException(self::handleError($errorData));
    }
}
