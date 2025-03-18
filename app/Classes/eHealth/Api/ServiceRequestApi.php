<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;
use App\Enums\HttpMethod;

class ServiceRequestApi
{
    protected const string ENDPOINT_SERVICE_REQUESTS = '/api/service_requests';

    /**
     * Discover service requests by requisition number. If nothing found by requisition number - it will return empty list.
     *
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function searchForServiceRequestsByParams(array $params): array
    {
        return new Request(HttpMethod::GET, self::ENDPOINT_SERVICE_REQUESTS, $params)->sendRequest();
    }
}
