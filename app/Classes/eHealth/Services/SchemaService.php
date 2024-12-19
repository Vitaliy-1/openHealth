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

    public function setDataSchema(array $data = [], object $class = null): self
    {

        $this->data = $data;
        $this->class = $class;
        return $this;
    }

    public function requestSchemaNormalize(): SchemaService
    {
        if ($this->class) {
            if (!method_exists($this->class, 'schemaRequest')) {
                throw new \InvalidArgumentException('Переданий об\'єкт повинен мати метод schemaRequest');
            }
        }
        return $this->setSchema($this->class->schemaRequest())
            ->arrayToCollection()
            ->snakeCaseKeys()
            ->mappingSchemaNormalize();
    }

    public function responseSchemaNormalize(): self
    {
        if ($this->class) {
            if (!method_exists($this->class, 'schemaResponse')) {
                throw new \InvalidArgumentException('Переданий об\'єкт повинен мати метод schemaResponse');
            }
        }
        return $this->setSchema($this->class->schemaResponse())
            ->arrayToCollection()
            ->snakeCaseKeys()
            ->mappingSchemaNormalize();
    }

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
        });
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
     * @param  mixed  $definitions  description
     * @param  string  $key  description
     */

    protected function handleProperty(Collection $data, array $property, mixed $definitions, string $key): void
    {
        // Check if the key exists in the data
        if ($data->has($key)) {
            $data->put($key,
                $this->mapDataBySchema(collect($data->get($key)), collect($property), $definitions));
        }
    }


    // Handle $ref property
    protected function handleRefProperty(Collection $data, array $property, mixed $definitions, string $key): void
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
        mixed $definitions,
        string $key
    ): void {
        // Check if the key exists in the data
        $refPath = $property['items']['$ref'];
        // Extract the definition key from the $ref path
        $definitionKey = $this->extractDefinitionKey($refPath);
        // Get the definition
        $definition = $definitions?->get($definitionKey);
        if ($data->has($key)) {
            $data->put($key, collect($data->get($key))->map(function ($item) use ($definition, $definitions) {
                return $this->mapDataBySchema(collect($item), collect($definition), $definitions);
            }));
        }
    }


    public function filterNormalizedData(): self
    {
        $this->normalizedData = $this->removeEmptyValuesFromCollection($this->normalizedData);
        return $this;
    }


    // Remove specific key from normalized data
    public function removeItemsKey(string $key = 'items'): self
    {
        $this->normalizedData = $this->flattenItemsKey($this->normalizedData, $key);
        return $this;
    }


    protected function flattenItemsKey(Collection $data, string $key): Collection
    {
        return $data->map(function ($value) use ($key) {
            if (is_array($value)) {
                $value = collect($value);
            }

            if ($value instanceof Collection) {
                if ($value->has($key)) {
                    $items = $value->get($key);
                    if ($items) {
                        $value = $value->merge($items->all())->forget($key);
                    }
                }
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
    private function extractDefinitionKey( string $refPath): bool|string
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

    // Convert array to collection
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



    // Remove empty values from collection
    public function removeEmptyValuesFromCollection($collection)
    {
        return $collection->map(function ($item) {
            if ($item instanceof Collection) {
                return $this->removeEmptyValuesFromCollection($item);
            }
            if (is_array($item)) {
                return $this->removeEmptyValuesFromCollection(collect($item));
            }
            return !empty($item) ? $item : null;
        })->filter();
    }

    /**
     * Replace IDs keys to UUID in the normalized data.
     */
    public function replaceIdsKeysToUuid($replace = []): self
    {

        $this->normalizedData = $this->replaceNestedKeys($this->normalizedData, $replace);
        return $this;
    }

    public function replaceNestedKeys($data = [], $replace = [])
    {
        // If $replace is empty or $data has no elements, return $data
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
                }else{
                    $newKey = Str::replace('_id', '_uuid', $key);
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
