<?php

namespace App\Helpers;



use Illuminate\Support\Facades\Cache;

/**
 * Class JsonHelper
 *
 * Helper class for searching values in a JSON file based on provided keys.
 *
 * @package App\Helpers
 */
class JsonHelper
{
    /**
     * Define the paths to JSON files using constant keys.
     */
    private static  array $json_path = [];

    const DICTIONARIES_API_URL = 'https://api.ehealth.gov.ua/api/dictionaries';

    public static function handle(): void
    {
        if (!Cache::has('json_path')) {
            $response = file_get_contents(self::DICTIONARIES_API_URL);
            if ($response === false) {
                // Handle error when fetching data from the API
                throw new \RuntimeException('Failed to fetch data from the API.');
            }
            $dictionaries = json_decode($response, true);
            if ($dictionaries === null) {
                // Handle error when decoding JSON data
                throw new \RuntimeException('Failed to decode JSON data.');
            }
            Cache::put('json_path', $dictionaries['data'], now()->addDays(7));

        }
        self::$json_path = Cache::get('json_path');
    }

    /**
     * Search for values in a JSON file based on the provided keys.
     *
     * @param string $pathKey      The key to identify the path to the JSON file in the constant array.
     * @param array  $searchKeys   The keys to search for in the JSON data.
     *
     * @return array|null An associative array containing the found values for each search key, or null if not found or an error occurred.
     */
    public static function searchValue(string $pathKey, array $searchKeys): ?array
    {


        static::handle();


        $path = self::getPath();

        if ($path === null) {
            return null;
        }

        // Search results
        $searchResults = [];

        foreach ($searchKeys as $searchKey) {
            $result = self::recursiveSearch($path, $searchKey);
            if ($result !== null) {
                // Add only the found results to the array
                $searchResults[$searchKey] = $result;
            }
        }

        return $searchResults;
    }

    /**
     * Recursively search for a key in a nested associative array.
     *
     * @param array  $data        The associative array to search in.
     * @param string $searchKey   The key to search for.
     *
     * @return mixed|null The value if found, or null if not found.
     */
    private static function recursiveSearch(array $data, string $searchKey): mixed
    {
        foreach ($data as $value) {
            if (isset($value['name']) && $value['name'] === $searchKey) {
                return $value['values'];
            }
            if (is_array($value)) {
                $result = self::recursiveSearch($value, $searchKey);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null; // Key not found
    }

    /**
     * Get the path from the constant array based on the provided key.
     *
     * @param string $pathKey  The key to identify the path in the constant array.
     *
     * @return mixed|null The path if found, or null if not found.
     */
    private static function getPath(): mixed
    {
        return static::$json_path ?? null;
    }
}
