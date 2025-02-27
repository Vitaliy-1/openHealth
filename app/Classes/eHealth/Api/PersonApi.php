<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;
use App\Enums\HttpMethod;

class PersonApi
{
    protected const string ENDPOINT_PERSON = '/api/persons';

    /**
     * Search for a person by parameters.
     *
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function searchForPersonByParams(array $params = []): array
    {
        return (new Request(HttpMethod::GET, self::ENDPOINT_PERSON, $params))->sendRequest();
    }

    /**
     * Get current person's verification status and another information about it.
     *
     * @param  string  $personId
     * @return array
     * @throws ApiException
     */
    public static function getPersonVerificationDetails(string $personId): array
    {
        return (new Request(HttpMethod::GET, self::ENDPOINT_PERSON . "/$personId/verification", []))->sendRequest();
    }

    /**
     * Get list of active confidant person relationships.
     *
     * @param  string  $personId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getConfidantPersonRelationships(string $personId, array $params): array
    {
        return (new Request(
            HttpMethod::GET,
            self::ENDPOINT_PERSON . "/$personId/confidant_person_relationships",
            $params
        ))->sendRequest();
    }

    /**
     * Get the active person's authentication methods.
     *
     * @param  string  $personId
     * @return array
     * @throws ApiException
     */
    public static function getAuthenticationMethods(string $personId): array
    {
        return (new Request(
            HttpMethod::GET,
            self::ENDPOINT_PERSON . "/$personId/authentication_methods",
            []
        ))->sendRequest();
    }

    /**
     * Create new Confidant Person relationship request.
     *
     * @param  string  $personId
     * @return array
     * @throws ApiException
     */
    public static function createConfidantRelationship(string $personId): array
    {
        return (new Request(
            HttpMethod::POST,
            self::ENDPOINT_PERSON . "/$personId/confidant_person_relationship_requests",
            []
        ))->sendRequest();
    }
}
