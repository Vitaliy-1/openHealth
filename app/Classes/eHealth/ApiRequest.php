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

class ApiRequest
{
    protected ?array $data;

    //TODO Check use of API key
    //private bool $isApiKey;
    protected string $url;

    protected string $method;

    protected array $params;
    private bool $isToken = true;


    public function __construct() {
        dd(request()->all());

    }



    /**
     * @throws ApiException
     */
    public function sendRequest()
    {
        if (config(''))

            $response = Http::acceptJson()
                ->withHeaders($this->getHeaders())
                ->{$this->method}($this->url, $this->params);

            if ($response->successful()) {
                $data = json_decode($response->body(), true);
                if (isset($data['urgent']) && !empty($data['urgent'])) {
                    return $data ?? [];
                }
                return $data['data'] ?? [];
            }

            if ($response->failed()) {
               $errors = json_decode($response->body(), true);

                Log::channel('api_errors')->error('API request failed', [
                    'url' => $this->url,
                    'status' => $response->status(),
                    'errors' => $errors
                ]);

               return (new ErrorHandler())->handleError($errors);
            }



    }


    public function getHeaders(): array
    {
        $headers = [
             'X-Custom-PSK' => env('EHEALTH_X_CUSTOM_PSK'),
        ];

        if ($this->isToken) {
            $headers['Authorization'] = 'Bearer '. $this->token;
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
