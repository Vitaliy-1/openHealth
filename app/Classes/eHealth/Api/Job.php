<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\EHealthRequest as Request;
use App\Classes\eHealth\EHealthResponse;
use GuzzleHttp\Promise\PromiseInterface;

class Job extends Request
{
    protected const string URL = '/api/jobs';

    public function get(string $url, $query = null): PromiseInterface|EHealthResponse
    {
        return parent::get(self::URL . "/$url", $query);
    }
}
