<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;

class DivisionApi extends Request
{
    public const URL = '/api/divisions';

    public static function _get($data = []): array
    {
        return (new Request('GET', self::URL, $data))->sendRequest();
    }

    public static  function _create($data): array
    {
        return (new Request('POST', self::URL, $data))->sendRequest();
    }

    /**
     * @throws ApiException
     */
    public static function _update($id, $data): array
    {
        return (new Request('PATCH', self::URL . '/' . $id, $data))->sendRequest();
    }

    public static function _activate($id): array
    {
        return (new Request('PATCH', self::URL . '/' . $id . '/actions/activate', []))->sendRequest();
    }

    public static function _deactivate($id): array
    {
        return (new Request('PATCH', self::URL . '/' . $id . '/actions/deactivate', []))->sendRequest();
    }

    public static function _sync($legal_entity_id): array
    {
        $data = [
            'legal_entity_id' => $legal_entity_id,
            'page'=> 1,
            'page_size' => 100
        ];
        return (new Request('GET', self::URL, $data))->sendRequest();
    }
}
