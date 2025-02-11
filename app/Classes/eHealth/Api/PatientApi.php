<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;
use App\Enums\HttpMethod;

class PatientApi
{
    protected const string ENDPOINT_PATIENT = '/api/patients';

    /**
     * Get the active person's authentication methods.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getShortEpisodes(string $patientId, array $params): array
    {
        return (new Request(
            HttpMethod::GET,
            self::ENDPOINT_PATIENT . "/$patientId/summary/episodes",
            $params
        ))->sendRequest();
    }

    /**
     * Get the current diagnoses related only to active episodes.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getActiveDiagnoses(string $patientId, array $params): array
    {
        return (new Request(
            HttpMethod::GET,
            self::ENDPOINT_PATIENT . "/$patientId/summary/diagnoses",
            $params
        ))->sendRequest();
    }

    /**
     * Get the current diagnoses related only to active episodes.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getObservations(string $patientId, array $params): array
    {
        return (new Request(
            HttpMethod::GET,
            self::ENDPOINT_PATIENT . "/$patientId/summary/observations",
            $params
        ))->sendRequest();
    }
}
