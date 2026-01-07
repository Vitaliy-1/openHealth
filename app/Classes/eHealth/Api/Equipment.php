<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Api;

use App\Exceptions\EHealth\EHealthResponseException;
use App\Exceptions\EHealth\EHealthValidationException;
use App\Models\LegalEntity as LegalEntityModel;
use App\Models\Division as DivisionModel;
use App\Models\Equipment as EquipmentModel;
use App\Models\Employee\Employee as EmployeeModel;
use App\Rules\InDictionary;
use Illuminate\Support\Facades\Log;
use App\Classes\eHealth\EHealthResponse;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Client\ConnectionException;
use App\Classes\eHealth\EHealthRequest as Request;
use Illuminate\Validation\Rule;

class Equipment extends Request
{
    protected const string URL = '/api/equipment';

    private array $divisionMap = [];
    private array $legalEntityMap = [];
    private array $parentMap = [];
    private array $recorderMap = [];

    /**
     * Get list of equipments.
     *
     * @param  array{
     *     division_id?: string,
     *     device_definition_id?: string,
     *     type?: string, // Code from dictionary device_definition_classification_type
     *     status?: \App\Enums\Equipment\Status::class,
     *     model_number?: string,
     *     manufacturer?: string,
     *     availability_status?: \App\Enums\Equipment\AvailabilityStatus::class,
     *     recorder?: string,
     *     inventory_number?: string,
     *     serial_number?: string,
     *     name?: string,
     *     created_from?: string,
     *     created_to?: string,
     *     page?: int,
     *     page_size?: int
     * }  $query
     * @return PromiseInterface|EHealthResponse
     * @throws ConnectionException|EHealthValidationException|EHealthResponseException
     *
     * @see https://uaehealthapi.docs.apiary.io/#reference/public.-medical-service-provider-integration-layer/equipment/get-equipment-list
     */
    public function getMany(array $query = []): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateMany(...));
        $this->setMapper($this->mapMany(...));
        $this->setDefaultPageSize();

        $mergedQuery = array_merge($this->options['query'], $query);

        return $this->get(self::URL, $mergedQuery);
    }

    /**
     * Register equipment and bind it with division of legal entity.
     *
     * @param  array  $data  // Data for API request
     * @return EHealthResponse|PromiseInterface
     * @throws ConnectionException|EHealthValidationException|EHealthResponseException
     *
     * @see https://uaehealthapi.docs.apiary.io/#reference/public.-medical-service-provider-integration-layer/equipment/create-equipment
     */
    public function create(array $data = []): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateResponse(...));
        $this->setMapper($this->mapCreate(...));

        return $this->post(self::URL, $data);
    }

    /**
     * Method to changes equipment status to inactive or entered_in_error.
     *
     * @param  string  $uuid
     * @param  array  $data
     * @return EHealthResponse|PromiseInterface
     * @throws ConnectionException|EHealthValidationException|EHealthResponseException
     *
     * @see https://uaehealthapi.docs.apiary.io/#reference/public.-medical-service-provider-integration-layer/equipment/change-equipment-status
     */
    public function changeStatus(string $uuid, array $data): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateResponse(...));

        return $this->patch(self::URL . "/$uuid" . '/actions/change_status', $data);
    }

    /**
     * Method to change equipment availability status.
     *
     * @param  string  $uuid
     * @param  array  $data
     * @return EHealthResponse|PromiseInterface
     * @throws ConnectionException|EHealthValidationException|EHealthResponseException
     *
     * @see https://uaehealthapi.docs.apiary.io/#reference/public.-medical-service-provider-integration-layer/equipment/change-equipment-availability-status
     */
    public function changeAvailabilityStatus(string $uuid, array $data): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateResponse(...));

        return $this->patch(self::URL . "/$uuid" . '/actions/change_availability_status', $data);
    }

    /**
     * Validate healthcare service response (create, activate, deactivate),
     * see: https://uaehealthapi.docs.apiary.io/#reference/public.-medical-service-provider-integration-layer/healthcare-services/create-healthcare-service
     */
    protected function validateResponse(EHealthResponse $response): array
    {
        $data = $response->getData();

        $replaced = self::replaceEHealthPropNames($data);

        $validator = Validator::make($replaced, $this->validationRules());

        if ($validator->fails()) {
            Log::channel('e_health_errors')->error('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validate();
    }

    /**
     * Validate list of equipments.
     *
     * @param  EHealthResponse  $response
     * @return array
     */
    protected function validateMany(EHealthResponse $response): array
    {
        $replaced = [];
        foreach ($response->getData() as $data) {
            $replaced[] = self::replaceEHealthPropNames($data);
        }

        // Get unique UUIDs from response.
        $uuids = [
            'division' => collect($replaced)->pluck('division_id')->filter()->unique()->values(),
            'legal_entity' => collect($replaced)->pluck('legal_entity_id')->filter()->unique()->values(),
            'parent' => collect($replaced)->pluck('parent_id')->filter()->unique()->values(),
            'recorder' => collect($replaced)->pluck('recorder')->filter()->unique()->values(),
        ];

        // Set IDs and UUID for validation and map
        $this->divisionMap = DivisionModel::whereIn('uuid', $uuids['division'])->pluck('id', 'uuid')->toArray();
        $this->legalEntityMap = LegalEntityModel::whereIn('uuid', $uuids['legal_entity'])->pluck('id', 'uuid')
            ->toArray();
        $this->parentMap = EquipmentModel::whereIn('uuid', $uuids['parent'])->pluck('id', 'uuid')->toArray();
        $this->recorderMap = EmployeeModel::whereIn('uuid', $uuids['recorder'])->pluck('id', 'uuid')->toArray();

        // Get unique UUIDs for validations.
        $divisionUuids = array_keys($this->divisionMap);
        $legalEntityUuids = array_keys($this->legalEntityMap);
        $employeeUuids = array_keys($this->recorderMap);

        // Get basic rules
        $rules = collect($this->validationRules())
            ->mapWithKeys(static fn ($rule, $key) => ["*.$key" => $rule])
            ->toArray();

        // Add rule 'in' to avoid N+1 queries
        $rules['*.division_id'] = ['nullable', 'uuid', Rule::in($divisionUuids)];
        $rules['*.legal_entity_id'] = ['required', 'uuid', Rule::in($legalEntityUuids)];
        $rules['*.recorder'] = [
            'required',
            'uuid',
            // Rule::in($employeeUuids) TODO - uncomment after fixing equipment sync on the eHealth side
        ];
        $rules['*.parent_id'] = ['nullable', 'uuid'];

        $validator = Validator::make($replaced, $rules);

        if ($validator->fails()) {
            Log::channel('e_health_errors')->error('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validate();
    }

    /**
     * Map UUID values to ID.
     *
     * @param  array  $validated
     * @return array
     */
    protected function mapCreate(array $validated): array
    {
        if (isset($validated['division_id'])) {
            $validated['division_id'] = DivisionModel::whereUuid($validated['division_id'])->value('id');
        }

        if (isset($validated['parent_id'])) {
            $validated['parent_id'] = EquipmentModel::whereUuid($validated['parent_id'])->value('id');
        }

        $validated['legal_entity_id'] = LegalEntityModel::whereUuid($validated['legal_entity_id'])->value('id');
        $validated['recorder'] = EmployeeModel::whereUuid($validated['recorder'])->value('id');

        return $validated;
    }

    /**
     * Map UUID values to ID for multiple records.
     *
     * @param  array  $validated
     * @return array
     */
    protected function mapMany(array $validated): array
    {
        return collect($validated)->map(
            function (array $item) {
                if (!empty($item['division_id'])) {
                    $item['division_id'] = $this->divisionMap[$item['division_id']] ?? null;
                }

                $item['legal_entity_id'] = $this->legalEntityMap[$item['legal_entity_id']] ?? null;

                if (!empty($item['parent_id'])) {
                    $item['parent_id'] = $this->parentMap[$item['parent_id']] ?? null;
                }

                $item['recorder'] = $this->recorderMap[$item['recorder']] ?? null;

                return $item;
            }
        )->toArray();
    }

    /**
     * List of validation rules for healthcare service.
     *
     * @return array
     */
    protected function validationRules(): array
    {
        return [
            'availability_status' => ['required', 'string', new InDictionary('equipment_availability_statuses')],
            'device_definition_id' => ['nullable', 'uuid'],
            'division_id' => ['nullable', 'uuid', 'exists:divisions,uuid'],
            'error_reason' => ['nullable', 'string', 'max:255'],
            'expiration_date' => ['nullable', 'date'],
            'uuid' => ['required', 'uuid'],
            'ehealth_inserted_at' => ['required', 'date'],
            'ehealth_inserted_by' => ['required', 'uuid'],
            'inventory_number' => ['nullable', 'string', 'max:255'],
            'legal_entity_id' => ['required', 'uuid', 'exists:legal_entities,uuid'],
            'lot_number' => ['nullable', 'string', 'max:255'],
            'manufacture_date' => ['nullable', 'date'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model_number' => ['nullable', 'string', 'max:255'],
            'names' => ['required', 'array'],
            'names.*.name' => ['required', 'string', 'max:255'],
            'names.*.type' => ['required', 'string', new InDictionary('device_name_type')],
            'note' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'uuid', 'exists:equipments,uuid'],
            'properties' => ['nullable', 'array'],
            'properties.*.type' => ['required', 'string', 'max:255', new InDictionary('device_properties')],
            'properties.*.valueInteger' => ['nullable', 'integer:strict'],
            'properties.*.valueDecimal' => ['nullable', 'decimal'],
            'properties.*.valueBoolean' => ['nullable', 'boolean:strict'],
            'properties.*.valueString' => ['nullable', 'string', 'max:255'],
            'recorder' => ['required', 'uuid', 'exists:employees,uuid'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', new InDictionary('equipment_statuses')],
            'type' => ['required', 'string', 'max:255', new InDictionary('device_definition_classification_type')],
            'ehealth_updated_at' => ['required', 'date'],
            'ehealth_updated_by' => ['required', 'uuid']
        ];
    }

    /**
     * Replace eHealth property names with the ones used in the application.
     * E.g., id => uuid.
     */
    protected static function replaceEHealthPropNames(array $properties): array
    {
        $replaced = [];

        foreach ($properties as $name => $value) {
            $newName = match ($name) {
                'id' => 'uuid',
                'inserted_at' => 'ehealth_inserted_at',
                'inserted_by' => 'ehealth_inserted_by',
                'updated_at' => 'ehealth_updated_at',
                'updated_by' => 'ehealth_updated_by',
                default => $name
            };

            $replaced[$newName] = $value;
        }

        return $replaced;
    }
}
