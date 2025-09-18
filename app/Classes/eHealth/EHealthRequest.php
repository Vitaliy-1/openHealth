<?php

declare(strict_types=1);

namespace App\Classes\eHealth;

use Closure;
use App\Exceptions\EHealth\EHealthResponseException;
use App\Exceptions\EHealth\EHealthValidationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\HigherOrderTapProxy;
use Illuminate\Http\Client\Response;
use Log;

abstract class EHealthRequest extends PendingRequest
{
    public const string QUERY_PARAM_PAGE_SIZE = 'page_size';

    /**
     * The HTTP request timeout in seconds.
     * This is specifically needed to load dictionaries
     * TODO load dictionaries during first-run or similar installation process
     */
    public const int TIMEOUT = 100;

    protected ?Closure $validator = null;

    public function __construct(?Factory $factory = null, $middleware = [])
    {
        parent::__construct($factory, $middleware);

        $this->withHeaders([
            'X-Custom-PSK' => config('ehealth.api.token'),
            'API-key' => config('ehealth.api.api_key'),
        ]);

        $token = session()->get(
            config('ehealth.api.oauth.bearer_token')
        );

        if ($token) {
            $this->withToken($token);
        }

        $this->baseUrl(
            config('ehealth.api.domain')
        );
    }

    /**
     * Sends an HTTP request to the eHealth API and handles the response.
     *
     * This method overrides the parent send method to provide custom error handling:
     * - Returns the response if it is not an EHealthResponse instance.
     * - Returns the response if it is successful.
     * - Throws EHealthValidationException if the response status is 422 (validation error).
     * - Throws EHealthResponseException for all other unsuccessful responses.
     *
     * @param  string  $method  The HTTP method (e.g., 'GET', 'POST', etc.).
     * @param  string  $url  The endpoint URL.
     * @param  array  $options  Additional request options (query, body, headers, etc.).
     *
     * @return EHealthResponse|Response
     *
     * @throws EHealthValidationException|EHealthResponseException|ConnectionException
     */
    public function send(string $method, string $url, array $options = []): EHealthResponse|Response
    {
        $response = parent::send($method, $url, $options);

        if (!is_a($response, EHealthResponse::class)) {
            return $response;
        }

        if ($response->successful()) {
            return $response;
        }

        if ($response->status() === 422) {
            throw new EHealthValidationException($response->json());
        }

        throw new EHealthResponseException($response);
    }

    /**
     * Set a Callable validator for the response, which accepts an EHealthResponse instance as an argument.
     * See EHealthResponse::validate().
     *
     * @param callable $validator
     */
    protected function setValidator(callable $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * Set the default page size for the request.
     * It's the maximum number of items that can be returned per page.
     */
    protected function setDefaultPageSize(): void
    {
        $this->withQueryParameters([
            self::QUERY_PARAM_PAGE_SIZE => config('ehealth.api.page_size'),
        ]);
    }

    /**
     * Overrides the HTTP Client Request method to get a custom response.
     */
    protected function newResponse($response): HigherOrderTapProxy|EHealthResponse
    {
        return tap(new EHealthResponse($response, $this->validator), function (EHealthResponse $laravelResponse) {

            if ($this->truncateExceptionsAt === null) {
                return;
            }

            $this->truncateExceptionsAt === false
                ? $laravelResponse->dontTruncateExceptions()
                : $laravelResponse->truncateExceptionsAt($this->truncateExceptionsAt);
        });
    }
}
