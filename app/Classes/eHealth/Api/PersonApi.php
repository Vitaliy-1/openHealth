<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;

class PersonApi extends Request
{
    protected const string URL_PERSON = '/api/persons';
    protected const string URL_V1 = '/api/person_requests';
    protected const string URL_V2 = '/api/v2/person_requests';

    /**
     * Create Person Request v2 (as part of Person creation w/o declaration process).
     *
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function createPersonRequest(array $params = []): array
    {
        return (new Request('POST', self::URL_V2, $params))->sendRequest();
    }

    /**
     * Create new Confidant Person relationship request.
     *
     * @param  string  $personId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function createConfidantRelationship(string $personId, array $params = []): array
    {
        return (new Request('POST', '/api/persons' . "/$personId/confidant_person_relationship_requests",
            $params))->sendRequest();
    }

    /**
     * Approve previously created Person Request.
     *
     * @param  string  $personId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function approvePersonRequest(string $personId, array $params = []): array
    {
        return (new Request('PATCH', self::URL_V2 . "/$personId/actions/approve", $params))->sendRequest();
    }

    /**
     * Upload file to storage by provided URL.
     *
     * @param  string  $url
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function uploadFileRequest(string $url, array $params = []): array
    {
        return (new Request('PUT', $url, $params))->sendRequest();
    }

    /**
     * Sign previously created Person Request.
     *
     * @param  string  $personId
     * @param  array  $params
     * @param  string  $mspDrfo
     * @return array
     * @throws ApiException
     */
    public static function singPersonRequest(string $personId, array $params = [], string $mspDrfo = ''): array
    {
        return (new Request('PATCH', self::URL_V2 . "/$personId/actions/sign", $params, mspDrfo: $mspDrfo))
            ->sendRequest();
    }

    /**
     * Obtains patient details by setting parameters like status, page, and page size.
     *
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getCreatedPersonsList(array $params = []): array
    {
        return (new Request('GET', self::URL_V1, $params))->sendRequest();
    }

    /**
     * Obtains patient details by ID.
     *
     * @param  string  $personId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getCreatedPersonById(string $personId, array $params = []): array
    {
        return (new Request('GET', self::URL_V2 . "/$personId", $params))->sendRequest();
    }

    /**
     * Re-send SMS to a person who approve creating or updating data about himself.
     *
     * @param  string  $personId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function resendAuthorizationSms(string $personId, array $params = []): array
    {
        return (new Request('POST', self::URL_V1 . "/$personId/actions/resend_otp", $params))->sendRequest();
    }

    /**
     * Search for a person by parameters.
     *
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function searchForPersonByParams(array $params = []): array
    {
        return (new Request('GET', self::URL_PERSON, $params))->sendRequest();
    }
}
