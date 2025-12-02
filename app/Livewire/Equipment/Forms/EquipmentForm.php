<?php

declare(strict_types=1);

namespace App\Livewire\Equipment\Forms;

use App\Core\Arr;
use App\Enums\Equipment\{AvailabilityStatus, Status};
use App\Rules\InDictionary;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rule;
use Livewire\Form;

class EquipmentForm extends Form
{
    public array $names = [['name' => '', 'type' => '']];
    public string|int|null $type = null;
    public ?string $serialNumber;
    public string $status;
    public string $recorder;
    public ?string $divisionId;
    public string $availabilityStatus;
    public ?string $inventoryNumber;
    public ?string $manufacturer;
    public ?string $manufactureDate;
    public ?string $expirationDate;
    public ?string $modelNumber;
    public ?string $lotNumber;
    public ?string $note;

    public ?string $parentId;
    public ?string $deviceDefinitionId;
    public ?array $properties;
    public ?string $uuid;
    public ?string $errorReason = null;

    /**
     * Rules based on: https://e-health-ua.atlassian.net/wiki/spaces/ESOZ/pages/17571807355/REST+API+Create+equipment+API-007-028-0001#Request-data-validation
     *
     * @return array
     */
    public function rules(): array
    {
        $requiredTypes = config('ehealth.equipment_types_with_required_serial_number', []);

        return [
            'names' => ['required', 'array', 'min:1'],
            'names.*.name' => ['required', 'string', 'max:255'],
            'names.*.type' => ['required', 'string', 'max:255', new InDictionary('device_name_type'), 'distinct'],
            'type' => [
                'required',
                Rule::when(
                    static fn (Fluent $value) => is_int($value->type),
                    ['integer'],
                    ['string', 'max:255']
                ),
                new InDictionary('device_definition_classification_type')
            ],
            'serialNumber' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn () => in_array($this->type, $requiredTypes, true)),
            ],
            'status' => ['required', 'string', Rule::in(Status::ACTIVE)],
            'recorder' => ['required', 'uuid', 'exists:employees,uuid'],
            'divisionId' => [
                'nullable',
                'uuid',
                Rule::exists('divisions', 'uuid')->where('status', Status::ACTIVE)
            ],
            'availabilityStatus' => ['required', 'string', Rule::in(AvailabilityStatus::AVAILABLE)],
            'inventoryNumber' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('equipments', 'inventory_number')
                    ->where(fn (Builder $query) => $query->where('legal_entity_id', legalEntity()->id))
                    ->ignore($this->component->equipmentId)
            ],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'manufactureDate' => ['nullable', Rule::date()->beforeOrEqual(today())],
            'expirationDate' => ['nullable', 'date_format:d.m.Y'],
            'modelNumber' => ['nullable', 'string', 'max:255'],
            'lotNumber' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'parentId' => [
                'nullable',
                'uuid',
                Rule::exists('equipments', 'uuid')->where('status', Status::ACTIVE)
            ],
            'properties' => ['nullable', 'array'],
            'properties.*.type' => ['required', 'string', 'max:255', new InDictionary('device_properties')],
            'properties.*.valueInteger' => [
                'nullable',
                'required_without_all:properties.*.valueDecimal,properties.*.valueBoolean,properties.*.valueString,',
                'prohibits:properties.*.valueDecimal,properties.*.valueBoolean,properties.*.valueString',
                'integer:strict'
            ],
            'properties.*.valueDecimal' => [
                'nullable',
                'required_without_all:properties.*.valueInteger,properties.*.valueBoolean,properties.*.valueString,',
                'prohibits:properties.*.valueInteger,properties.*.valueBoolean,properties.*.valueString',
                'decimal'
            ],
            'properties.*.valueBoolean' => [
                'nullable',
                'required_without_all:properties.*.valueInteger,properties.*.valueDecimal,properties.*.valueString',
                'prohibits:properties.*.valueInteger,properties.*.valueDecimal,properties.*.valueString',
                'boolean:strict'
            ],
            'properties.*.valueString' => [
                'nullable',
                'required_without_all:properties.*.valueInteger,properties.*.valueDecimal,properties.*.valueBoolean',
                'prohibits:properties.*.valueInteger,properties.*.valueDecimal,properties.*.valueBoolean',
                'string',
                'max:255'
            ],
            'deviceDefinitionId' => ['nullable', 'uuid'],
            // add exists: https://uaehealthapi.docs.apiary.io/#reference/public.-devices/get-device-definitions/get-device-definitions-v2
        ];
    }

    /**
     * Convert validated date to ISO 8601 and format to snake case.
     */
    public function formatForApi(array $data): array
    {
        collect($data)
            ->only(['manufactureDate', 'expirationDate'])
            ->filter()
            ->each(static function (string $value, string $key) use (&$data) {
                $data[$key] = convertToYmd($value);
            });

        return removeEmptyKeys(Arr::toSnakeCase($data));
    }

    /**
     * Redefine field names for error messages.
     *
     * @return array
     */
    protected function validationAttributes(): array
    {
        return [
            'type' => __('equipments.type_medical_device'),
            'divisionId' => __('forms.division_name')
        ];
    }
}
