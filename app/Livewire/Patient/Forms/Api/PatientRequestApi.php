<?php

namespace App\Livewire\Patient\Forms\Api;

use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Exceptions\ApiException;

class PatientRequestApi extends PersonApi
{
    /**
     * @throws ApiException
     */
    public static function createPatientRequest($data): array
    {
        return self::_create($data);
    }

    /**
     * @throws ApiException
     */
    public static function initializeOtpVerification($data): array
    {
        return self::_initialize($data);
    }

    /**
     * @throws ApiException
     */
    public static function completeOtpVerification($phoneNumber, $data)
    {
        return self::_complete($phoneNumber, $data);
    }

    /**
     * @throws ApiException
     */
    public static function verifyOtpVerification($data)
    {
        return self::_verify($data);
    }

    /**
     * @throws ApiException
     */
    public static function approvePatientRequest($personId, $data): array
    {
        return self::_patch($personId, $data);
    }
}
