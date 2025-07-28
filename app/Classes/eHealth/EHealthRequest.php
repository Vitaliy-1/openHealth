<?php

declare(strict_types=1);

namespace App\Classes\eHealth;

use Closure;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\HigherOrderTapProxy;

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
     * Set a Callable validator for the response, which accepts an EHealthResponse instance as an argument.
     * See EHealthResponse::validate().
     *
     * @param Callable $validator
     */
    protected function setValidator(Callable $validator): void
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
