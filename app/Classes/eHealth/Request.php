<?php

namespace App\Classes\eHealth;

use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealth;
use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealthInterface;
use App\Classes\eHealth\Exceptions\ApiException;
use Illuminate\Support\Facades\Http;

class Request
{
    private string $method;

    private string $url;

    private array $params;

    private bool $isToken ;
    private oAuthEhealthInterface $oAuthEhealth;

    private array $headers = [];


    public function __construct(
        string $method,
        string $url,
        array $params,
        bool $isToken = true,
    ) {
        $this->method = $method;
        $this->url = $url;
        $this->params = $params;
        $this->isToken = $isToken;
        $this->oAuthEhealth = new oAuthEhealth();
    }

    protected function makeApiUrl(): string
    {
        return config('ehealth.api.domain'). $this->url;
    }

    /**
     * @throws ApiException
     */
    public function sendRequest()
    {
        $response = Http::acceptJson()
            ->withHeaders($this->getHeaders())
            ->{$this->method}(self::makeApiUrl(), $this->params);
        if ($response->successful()) {

            return json_decode($response->body(), true)['data'] ?? [];
        }
        if ($response->status() === 401) {
            $this->oAuthEhealth->forgetToken();
        }

        if ($response->failed()) {
            $error = json_decode($response->body(), true);
            throw match ($response->status()) {
                400 => new ApiException($error['message'] ?? 'Невірний запит'),
                403 => new ApiException($error['message'] ?? 'Немає доступу'),
                404 => new ApiException($error['message'] ?? 'Не вдалося знайти запитану сторінку'),
                default => new ApiException($error['message'] ?? 'API request failed'),
            };
        }

    }


    public function getHeaders(): array
    {
        $headers = [
            'X-Custom-PSK' => env('EHEALTH_X_CUSTOM_PSK'),
        ];

        if ($this->isToken) {
            $headers['Authorization'] = 'Bearer '. $this->oAuthEhealth->getToken();
        }
        return array_merge($headers, $this->headers);
    }
}
