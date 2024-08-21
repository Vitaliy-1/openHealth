<?php

namespace App\Classes\Cipher;

use App\Classes\Cipher\Errors\ErrorHandler;
use App\Classes\Cipher\Exceptions\ApiException;
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
            $error = ErrorHandler::handleError($error);
            throw new ApiException($error);
        }

    }


}
