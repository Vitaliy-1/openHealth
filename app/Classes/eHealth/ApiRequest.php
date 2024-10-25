<?php

namespace App\Classes\eHealth;

use App\Classes\eHealth\Errors\ErrorHandler;
use App\Classes\eHealth\Exceptions\ApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiRequest
{


    private string $method;

    private string $url;

    private array $params;

    private null|string $token;

    private array $headers = [];
    private bool $isToken = true;

    /**
     * @throws ApiException
     */
    public function sendRequest()
    {
        // Retrieve all data from the incoming request
        $data = request()->all();


        // Check if any data was provided
        if (empty($data)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No data provided'
            ], 400); // Return a 400 Bad Request response
        }

        // Extract data from the request
        $this->method = $data['method'] ?? null; // Use null coalescing operator to avoid errors
        $this->url = $data['url'] ?? null;
        $this->params = $data['params'] ?? [];

        $this->token = $data['token'] ?? null;
        $this->isToken = $data['isToken'] ?? true;

        // Validate that both method and URL are provided
        if (empty($this->method) || empty($this->url)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Method and URL are required'
            ], 400); // Return a 400 Bad Request response
        }

        // Send the HTTP request using the specified method, URL, and parameters
        $response = Http::acceptJson()
            ->withHeaders($this->getHeaders())
            ->{$this->method}($this->url, $this->params);

        // Handle a successful response
        if ($response->successful()) {
            $data = json_decode($response->body(), true);
            if (isset($data['urgent']) && !empty($data['urgent'])) {
                return response()->json([
                    'status' => 'success',
                    'data'   => $data
                ]);
            }
            return response()->json($data, $response->status()); // Return the successful response data
        }

        // Handle 401 Unauthorized error
        if ($response->status() === 401) {
            $data = json_decode($response->body(), true);
            return response()->json($data, $response->status());
        }

        // Handle other types of errors
        if ($response->failed()) {
            $errors = json_decode($response->body(), true);
            return response()->json($errors, $response->status());
        }

        // Handle unexpected errors
        return response()->json([
            'status'  => 'error',
            'message' => 'An unexpected error occurred'
        ], 500); // Return a 500 Internal Server Error response
    }


    public function getHeaders(): array
    {
        $headers = [
            'X-Custom-PSK' => config('ehealth.api.token'),
            'API-key'      => config('ehealth.api.api_key'),
        ];

        if ($this->isToken) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
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
