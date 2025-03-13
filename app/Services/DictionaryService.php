<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Dictionary\Dictionary;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DictionaryService
{
    protected string $dictionariesUrl;

    /**
     * Local storage for all founded Dictionaries into incoming array.
     * As 'Dictionary' here should be interpreted as object created from the associative array.
     * Also, must be present the key that pointed to.
     *
     * @var Dictionary $rootDictionary
     */
    protected Dictionary $rootDictionary;

    public function __construct(array $config)
    {
        $this->dictionariesUrl = $config['dictionaries_api_v2_url'];
        $this->rootDictionary = new Dictionary();
        $this->update();
    }

    /**
     * Update the data received from aHealth API.
     * This method filled the $rootDictionary with all founded data.
     *
     * @return void
     */
    public function update(): void
    {
        $dictionaries = $this->getSourceDictionaries($this->dictionariesUrl);

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
     * @param  string  $dictionariesUrl  API URL to the resource
     * @return array
     * @throws RuntimeException
     */
    protected function getSourceDictionaries(string $dictionariesUrl): array
    {
        return Cache::remember('dictionaries', now()->addDays(7), static function () use ($dictionariesUrl): array {
            try {
                $response = Http::get($dictionariesUrl);
                $response->throw();
                return $response->json('data');
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
}
