<?php

declare(strict_types=1);

namespace App\Services\Dictionary\v2;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Dictionary
{
    protected const string DICTIONARIES_API_URL = 'https://api.ehealth.gov.ua/api/v2/dictionaries';
    protected array $allDictionaries;

    public function __construct()
    {
        $this->allDictionaries = $this->getJsonDictionaries();
    }

    /**
     * Get array of dictionaries by names.
     *
     * @param  array  $names
     * @return array
     */
    public function getDictionaries(array $names): array
    {
        return $this->prepareDictionaries($names)->toArray();
    }

    /**
     * Get a collection of dictionaries with names and values.
     *
     * @param  array  $names
     * @return Collection
     */
    protected function prepareDictionaries(array $names): Collection
    {
        return collect($this->allDictionaries)
            ->filter(static fn(array $item) => in_array($item['name'], $names, true))
            ->mapWithKeys(static function (array $dictionary) {
                $values = collect($dictionary['values'])
                    ->map(static fn(array $value) => new Item(
                        $value['code'],
                        $value['description'],
                        $value['is_active']
                    ))
                    ->keyBy(static fn(Item $item) => $item->code)
                    ->map(static fn(Item $item) => $item->description);

                return [$dictionary['name'] => $values];
            });
    }

    /**
     * Save dictionaries in JSON in the cache.
     *
     * @return array
     */
    protected function getJsonDictionaries(): array
    {
        return Cache::remember('dictionaries', now()->addDays(7), static function (): array {
            try {
                $response = Http::get(self::DICTIONARIES_API_URL);
                $response->throw();
                return $response->json('data');
            } catch (\Exception $e) {
                throw new \RuntimeException('Failed to fetch dictionaries data: ' . $e->getMessage());
            }
        });
    }
}
