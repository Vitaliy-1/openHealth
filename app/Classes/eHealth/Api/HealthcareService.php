<?php

namespace App\Classes\eHealth\Api;

use Exception;
use App\Models\Division;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use App\Classes\eHealth\EHealthResponse;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Client\ConnectionException;
use App\Classes\eHealth\EHealthRequest as Request;

class HealthcareService extends Request
{
    public const string URL = '/api/healthcare_services';

    public const string ACTIONS_ACTIVATE = '/actions/activate';

    public const string ACTIONS_DEACTIVATE = '/actions/deactivate';

    public const string QUERY_DIVISION_UUID = 'division_id';

    /**
     * Get list of Healthcare Services belong to the current LegalEntity
     *
     * Important: If the second parameter `$query` is provided,
     * it will override any previously set query parameters (e.g., via `withQueryParameters()`).
     *
     * If only the URL is provided (i.e., one argument) or nothing, the request will use the internal `$this->options`.
     *
     * @param string $url The request URL.
     * @param array|null $query Optional query parameters. If provided, it replaces any existing 'query' options.
     *
     * @return PromiseInterface|EHealthResponse
     */
    public function getMany(?string $divisionUuid = null, string $url = self::URL, $query = null): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateHealthcareServicesList(...));

        $this->setDefaultPageSize();

        if ($divisionUuid) {
            $this->setDivisionUuidToQuery($divisionUuid);
        }

        $mergedQuery = array_merge(
    $this->options['query'] ?? [],
            $query ?? []
        );

        return parent::get($url, $mergedQuery);
    }

    /**
     * Set the division identifier (uuid) for the request.
     */
    protected function setDivisionUuidToQuery(string $uuid): void
    {
        $this->withQueryParameters([
            self::QUERY_DIVISION_UUID => $uuid,
        ]);
    }

    /**
     * Update the Division
     *
     * @param string $uuid
     * @param mixed $data // Data for API request
     *
     * @return EHealthResponse|PromiseInterface
     */
    public function update(string $uuid, $data = []): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateHealthcareService(...));

        return parent::patch(self::URL . '/' . $uuid, $data);
    }

    /**
     * Create the Division
     *
     * @param mixed $data // Data for API request
     *
     * @return EHealthResponse|PromiseInterface
     */
    public function create(array $data = []): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateHealthcareService(...));

        return parent::post(self::URL, data: $data);
    }

    /**
     * @param string $uuid unique eHealth identifier of the license
     * @param array $data
     *
     * @return PromiseInterface|EHealthResponse
     *
     * @throws ConnectionException
     */
    public function activate(string $uuid): PromiseInterface|EHealthResponse
    {
        return parent::patch(self::URL . '/' . $uuid . self::ACTIONS_ACTIVATE);
    }

    /**
     * @param string $uuid unique eHealth identifier of the license
     * @param array $data
     *
     * @return PromiseInterface|EHealthResponse
     *
     * @throws ConnectionException
     */
    public function deactivate(string $uuid): PromiseInterface|EHealthResponse
    {
        return parent::patch(self::URL . '/' . $uuid . self::ACTIONS_DEACTIVATE);
    }

    /**
     * Normalize healthcare services response data for database upsert operation.
     *
     * This method processes raw API response data by:
     * - Converting division UUIDs to database IDs using batch lookup
     * - Filtering out records with invalid division references
     * - Converting array fields to JSON strings for JSONB database columns
     *
     * @param array $healthcareServicesList Raw healthcare services data from API
     *
     * @return array Processed data ready for database upsert operation
     */
    public static function normalizeResponseDataForUpsert(array $healthcareServicesList): array
    {
            // Get all unique division UUIDs for batch lookup
            $divisionUuids = array_unique(array_column($healthcareServicesList, 'division_id'));

            // Batch lookup: get division IDs mapped by their UUIDs to avoid redundant queries
            $divisions = Division::whereIn('uuid', $divisionUuids)->pluck('id', 'uuid')->toArray();
            Log::debug('Division UUID to ID mapping:', ['divisions' => $divisions, 'requested_uuids' => $divisionUuids]);

            // First filter only records with valid division references
            $filteredData = array_filter($healthcareServicesList, function ($item) use ($divisions) {
                if (!isset($item['division_id'])) {
                    return false;
                }

                if (!isset($divisions[$item['division_id']])) {
                    return false;
                }

                return true;
            });

            // Now process only valid records
            return array_map(function ($item) use ($divisions) {
                // Convert division_id from UUID to ID
                $item['division_id'] = $divisions[$item['division_id']];

                // Convert JSON fields
                $jsonFields = ['category', 'type', 'coverage_area', 'available_time', 'not_available', 'licensed_healthcare_service'];

                foreach ($jsonFields as $field) {
                    $value = Arr::get($item, $field);
                    if (is_array($value)) {
                        $item[$field] = json_encode($value);
                    }
                }

                return $item;
            }, $filteredData);
    }

    /**
     * validate get Divisions input,
     * see: https://esoz.docs.apiary.io/#reference/administration/divisions/get-divisions
     */
    protected function validateHealthcareServicesList(EHealthResponse $response): array
    {
        if (! $response->successful()) {
            throw new Exception('validateHealthcareServicesList: ' . $response->body());
        }

        $replaced = [];

        $healthscareServicesList = $response->getData();

        $validationRules = ['*' => 'required|array'];

        foreach ($this->getValidationRules() as $key => $rule) {
            $validationRules["*.{$key}"] = $rule;
        }

        foreach ($healthscareServicesList as $data) {
            $replaced[] = self::replaceEHealthPropNames($data);
        }

        $validator = Validator::make($replaced, $validationRules);

        if ($validator->fails()) {
            Log::channel('e_health_errors')->error('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validate();
    }

    /**
     * Validate single division response data
     * see; https://uaehealthapi.docs.apiary.io/#reference/public.-medical-service-provider-integration-layer/divisions/get-division-details
     */
    public function validateHealthcareService(EHealthResponse $response): array
    {
        if (! $response->successful()) {
            throw new Exception('validateHealthcareService: ' . $response->body());
        }

        $data = $response->getData();

        $replaced = self::replaceEHealthPropNames($data);

        $validator = Validator::make($replaced, $this->getValidationRules());

        if ($validator->fails()) {
            Log::channel('e_health_errors')->error('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validate();
    }

    /**
     * Returns the validation rules array used for validating healthcare service data.
     *
     * @return array An associative array containing validation rules for healthcare service fields
     */
    protected function getValidationRules(): array
    {
        return [
            'available_time' => 'sometimes|array',
            'available_time.*.all_day' => 'required_with:available_time|boolean',
            'available_time.*.available_end_time' => 'required_if:available_time.*.all_day,false|nullable|string',
            'available_time.*.available_start_time' => 'required_if:available_time.*.all_day,false|nullable|string',
            'available_time.*.days_of_week' => 'required|array',
            'available_time.*.days_of_week.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'category' => 'required|array',
            'category.coding' => 'required|array',
            'category.coding.*.code' => 'required_with:category.coding|string',
            'category.coding.*.system' => 'required_with:category.coding|string',
            'category.text' => 'nullable|string',
            'comment' => 'nullable|string',
            'coverage_area' => 'nullable|array',
            'coverage_area.*' => 'required_with:coverage_area|string',
            'division_id' => 'required|string',
            'uuid' => 'required|string',
            'ehealth_inserted_at' => 'required|date',
            'ehealth_inserted_by' => 'required|uuid',
            'is_active' => 'required|boolean',
            'legal_entity_uuid' => 'required|string',
            'license_id' => 'nullable|string',
            'licensed_healthcare_service' => 'nullable|array',
            'licensed_healthcare_service.status' => 'required_with:licensed_healthcare_service|string',
            'licensed_healthcare_service.updated_at' => 'required_with:licensed_healthcare_service|string',
            'not_available' => 'sometimes|array',
            'not_available.*.description' => 'required_with:not_available|string',
            'not_available.*.during' => 'required_with:not_available|array',
            'not_available.*.during.start' => 'required_with:not_available.*.during|string',
            'not_available.*.during.end' => 'required_with:not_available.*.during|string',
            'providing_condition' => 'nullable|string',
            'speciality_type' => 'nullable|string',
            'status' => 'required|string',
            'type' => 'nullable|array',
            'type.coding' => 'required_with:type|array',
            'type.coding.*' => 'required_with:type.coding|array',
            'type.coding.*.system' => 'required_with:type.coding.*|string',
            'type.coding.*.code' => 'required_with:type.coding.*|string',
            'ehealth_updated_at' => 'required|date',
            'ehealth_updated_by' => 'required|uuid'
        ];
    }

    /**
     * @param string $url
     * @param array $data
     *
     * @return PromiseInterface|EHealthResponse
     * @throws ConnectionException
     */
    public function post(string $url = self::URL, $data = []): PromiseInterface|EHealthResponse
    {
        return parent::post($url, $data);
    }

    /**
     * Replace eHealth property names with the ones used in the application.
     * E.g., id => uuid.
     */
    protected static function replaceEHealthPropNames(array $properties): array
    {
        $replaced = [];

        foreach ($properties as $name => $value) {
            switch ($name) {
                case 'id':
                    $replaced['uuid'] = $value;
                    break;
                case 'legal_entity_id':
                    $replaced['legal_entity_uuid'] = $value;
                    break;
                case 'inserted_at':
                    $replaced['ehealth_inserted_at'] = $value;
                    break;
                case 'inserted_by':
                    $replaced['ehealth_inserted_by'] = $value;
                    break;
                case 'updated_at':
                    $replaced['ehealth_updated_at'] = $value;
                    break;
                case 'updated_by':
                    $replaced['ehealth_updated_by'] = $value;
                    break;
                default:
                    $replaced[$name] = $value;
                    break;
            }
        }

        return $replaced;
    }
}
