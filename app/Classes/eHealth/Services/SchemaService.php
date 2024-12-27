<?php

namespace App\Classes\eHealth\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SchemaService
{
    protected Collection $schema;
    protected ?Collection $normalizedData;

    protected ?array $data;
    /**
     * @var array|mixed
     */
    protected mixed $class;


    //Set schema and data
    public function setDataSchema(array $data = [], object $class = null): self
    {
        $this->data = $data;
        $this->class = $class;
        return $this;
    }

    //Return schema from class
    public function requestSchemaNormalize(): self
    {
        if ($this->class) {
            if (!method_exists($this->class, 'schemaRequest')) {
                throw new \InvalidArgumentException('Переданий об\'єкт повинен мати метод schemaRequest');
            }
        }
        return $this->setSchema($this->class->schemaRequest())
            ->arrayToCollection()
            ->snakeCaseKeys()
            ->mappingSchemaNormalize()
            ->removeItemsKey();
    }

    /**
     * A function to normalize the schema.
     *
     */
    //TODO: доробити оди виклик
    public function schemaNormalize(): self{
        return $this->arrayToCollection()
            ->snakeCaseKeys()
            ->mappingSchemaNormalize()
            ->removeItemsKey();
    }


    // Extract data from request
    public function responseSchemaNormalize(): self
    {
        if ($this->class) {
            if (!method_exists($this->class, 'schemaResponse')) {
                throw new \InvalidArgumentException('Переданий об\'єкт повинен мати метод schemaResponse');
            }
        }
        return $this->setSchema($this->class->schemaResponse())
            ->arrayToCollection()
            ->mappingSchemaNormalize()
            ->camelCaseKeys();
    }


    //Set schema and convert data to collection
    public function setSchema($schema): self
    {
        $this->schema = collect($schema);
        return $this;
    }

    // Convert collection to array
    public function getNormalizedData(): array
    {
        return $this->normalizedData->toArray();
    }


    public function mappingSchemaNormalize(): self
    {
        $this->normalizedData = $this->mapDataBySchema(
            collect($this->data),
            $this->schema);

        return $this;
    }

    // Map data by schema
    public function mapFields(array $fieldMappings, ?string $sourceGroup = null, ?string $targetGroup = ''): self
    {
        $data = $targetGroup ? $this->data[$targetGroup] ?? $this->data : $this->data;

        foreach ($fieldMappings as $key => $fields) {
            if ($sourceGroup) {
                foreach ((array) $fields as $field) {
                    $data[$field] = $data[$sourceGroup][$field] ?? null;
                }
            } else {
                foreach ((array) $fields as $field) {
                    $data[$key][$field] = $data[$field] ?? null;
                }
            }
        }
        if ($targetGroup) {
            $this->data[$targetGroup] = $data;
        } else {
            $this->data = $data;
        }

        return $this;
    }


    /**
     * A function that maps data by a given schema.
     *
     * @param  Collection  $data  The data collection to map.
     * @param  Collection  $schema  The schema collection to use for mapping.
     * @param  mixed|null  $definitions  (Optional) The definitions to use for mapping.
     * @return Collection The mapped data as an array.
     */
    public function mapDataBySchema(Collection $data, Collection $schema, mixed $definitions = []): Collection
    {
        $definitions = $definitions ?: $this->schema->get('definitions');
        $schema = collect($schema->get('properties'));
        return collect($schema)->map(function ($property, $key) use (
            $data,
            $definitions,
            $schema
        ) {
            // Check if the property is a reference
            if (isset($property['$ref'])) {
                // Check if the reference is in the definitions
                $this->handleRefProperty($data, $property, $definitions, $key);

           // Check if the property is an array
            } elseif (isset($property['items'])) {
                // Check if the property is an array of references
                $this->handleItemsProperty($data, $property, $definitions, $key);
            } elseif (isset($property['properties'])) {
                $this->handleProperty($data, $property, $definitions, $key);
            } else {
                $this->handleDataKey($data, $key);
            }
            // Return the updated data
            return $data->get($key);
        })->filter(function ($value) {
            // Filter out empty values
            return $this->isNotEmpty($value);
        });
    }

    /**
     * Handle the provided key in the data collection.
     * If the key exists in the data, keep the value as is.
     * If the key does not exist, set the value to an empty string.
     *
     * @param Collection $data The data collection to handle.
     * @param string $key The key to check and update in the data collection.
     */
    protected function handleDataKey(
        Collection $data,
        string $key
    ): void {
        if ($data->has($key)) {
            // If the key exists, keep the value as is
            $data->put($key, $data->get($key));
        } else {
            // If the key does not exist, set the value to an empty string
            $data->put($key, '');
        }
    }


    /**
     * Handle a specific property in the data collection.
     * If the key exists in the data, it maps the data based on the provided schema.
     *
     * @param Collection $data The collection of data to be processed
     * @param array $property The property to be handled
     * @param mixed $definitions The definitions to be used for mapping
     * @param string $key The key to check and process in the data collection
     * @return void
     */
    protected function handleProperty(Collection $data, array $property, mixed $definitions, string $key): void
    {
        // Check if the key exists in the data
        if ($data->has($key)) {
            $data->put($key,
                $this->mapDataBySchema(collect($data->get($key)), collect($property), $definitions));
        }
    }

    /**
     * Handle the $ref property by mapping the data based on the provided schema definitions.
     *
     * @param Collection $data The data to be processed
     * @param array $property The property array containing the $ref key
     * @param mixed $definitions The schema definitions
     * @param string $key The key to store the processed data
     */
    protected function handleRefProperty(Collection $data, array $property, mixed $definitions, string $key): void
    {
        // Extract the $ref path from the property array
        $refPath = $property['$ref'];

        // Extract the definition key from the $ref path
        $definitionKey = $this->extractDefinitionKey($refPath);

        // Retrieve the definition from the definitions if it exists
        $definition = $definitions?->get($definitionKey);
        // Process the data based on the schema definition if the definition is found
        if ($definition) {
            $data->put($key, $this->mapDataBySchema(collect($data), collect($definition), $definitions));
        }
    }

    /**
     * Handle the items property by mapping data based on a schema definition.
     *
     * @param Collection $data The data collection to process
     * @param array $property The property array containing schema details
     * @param mixed $definitions The definitions to use for mapping
     * @param string $key The key to check and process in the data
     */
    protected function handleItemsProperty(
        Collection $data,
        array $property,
        mixed $definitions,
        string $key
    ): void {
        // Check if the 'items' key exists in the property
        if (!isset($property['items']['$ref'])) {
            return;
        }

        // Extract the $ref path from the property array
        $refPath = $property['items']['$ref'];
        // Extract the definition key from the $ref path
        $definitionKey = $this->extractDefinitionKey($refPath);
        // get the definition from the definitions if it exists
        $definition = $definitions?->get($definitionKey);

        // Process the data based on the schema definition if the definition is found
        $data->put($key, collect($data->get($key))->map(function ($item) use ($definition, $definitions) {
            // Process the data based on the schema definition
            if (is_iterable($item)) {
                return $this->mapDataBySchema(collect($item), collect($definition), $definitions);
            }
            // Return the original item if it is not an array
            return $item;
        }));
    }



    // Remove empty values from the normalized data.
    protected function isNotEmpty($value): bool
    {
        if (is_array($value)) {
            return !empty(array_filter($value, fn($item) => $this->isNotEmpty($item)));
        }

        if ($value instanceof Collection) {
            return $value->filter(fn($item) => $this->isNotEmpty($item))->isNotEmpty();
        }

        return !is_null($value) && $value !== '' && $value !== [];
    }


    /**
     * Removes the specified key from the data and flattens the nested structure.
     *
     * @param string $key The key to be removed and flattened. Default is 'items'.
     * @return self
     */
    public function removeItemsKey(string $key = 'items'): self
    {
        $this->normalizedData = $this->flattenItemsKey($this->normalizedData, $key);
        return $this;
    }


    /**
     * Flattens the items in the collection based on the provided key.
     *
     * @param Collection $data The collection to flatten
     * @param string $key The key to use for flattening
     * @return Collection The flattened collection
     */
    protected function flattenItemsKey(Collection $data, string $key): Collection
    {
        return $data->map(function ($value) use ($key) {
            // Check if the value is an array and convert it to a Collection
            if (is_array($value)) {
                $value = collect($value);
            }
            // Check if the value is a Collection
            if ($value instanceof Collection) {
                // Check if the Collection has the specified key
                if ($value->has($key)) {
                    $items = $value->get($key);
                    // Merge the items with the Collection and remove the key
                    if ($items) {
                        $value = $value->merge($items->all())->forget($key);
                    }
                }
                // Recursively flatten the Collection
                if ($value instanceof Collection) {
                    return $this->flattenItemsKey($value, $key);
                }
            }

            return $value;
        });
    }

    /**
     * Extract the definition key from the $ref path.
     */
    private function extractDefinitionKey(string $refPath): bool|string
    {
        // Extract the definition key from the $ref path
        $parts = explode('/', $refPath);
        return end($parts);
    }

    /**
     * snakeCaseKeys function converts keys of data to snake case.
     *
     * @return self
     */
    public function snakeCaseKeys(): self
    {
        $this->data = $this->convertKeysToSnakeCase($this->data);
        return $this;
    }



    /**
     * Convert keys from camelCase to snake_case.
     */
    protected function convertKeysToSnakeCase(array $data): array
    {
        return collect($data)->mapWithKeys(function ($value, $key) {
            $snakeKey = Str::snake($key);
            return [$snakeKey => is_array($value) ? $this->convertKeysToSnakeCase($value) : $value];
        })->all();
    }



    /**
     * camelCaseKeys function converts keys of data to camel case.
     *
     * @return self
     */
    public function camelCaseKeys(): self
    {
        $this->normalizedData = $this->convertKeysToCamelCase($this->normalizedData);
        return $this;
    }

    /**
     * Convert keys from camelCase to snake_case.
     */
    protected function convertKeysToCamelCase(Collection $data): Collection
    {
        return $data->mapWithKeys(function ($value, $key) {
            $camelKey = Str::camel($key);
            if (is_array($value)) {
                $value = $this->convertKeysToCamelCase(collect($value));
            } elseif ($value instanceof Collection) {
                $value = $this->convertKeysToCamelCase($value);
            }
            return [$camelKey => $value];
        });
    }
    /**
     * Convert the schema to collection.
     *
     * @return self
     */
    protected function arrayToCollection(): self
    {
        if (empty($this->schema)) {
            return $this;
        }
        $this->schema = $this->schema->map(function ($item) {
            return is_array($item) ? collect($item) : $item;
        });
        return $this;
    }


    /**
     * Replace IDs keys to UUID in the normalized data.
     */
    public function replaceIdsKeysToUuid($replace = []): self
    {
        $this->normalizedData = $this->replaceNestedKeys($this->normalizedData, $replace);
        return $this;
    }


    /**
     * Replaces keys in a nested array or collection based on the provided mapping
     *
     * @param  array|Collection  $data The data array or collection to be processed
     * @param  array  $replace An array mapping keys to be replaced
     * @return array|Collection The updated data with keys replaced
     */
    public function replaceNestedKeys(array|Collection $data = [], array $replace = []):array|Collection
    {
        // If $replace is empty or $data has no elements, return $data as is
        if (empty($replace) || empty($data)) {
            return $data;
        }
        // Replace the keys
        return $data->mapWithKeys(function ($value, $key) use ($replace) {
            $newKey = $key;

            // Check if the key is in the $replace array
            if (in_array($key, $replace, true)) {
                // Replace 'id' with 'uuid' or '_id' with '_uuid' in the key
                if ($key == 'id') {
                    $newKey = 'uuid';
                } else {
                    $newKey = Str::replace('Id', 'Uuid', $key);
                }
            }
            // If the value is an array or collection, process it recursively
            if ($value instanceof \Illuminate\Support\Collection) {
                $value = $this->replaceNestedKeys($value, $replace);
            }
            return [$newKey => $value];
        });
    }

}
