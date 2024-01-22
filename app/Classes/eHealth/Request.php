<?php

namespace App\Classes\eHealth;

use Illuminate\Support\Facades\Http;

class Request extends Configuration
{


    protected static function makeApiUrl(string $url): string
    {
        return self::getApiUrl() . '/api/'  . $url;
    }

    protected static function sendRequest(string $method, string $url, array $params = []): array
    {

        $response = Http::acceptJson()
            ->{$method}(self::makeApiUrl($url), $params);
        if ($response->successful()) {
            return json_decode($response->body(), true)['data'] ?? [];
        }

        // Handle failed requests if necessary

        return [];
    }

    public static function get(string $url, array $params = []): array
    {
        return self::sendRequest('get', $url, $params);
    }

    public static function post(string $url, array $params = []): array
    {
        return self::sendRequest('post', $url, $params);
    }

    public static function put(string $url, array $params = []): array
    {
        return self::sendRequest('put', $url, $params);
    }
}
