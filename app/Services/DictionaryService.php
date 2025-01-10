<?php

namespace App\Services;

use App\Services\Dictionary\Dictionary;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class DictionaryService
{
    protected string $dictionariesUrl;

    /**
    * Local storage for all founded Dictionaries into incoming array.
    * As 'Dictionary' here should be interpreted as object created from the associative array.
    * Also must be present the key that pointed to.
    *
    * @var Dictionary $rootDictionary
    */
    protected Dictionary $rootDictionary;

    public function __construct(array $dictionariesData)
    {
        $this->dictionariesUrl = $dictionariesData['dictionaries_api_url'];

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

            $this->rootDictionary->setValue($key, $entity);
        }
    }

    /**
     * Get all dictionaries data from external resource via API and put it into the Cache
     *
     * @param mixed $dictionariesUrl API URL to the resource
     * @return array
     * @throws RuntimeException
     */
    public static function getSourceDictionaries($dictionariesUrl): array
    {
        if (!Cache::has('json_path')) {
            $response = file_get_contents($dictionariesUrl);

            if ($response === false) {
                // Handle error when fetching data from the API
                throw new RuntimeException('Failed to fetch dictionaries data from the API.');
            }

            $dictionaries = json_decode($response, true);

            if ($dictionaries === null) {
                // Handle error when decoding JSON data
                throw new RuntimeException('Failed to decode dictionaries JSON data.');
            }

            Cache::put('json_path', $dictionaries['data'], now()->addDays(7));
        }

        return Cache::get('json_path');
    }


    /**
    * Get all founded Dictionaries and return it as associative array.
    * The key pointed to the dictionary is it's name.
    *
    * @return array
    */
    public function getAll(): array
    {
        $dictionaries = [];

        foreach ($this->rootDictionary->getKeys() as $key) {
            if (isset($dictionaries[$key])) {
                continue;
            }

            $dictionaries[$key] = $this->rootDictionary->getValue($key);
        }

        return $dictionaries;
    }

    /**
    * Found and return (if successfully) the Dictionary object.
    * If $isArray is set to TRUE then method return an array instead of the object.
    *
    * @param string $key Name of Dictionary
    * @param bool $isArray Flag indicates to return Dictionary as Array
    *
    * @return Dictionary|array|null
    */
    public function getDictionary(string $key, bool $isArray = false): Dictionary|array|null
    {
        return $isArray
            ? $this->rootDictionary->getValue($key)?->toArray()
            : $this->rootDictionary->getValue($key);
    }

    /**
    * Find and return (if successfully) array of the Dictionaries.
    * If $isArray is set to TRUE then method return an array instead of the object.
    *
    * @param array $searchArray Name of Dictionary
    * @param bool $isArray Flag indicates to return Dictionary as Array
    * @return array
    */
    public function getDictionaries(array $searchArray, bool $isArray = false): array
    {
        $dictionaries = [];

        foreach ($searchArray as $key) {
            $dictionary = $this->getDictionary($key, $isArray);
            // Skip wrong result
            if (is_null($dictionary)) {
                continue;
            }

            if ($isArray) {
                $dictionary = $dictionary['values'];
            }
            $dictionaries[$key] = $dictionary;
        }

        return $dictionaries;
    }

    /**
    * Get the associative array with key as name of Dictionary pointed to array with specified keys and its data.
    *
    * @param array $filterKeys Keys to store in array with its values
    * @param string $searchDict Dictionary where the
     * @return array
    */
    public function getDictionariesFields(array $filterKeys, string $searchDict): array
    {
        $filteredDictionaries = [];

        foreach ($filterKeys as $key) {
            $dictionary = $this->getDictionary($searchDict);

            $filteredDictionaries[$dictionary->getName()][$key] = $dictionary?->getValueThrough($key) ?? '';
        }

        return $filteredDictionaries;
    }
}
