<?php

namespace App\Classes\Cipher;

use App\Classes\eHealth\Exceptions\ApiException;
use Illuminate\Support\Facades\Http;

class Request
{
    private string $method;

    private string $url;

    private string $params;

    public function __construct(
        string $method,
        string $url,
        string $params,
    ) {
        $this->method = $method;
        $this->url = $url;
        $this->params = $params;
    }


    /**
     * @throws ApiException
     */
    public function sendRequest()
    {
        $url = env('CIPHER_API_URL') . $this->url;
        $response = Http::acceptJson()
            ->withBody($this->params )
            ->{$this->method}($url);

        if ($response->successful()) {
            $success = json_decode($response->body(), true);
            $success['status'] = $response->status();
            return $success ?? [];
        }

        if ($response->failed()) {
            $error = json_decode($response->body(), true);
            dd($error);
            return $error ?? [];

            throw match ($response->status()) {
                400 => new ApiException($error['message'] ?? 'Невірний запит'),
                403 => new ApiException($error['message'] ?? 'Немає доступу'),
                404 => new ApiException($error['message'] ?? 'Не вдалося знайти запитану сторінку'),
                default => new ApiException($error['message'] ?? 'API request failed'),
            };
        }

    }


}
