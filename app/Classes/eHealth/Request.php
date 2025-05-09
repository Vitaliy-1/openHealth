<?php

declare(strict_types=1);

namespace App\Classes\eHealth;

use App\Enums\HttpMethod;
use App\Auth\EHealth\Services\TokenStorage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Classes\eHealth\Errors\ErrorHandler;
use App\Classes\eHealth\Exceptions\ApiException;

class Request
{
    private TokenStorage $tokenStorage;

    private array $headers = [];

    //TODO Check use of API key
    //private bool $isApiKey;

    public function __construct(
        private string|HttpMethod $method,
        private string $url,
        private array $params,
        private bool $isToken = true,
        private ?string $mspDrfo = null,
    ) {
        $this->tokenStorage = new TokenStorage();
        $this->mspDrfo = $mspDrfo ?? '';
    }

    /**
     * If the URL is already absolute, return it unchanged, otherwise, add a basic domain
     *
     * @return string
     */
    protected function makeApiUrl(): string
    {
        if (filter_var($this->url, FILTER_VALIDATE_URL)) {
            return $this->url;
        }

        return config('ehealth.api.domain') . $this->url;
    }

    /**
     * @throws ApiException
     */
    public function sendRequest()
    {
        // If the URL is full, and you need to send a file via form-data
        if (filter_var($this->url, FILTER_VALIDATE_URL)) {
            $file = $this->params['multipart'][0] ?? null;

            if ($file) {
                $fileContent = stream_get_contents($file['contents']);

                $response = Http::attach('file', $fileContent, $file['filename'])
                    ->withHeaders(['Content-Type' => 'multipart/form-data'])
                    ->put($this->url);

                if ($response->status() !== 200) {
                    Log::channel('api_errors')->error('API request failed', [
                        'url' => $this->makeApiUrl(),
                        'status' => $response->status(),
                        'errors' => $response->body()
                    ]);
                }

                return [
                    'status' => $response->status(),
                    'body' => $response->body()
                ];
            }
        }

        //TODO DELETE AFTER TESTING
        if (config('ehealth.api.key') == null && empty(config('ehealth.api.key'))) {
            $data = [
                'method' => $this->method,
                'url' => $this->makeApiUrl(),
                'params' => $this->params,
                'token' => $this->tokenStorage->getBearerToken(),
                'isToken' => $this->isToken
            ];

            $response = Http::acceptJson()
                ->post('https://openhealths.com/api/v1/send-request', $data);
        } else {
            $response = Http::acceptJson()
                ->withHeaders($this->getHeaders())
                ->{$this->method}($this->makeApiUrl(), $this->params);
        }

        if ($response->successful()) {
            $data = json_decode($response->body(), true);

            if (isset($data['urgent']) && !empty($data['urgent'])) {
                return $data ?? [];
            }

            return $data['data'] ?? [];
        }

        if ($response->status() === 401) {
            $this->tokenStorage->clear();
        }

        if ($response->failed()) {
            $errors = json_decode($response->body(), true);

            Log::channel('api_errors')->error('API request failed', [
                'url' => $this->makeApiUrl(),
                'status' => $response->status(),
                'errors' => $errors
            ]);

            dd($errors);
            return (new ErrorHandler())->handleError($errors);
        }
    }

    public function getHeaders(): array
    {
        $headers = [
            'X-Custom-PSK' => config('ehealth.api.token'),
            //TODO Check use of API key
            'API-key' => config('ehealth.api.api_key'),
        ];

        if (!empty($this->mspDrfo)) {
            $headers['msp_drfo'] = $this->mspDrfo;
        }

        if ($this->isToken) {
            $headers['Authorization'] = 'Bearer ' .$this->tokenStorage->getBearerToken();
        }

        return array_merge($headers, $this->headers);
    }
}
