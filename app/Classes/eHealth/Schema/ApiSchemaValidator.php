<?php
namespace App\Classes\eHealth\Schema;
use InvalidArgumentException;
use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;

class ApiSchemaValidator
{
    protected string $schemaPath;

    public function __construct(string $schemaPath)
    {
        $this->schemaPath = $schemaPath;
    }

    /**
     * Load JSON Schema.
     */
    protected function loadSchema(): object
    {
        $schemaContent = file_get_contents($this->schemaPath);
        return json_decode($schemaContent);
    }

    /**
     * Check JSON Schema.
     */
    public function validate(array $data): void
    {
        $validator = new Validator();
        $schema = $this->loadSchema();
        $validator->validate($data, $schema, Constraint::CHECK_MODE_APPLY_DEFAULTS);
        if (!$validator->isValid()) {
            $errors = array_map(fn($e) => "{$e['property']}: {$e['message']}", $validator->getErrors());
            throw new InvalidArgumentException("Validation failed: " . implode(", ", $errors));
        }
    }

    /**
     *
     * Normalize JSON Schema.
     */
    public function normalizeData(array $data): array
    {
        $this->validate($data);
        $schema = $this->loadSchema();

        // Рекурсивно заповнюємо значення за замовчуванням
        return $this->applyDefaults($data, $schema->properties);
    }

    /**
     * @param  array  $data
     * @param  object  $properties
     * @return array
     */
    protected function applyDefaults(array $data, object $properties): array
    {
        foreach ($properties as $key => $property) {
            if (!array_key_exists($key, $data) && property_exists($property, 'default')) {
                $data[$key] = $property->default;
            }
            if (isset($data[$key]) && isset($property->properties)) {
                $data[$key] = $this->applyDefaults($data[$key], $property->properties);
            }
        }
        return $data;
    }
}
