<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;

class PersonApi extends Request
{
    public const string URL = '/api/v2/person_requests';

    /**
     * @throws ApiException
     */
    public static function _create(array $params = []): array
    {
        return (new Request('POST', self::URL, $params))->sendRequest();
    }

    /**
     * @throws ApiException
     */
    public static function _initialize(array $params = [])
    {
        return (new Request('POST', '/api/verifications', $params))->sendRequest();
    }

    /**
     * @throws ApiException
     */
    public static function _complete($phoneNumber, array $params = [])
    {
        return (new Request('PATCH', '/api/verifications/' . urlencode($phoneNumber) . '/actions/complete', $params))->sendRequest();
    }

    /**
     * @throws ApiException
     */
    public static function _verify(array $params = [])
    {
        return (new Request('POST', '/api/sms_verifications', $params))->sendRequest();
    }

    /**
     * @throws ApiException
     */
    public static function _patch(string $personId, array $params = []): array
    {
        return (new Request('PATCH', self::URL . "/$personId/actions/approve", $params))->sendRequest();
    }
}
