<?php

namespace App\Classes\eHealth\Services;

use Illuminate\Support\Collection;

class SchemaService
{
    protected Collection $schema;


    /**
     * A function that normalizes data by a given schema.
     *
     * @param  array  $data  description
     * @param  object  $schema  description
     * @return array
     */
    public function requestSchemaNormalize(array $data, object $schema): array
    {
        $this->schema = $this->arrayToCollection(json_decode($schema->schemaRequest(), true));

        // Convert keys to snake_case
        $data = $this->convertKeysToSnakeCase($data);
        // Normalize data (validate, typecast, and autofill defaults)
        return $this->mapDataBySchema(collect($data), $this->schema);
    }


    /**
     * A function that maps data by a given schema.
     *
     * @param  Collection  $data  The data collection to map.
     * @param  Collection  $schema  The schema collection to use for mapping.
     * @param  mixed|null  $definitions  (Optional) The definitions to use for mapping.
     * @return array The mapped data as an array.
     */
    public function mapDataBySchema(Collection $data, Collection $schema, mixed $definitions = null): array
    {
        $definitions = $definitions ?: $this->schema->get('definitions');
        $schema = collect($schema->get('properties'));
        return $this->removeItemsKey(collect($schema)->map(function ($property, $key) use (
            $data,
            $definitions,
            $schema
        ) {
            if (isset($property['$ref'])) {
                $this->handleRefProperty($data, $property, $definitions, $key);
            } elseif (isset($property['items'])) {
                $this->handleItemsProperty($data, $property, $definitions, $key);
            } elseif (isset($property['properties'])) {
                $this->handleProperty($data, $property, $definitions, $key);
            } else {
                $this->handleDataKey($data, $key);
            }
            return $data->get($key);
        })->filter()->toArray());
    }

    /**
     * @param  Collection  $data  The data collection to handle.
     * @param  string  $key  The key to handle in the data collection.
     */

    protected function handleDataKey(
        Collection $data,
        string $key
    ): void {
        // Check if the key exists in the data
        if ($data->has($key)) {
            $data->put($key, $data->get($key));
        } else {
            $data->put($key, '');
        }
    }

    /**
     * A description of the entire PHP function.
     *
     * @param  Collection  $data  description
     * @param  array  $property  description
     * @param  Collection  $definitions  description
     * @param  string  $key  description
     */

    protected function handleProperty(Collection $data, array $property, Collection $definitions, string $key): void
    {
        // Check if the key exists in the data
        if ($data->has($key)) {
            $data->put($key,
                $this->mapDataBySchema(collect($data->get($key)), collect($property), $definitions));
        }
    }


    // Handle $ref property
    protected function handleRefProperty(Collection $data, array $property, Collection $definitions, string $key): void
    {
        // Check if the key exists in the data
        $refPath = $property['$ref'];
        // Extract the definition key from the $ref path
        $definitionKey = $this->extractDefinitionKey($refPath);
        // Get the definition
        $definition = $definitions?->get($definitionKey);
        if ($definition) {
            $data->put($key, $this->mapDataBySchema(collect($data), collect($definition), $definitions));
        }
    }


    protected function handleItemsProperty(
        Collection $data,
        array $property,
        Collection $definitions,
        string $key
    ): void {
        // Check if the key exists in the data
        $refPath = $property['items']['$ref'];
        // Extract the definition key from the $ref path
        $definitionKey = $this->extractDefinitionKey($refPath);
        // Get the definition
        $definition = $definitions?->get($definitionKey);
        if ($data->has($key)) {
            $data->put($key,
                collect($data->get($key))->map(function ($item) use ($definition, $definitions) {
                    return $this->mapDataBySchema(collect($item), collect($definition), $definitions);
                }));
        }
    }


    protected function removeItemsKey(array $data): array
    {
        return collect($data)->map(function ($value) {
            if (is_array($value)) {
                // if $value is an array, remove the 'items' key
                if (isset($value['items']) && is_array($value['items'])) {
                    return $this->removeItemsKey($value['items']);
                }
                return $this->removeItemsKey($value);
            }
            return $value;
        })->all();
    }

    private function extractDefinitionKey($refPath): bool|string
    {
        // Extract the definition key from the $ref path
        $parts = explode('/', $refPath);
        return end($parts);
    }


    /**
     * Convert keys from camelCase to snake_case.
     */
    protected function convertKeysToSnakeCase(array $data): array
    {
        return collect($data)->mapWithKeys(function ($value, $key) {
            $snakeKey = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $key));
            return [$snakeKey => is_array($value) ? $this->convertKeysToSnakeCase($value) : $value];
        })->all();
    }


    // Convert array to collection
    protected static function arrayToCollection(string|array $array): Collection
    {
        if (is_string($array)) {
            $array = json_decode($array, true);
        }
        if (empty($array)) {
            return collect();
        }
        $collection = collect($array);
        return $collection->map(function ($item) {
            return is_array($item) ? collect($item) : $item;
        });
    }
}
