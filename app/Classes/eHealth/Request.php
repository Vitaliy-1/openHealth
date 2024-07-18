<?php

namespace App\Classes\eHealth;

use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealth;
use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealthInterface;
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

    private bool $isToken ;

    private oAuthEhealthInterface $oAuthEhealth;

    private array $headers = [];

    //private bool $isApiKey;


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

//        if (!$this->oAuthEhealth->isLoggedIn()){
//            abort(401);
//        }
//        Log::info('HTTP Request:', [
//            'url' => $this->url,
//            'method' => $this->method,
//            'headers' => $this->headers,
//            'params' => $this->params,
//        ]);
        try {
            $response = Http::acceptJson()
                ->withHeaders($this->getHeaders())
                ->{$this->method}(self::makeApiUrl(), $this->params);


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
                $error = json_decode($response->body(), true);
                dd($error);
//                FlashMessage::class($error['error']['message'], 'error');
                throw new ApiException($error['message'], $response->status());
            }
        }

        catch (Exception $exception){
            return json_decode($exception);
        }


    }


    public function getHeaders(): array
    {
//        dd($this->oAuthEhealth->getApikey());
        $headers = [
             'X-Custom-PSK' => env('EHEALTH_X_CUSTOM_PSK'),
             'API-key' => $this->oAuthEhealth->getApikey(),
        ];
        if ($this->isToken) {
            $headers['Authorization'] = 'Bearer '. $this->oAuthEhealth->getToken();
        }
        return array_merge($headers, $this->headers);
    }

    private function flashMessage($message, $type)
    {
        // Виклик події браузера через Livewire
        \Livewire\Component::dispatch('flashMessage', ['message' => $message, 'type' => $type]);
    }
}
