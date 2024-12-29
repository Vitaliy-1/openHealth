<?php

namespace App\Classes\eHealth;

use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealth;
use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealthInterface;
use App\Classes\eHealth\Errors\ErrorHandler;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Components\FlashMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class Request
{
    private string $method;

    private string $url;

    private array $params;
    private bool $isToken;

    private oAuthEhealthInterface $oAuthEhealth;

    private array $headers = [];

    //TODO Check use of API key
    //private bool $isApiKey;


    public function __construct(
        string $method,
        string $url,
        array  $params,
        bool   $isToken = true,
    )
    {
        $this->method = $method;
        $this->url = $url;
        $this->params = $params;
        $this->isToken = $isToken;
        $this->oAuthEhealth = new oAuthEhealth();

    }

    protected function makeApiUrl(): string
    {
        return config('ehealth.api.domain') . $this->url;
    }

    /**
     * @throws ApiException
     */
    public function sendRequest()
    {
        //TODO DELETE AFTER TESTING
        if (config('ehealth.api.key') == null && empty(config('ehealth.api.key'))) {
            $data = [
                'method' => $this->method,
                'url'    => self::makeApiUrl(),
                'params' => $this->params,
                'token'  => $this->oAuthEhealth->getToken(),
                'isToken' => $this->isToken
            ];
            $response = Http::acceptJson()
                ->post('https://openhealths.com/api/v1/send-request', $data);
        } else {
            $response = Http::acceptJson()
                ->withHeaders($this->getHeaders())
                ->{$this->method}(self::makeApiUrl(), $this->params);
        }

        if ($response->successful()) {
            $data = json_decode($response->body(), true);
            if (isset($data['urgent']) && !empty($data['urgent'])) {
                return $data ?? [];
            }
            return $data['data'] ?? [];
        }
        if ($response->status() === 401) {
            $this->oAuthEhealth->forgetToken();
        }

        if ($response->failed()) {
            $errors = json_decode($response->body(), true);
            dd($errors);

            Log::channel('api_errors')->error('API request failed', [
                'url'    => self::makeApiUrl(),
                'status' => $response->status(),
                'errors' => $errors
            ]);
            return (new ErrorHandler())->handleError($errors);
        }


    }


    public function getHeaders(): array
    {
        $headers = [
            'X-Custom-PSK' => config('ehealth.api.token'),
            //TODO Check use of API key
            'API-key'      => $this->oAuthEhealth->getApikey(),
        ];

        if ($this->isToken) {
            $headers['Authorization'] = 'Bearer ' . $this->oAuthEhealth->getToken();
        }
        return array_merge($headers, $this->headers);
    }
//
//    //TODO
//    private function flashMessage($message, $type)
//    {
//        // Виклик події браузера через Livewire
//        \Livewire\Component::dispatch('flashMessage', ['message' => $message, 'type' => $type]);
//    }



}
