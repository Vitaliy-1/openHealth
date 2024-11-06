<?php

namespace App\Services\Dictionary;

class Dictionary
{
    /*
    * Local storage for all incoming data (includes Dictionary instances)
    *
    * @var array $dictionary
    */
    protected array $dictionary = [];

    /*
    * Some of dictionaries has name as if path. Ex: eHealth/SNOMED/anatomical_structure_administration_site_codes
    * This variable used to store such type of data
    *
    * @var array $path
    */
    protected array $path = [];

    /*
    * Name of current Dictionary (just a value of the 'name' key in original array)
    *
    * @var string $name
    */
    protected string $name = '';

    /*
    * Amount of stored items in Dictionary
    *
    * @var int $count
    */
    protected int $count = 0;

    public function __construct(array $dictionary = [])
    {
        $this->setDictionary($dictionary);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    /*
    * Create new pair in the dictionary 'key' => 'value'.
    *
    * @param string $key A key for new data pair
    * @param mixed $value A data value for corresponded key
    *
    * @return mixed
    */
    public function setValue(string $key, mixed $value): mixed
    {
        // TODO: FIND OUT is need to check for empty $value ?

        $this->count++;

        if (is_array($value)) {
            $dict = new self($value);
            $dict->checkPath($key);
            $dict->name = $key;

            $this->dictionary[$key] = $dict;

            return $dict;
        } else {
            $this->dictionary[$key] = $value;
        }

        return $value;
    }

    /*
    * Delete existent pair from the dictionary by key.
    *
    * @param string $key
    *
    * @return static
    */
    public function deleteValue(string $key): static
    {
        if (!empty($this->dictionary[$key])) {
            unset($this->dictionary[$key]);
            $this->count--;
        }

        return $this;
    }

    public function getValue(string $key): mixed
    {
        return $this->dictionary[$key] ?? null;
    }

    /*
    * Get value by specified key through all stored data in Dictionary.
    * In some cases the data can be another Dictionary objects. Therefore this method search into them too.
    * Recursive through all values.
    *
    * @param string $searchKey
    *
    * @return mixed
    */
    public function getValueThrough(string $searchKey): mixed
    {
        if (!empty($this->dictionary[$searchKey])) {
            return $this->dictionary[$searchKey];
        }

        foreach ($this->dictionary as $value) {
            if ($value instanceof self) {
                $result = $value->getValueThrough($searchKey);

                if ($result) {
                    return $result;
                }
            }
        }

        return null;
    }

    /*
    * Set whole Dictionary from incoming array (must be associative array)
    *
    * @param array $dictionary
    *
    * @return static
    */
    public function setDictionary(array $dictionary): static
    {
        foreach ($dictionary as $key => $value) {
            if (isset($this->dictionary[$key])) {
                continue;
            }

            $this->dictionary[$key] = $this->setValue($key, $value);
        }

        return $this;
    }

    /*
    * Check if current Dictionary has at least one Path
    *
    * @return bool
    */
    public function hasPath(): bool
    {
        return count($this->path) > 0;
    }

    /*
    * Check incoming name of the Dictionary and divide it to the separate parts.
    * Each part is single path element
    *
    * @param string $name
    *
    * @return static
    */
    public function checkPath(string $name): static
    {
        if (str_contains($name, '/')) {
            $this->path = explode('/', $name);

            // TODO: FIND OUT about last elem in the path - is it consider as part of the path too
            // $indexOfLastElement = count($this->path) - 1;
            // unset($this->path[$indexOfLastElement]);
        }

        return $this;
    }

    /*
    * Get array stored the Path data. Each element is single Path part.
    *
    * @return array
    */
    public function getPath(): array
    {
        return $this->path;
    }

    /*
    * Convert Dictionary to the Array. All included Dictionaries will converts too.
    * Recursive.
    *
    * @return array
    */
    public function toArray(): array
    {
        $arr = [];

        foreach ($this->dictionary as $key => $value) {
            $arr[$key] = $value instanceof self ? $value->toArray() : $value;
        }

        return $arr;
    }

    /*
    * Check if the Dictionary and it's subDictionaries has the specified key
    * Recursive.
    *
    * @param string $key
    *
    * return bool
    */
    public function hasKey(string $key): bool
    {
        return in_array($key, $this->getAllKeys(), true);
    }

    /*
    * Get 'first level' keys from dictionary. The keys from subDictionaries are not included.
    * Not recursive!
    *
    * @return array
    */
    public function getKeys(): array
    {
        $keys = [];

        foreach ($this->dictionary as $key => $value) {
            $keys[] = $key;
        }

        return $keys;
    }


    /*
    * Get all keys from dictionary.
    * The keys from subDictionaries and numeric keys are included too.
    * Recursive.
    *
    * @return array
    */
    public function getAllKeys(): array
    {
        $keys = [];

        foreach ($this->dictionary as $key => $value) {
            $keys[] = $key;

            if ($value instanceof self) {
                $keys = array_merge($keys, $value->getKeys());
            }
        }

        return $keys;
    }

    /*
    * Get value as another Dictionary if it stored as object.
    *
    * @param string $key Name of the key with the Dictionary object
    *
    * @return Dictionary|null
    */
    public function getSubDictionary(string $key): ?Dictionary
    {
        if (empty($this->dictionary[$key]) || ! $this->dictionary[$key] instanceof self) {
            return null;
        }

        return $this->dictionary[$key];
    }
}
