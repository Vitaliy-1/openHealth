<?php

declare(strict_types=1);

namespace App\Classes\eHealth;

use Closure;
use Illuminate\Http\Client\Response;
use RuntimeException;

class EHealthResponse extends Response
{
    /**
     * The path to the data in the response.
     * This is used to access the actual data in the response body using array dot notation.
     */
    public const string DATA_PATH = 'data';

    /**
     * The path to the paging information in the response, i.e. page_number, page_size, total_entries, total_pages.
     */
    public const string PAGING_PATH = 'paging';

    /**
     * @var Closure|null The closure that holds the validation rules for the response.
     */
    protected ?Closure $validator = null;

    public function __construct($response, ?Closure $validator = null)
    {
        parent::__construct($response);
        $this->validator = $validator;
    }

    /**
     * Validate response data.
     *
     * @return array
     */
    public function validate(): array
    {
        if (is_null($this->validator)) {
            throw new RuntimeException('Validator is not implemented for this response.');
        }

        return call_user_func($this->validator, $this);
    }

    /**
     * @return array eHealth response actual data
     */
    public function getData(): array
    {
        return $this->json(self::DATA_PATH, []);
    }

    /**
     * @return array eHealth pagination information
     */
    public function getPaging(): array
    {
        return $this->json(self::PAGING_PATH, []);
    }

    /**
     * Determine if the response contains not all the data, e.g., if it is paginated and returns a subset of results.
     *
     * @return bool
     */
    public function isNotLast(): bool
    {
        $paging = $this->getPaging();

        // Check by page number
        if (isset($paging['page_number'], $paging['total_pages'])) {
            return $paging['page_number'] < $paging['total_pages'];
        }

        return false;
    }
}
