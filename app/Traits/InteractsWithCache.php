<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

trait InteractsWithCache
{
    /**
     * Store data in cache from forms.
     *
     * @param string $cacheKey The cache key to store the data under.
     * @param string $model The model type to store data for.
     * @param string $requestProperty The property in the request object containing the data.
     * @param array $singleModels An array of models that should be stored as single entries.
     * @return void
     */
    public function storeCacheData(string $cacheKey, string $model, string $requestProperty, array $singleModels = []): void
    {
        $cacheData = [];

        // Load existing cache if available
        if ($this->hasCache($cacheKey)) {
            $cacheData = $this->getCache($cacheKey);
        }

        // Ensure $cacheData[$this->requestId] is an array
        if (!isset($cacheData[$this->requestId]) || !is_array($cacheData[$this->requestId])) {
            $cacheData[$this->requestId] = [];
        }

        // Determine if the model should be a single item or an array of items
        if (in_array($model, $singleModels, true)) {
            // Assign directly if model should be a single entry
            $cacheData[$this->requestId][$model] = $this->{$requestProperty}->{$model};
        } else {
            // Initialize the array if not already an array
            if (!isset($cacheData[$this->requestId][$model]) || !is_array($cacheData[$this->requestId][$model])) {
                $cacheData[$this->requestId][$model] = [];
            }
            // Append to the array if model allows multiple entries
            $cacheData[$this->requestId][$model][] = $this->{$requestProperty}->{$model};
        }

        $this->putCache($cacheKey, $cacheData);
    }

    /**
     * Check is cache exists by cache key.
     *
     * @param string $cacheKey
     * @return bool
     */
    public function hasCache(string $cacheKey): bool
    {
        return Cache::has($cacheKey);
    }

    /**
     * Get data from cache.
     *
     * @param string $cacheKey
     * @return mixed
     */
    public function getCache(string $cacheKey): mixed
    {
        return Cache::get($cacheKey, []);
    }

    /**
     * Put data to cache.
     *
     * @param string $cacheKey
     * @param array $cacheData
     * @return void
     */
    public function putCache(string $cacheKey, array $cacheData): void
    {
        Cache::put($cacheKey, $cacheData, now()->days(90));
    }
}
