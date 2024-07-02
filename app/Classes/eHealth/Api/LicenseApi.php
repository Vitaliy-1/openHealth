<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class LicenseApi extends Request
{
    public const URL = '/api/licenses';

    public static function _get(array $params = []): array
    {
        return (new Request('GET', self::URL, $params))->sendRequest();
    }

    public static function _create(array $params = []): array
    {
        return (new Request('POST', self::URL, $params))->sendRequest();
    }

    public static function _update(string $id, array $params = []): array
    {
        return (new Request('PATCH', self::URL . '/' . $id, $params))->sendRequest();
    }
}
