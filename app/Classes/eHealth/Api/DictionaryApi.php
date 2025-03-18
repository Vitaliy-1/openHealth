<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;
use Symfony\Component\HttpFoundation\Request as RequestHttp;

class DictionaryApi
{
    protected const string ENDPOINT_DICTIONARY = '/api/v2/dictionaries';

    /**
     * Each dictionary is an object that contains not only a code and description of a value, but also a status of the value.
     * In addition, it can represent hierarchical dictionaries with subordinate (child) values
     *
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getDictionaries(array $params = []): array
    {
        return new Request(RequestHttp::METHOD_GET, self::ENDPOINT_DICTIONARY, $params)->sendRequest();
    }
}
