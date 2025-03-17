<?php

declare(strict_types=1);

namespace App\Services;

use App\Classes\eHealth\Api\DictionaryApi;
use App\Services\Dictionary\Dictionary;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class DictionaryService
{
    /**
     * Local storage for all founded Dictionaries into incoming array.
     * As 'Dictionary' here should be interpreted as object created from the associative array.
     * Also, must be present the key that pointed to.
     *
     * @var Dictionary $rootDictionary
     */
    protected Dictionary $rootDictionary;

    public function __construct()
    {
        $this->rootDictionary = new Dictionary();
        $this->update();
    }

    /**
     * Update the data received from aHealth API.
     * This method filled the $rootDictionary with all founded data.
     *
     * @return void
     */
    protected function update(): void
    {
        $dictionaries = $this->getSourceDictionaries();

        foreach ($dictionaries as $entity) {
            if (empty($entity['name'])) {
                continue;
            }

            $key = $entity['name'];
            unset($entity['name']);

            $this->rootDictionary->setValue($key, $entity['values']);
        }
    }

    /**
     * Get all dictionaries data from external resource via API and put it into the cache.
     *
     * @return array
     * @throws RuntimeException
     */
    protected function getSourceDictionaries(): array
    {
        return Cache::remember('dictionaries', now()->addDays(7), static function (): array {
            try {
                return DictionaryApi::getDictionaries();
            } catch (\Exception $e) {
                throw new \RuntimeException('Failed to fetch dictionaries data: ' . $e->getMessage());
            }
        });
    }

    /**
     * Find and return (if successfully) array of the Dictionaries.
     * If $toArray is set to TRUE then method return an array instead of the collection.
     *
     * @param  array  $searchArray  Name of Dictionary
     * @param  bool  $toArray  Flag indicates to return Dictionary as Array
     * @return Dictionary|array
     */
    public function getDictionaries(array $searchArray, bool $toArray = true): Dictionary|array
    {
        $items = [];

        foreach ($searchArray as $value) {
            if (isset($this->rootDictionary[$value])) {
                $items[$value] = collect($this->rootDictionary[$value])
                    ->mapWithKeys(static fn(array $item) => [$item['code'] => $item['description']]);
            }
        }

        return $toArray ? collect($items)->toArray() : new Dictionary($items);
    }

    /**
     * Get values by dictionary name.
     *
     * @param  string  $name
     * @param  bool  $toArray
     * @return Dictionary|array
     */
    public function getDictionary(string $name, bool $toArray = true): Dictionary|array
    {
        $items = [];

        if (isset($this->rootDictionary[$name])) {
            $items = collect($this->rootDictionary[$name])
                ->mapWithKeys(static fn(array $item) => [$item['code'] => $item['description']]);
        }

        return $toArray ? collect($items)->toArray() : new Dictionary($items);
    }

    /**
     * In order to get values that belong to a large reference dictionary, we must pass the name of the dictionary in the name parameter.
     *
     * @param  array  $params
     * @param  bool  $toArray
     * @return Dictionary|array
     */
    public function getLargeDictionary(array $params, bool $toArray = true): Dictionary|array
    {
        $cacheKey = "large_dictionary_" . $params['name'];

        return Cache::remember($cacheKey, now()->addDays(7), static function () use ($params, $toArray) {
            $items = DictionaryApi::getDictionaries($params);

            $formatted = collect($items)
                ->filter(static fn($item) => isset($item['name'], $item['values']))
                ->mapWithKeys(static fn($item) => [
                    $item['name'] => collect($item['values'])
                        ->filter(static fn($value) => isset($value['code'], $value['description']))
                        ->mapWithKeys(static fn($value) => [$value['code'] => $value['description']])
                        ->toArray()
                ])
                ->toArray();

            return $toArray ? $formatted : new Dictionary($formatted);
        });
    }
}
