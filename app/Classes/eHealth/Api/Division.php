<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Api;

use Exception;
use App\Models\LegalEntity;
use App\Traits\WorkTimeUtilities;
use  Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Promise\PromiseInterface;
use App\Classes\eHealth\EHealthResponse;
use App\Models\Division as DivisionModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Client\ConnectionException;
use App\Classes\eHealth\EHealthRequest as Request;

class Division extends Request
{
    use WorkTimeUtilities;

    public const string URL = '/api/divisions';

    public const string ACTIONS_ACTIVATE = '/actions/activate';

    public const string ACTIONS_DEACTIVATE = '/actions/deactivate';

    /**
     * Get list of Divisisons belong to the current LegalEntity
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
    public function getMany(string $url = self::URL, $query = null): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateMany(...));

        $this->setDefaultPageSize();

        $mergedQuery = array_merge(
            $this->options['query'] ?? [],
            $query ?? []
        );

        return parent::get($url, $mergedQuery);
    }

    /**
     * Update the Division
     *
     * @param string $uuid
     * @param mixed $data // Data for API request
     *
     * @return EHealthResponse|PromiseInterface
     */
    public function update(?string $uuid, $data = []): PromiseInterface|EHealthResponse
    {
        // If $uuid is missed it means that Division's record was edited is DRAFT (newly created and saved)
        if (! $uuid) {
            return $this->create($data);
        }

        $this->setValidator($this->validateOne(...));

        return parent::patch(self::URL . '/' . $uuid, $data);
    }

    /**
     * Create the Division
     *
     * @param mixed $data // Data for API request
     *
     * @return EHealthResponse|PromiseInterface
     */
    public function create($data = []): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateOne(...));

        return parent::post(self::URL, $data);
    }

    /**
     * @param string $uuid unique eHealth identifier of the license
     * @param array $data
     *
     * @return PromiseInterface|EHealthResponse
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
     * @throws ConnectionException
     */
    public function deactivate(string $uuid): PromiseInterface|EHealthResponse
    {
        return parent::patch(self::URL . '/' . $uuid . self::ACTIONS_DEACTIVATE);
    }

    /**
      * Schema Create/Update Division
      *
      * @return array
      */
    public static function schemaRequest(): array
    {
        return [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string'
                ],
                'addresses' => [
                    'type' => 'array'
                ],
                'phones' => [
                    'type' => 'array'
                ],
                'email' => [
                    'type' => 'string'
                ],
                'working_hours' => [
                    'type' => 'object',
                    'properties' => [
                        'mon' => [
                            'type' => 'array'
                        ],
                        'tue' => [
                            'type' => 'array'
                        ],
                        'wed' => [
                            'type' => 'array'
                        ],
                        'thu' => [
                            'type' => 'array'
                        ],
                        'fri' => [
                            'type' => "array"
                        ],
                        'sat' => [
                            'type' => 'array'
                        ],
                        'sun' => [
                            'type' => 'array'
                        ]
                    ]
                ],
                'type' => [
                    'type' => 'string',
                    'enum' => [
                        'ACTIVE',
                        'INACTIVE',
                        'DRAFT',
                        'UNSYNCED'
                    ]
                ],
                'legal_entity_id' => [
                    'type' => 'string'
                ],
                'external_id' => [
                    'type' => 'string'
                ],
                'location' => [
                    'type' => 'object',
                    'properties' => [
                        'latitude' => [
                            'type' => 'number'
                        ],
                        'longitude' => [
                            'type' => 'number'
                        ]
                    ],
                    'required' => [
                        'latitude',
                        'longitude'
                    ]
                ]
            ],
            'required' => [
                'name',
                'addresses',
                'phones',
                'email',
                'type'
            ]
        ];
    }

    /**
     * Normalizes division response data for database upsert operation.
     *
     * This method transforms the division data received from eHealth API to a format
     * suitable for bulk upsert operation by:
     * 1. Removing nested relationship data (legal_entity_uuid, addresses, phones)
     * 2. Setting the legal_entity_id from the current legal entity
     * 3. Converting JSON-serializable fields (location, working_hours) to JSON strings
     *
     * @param array $divisionsList Array of division data from eHealth API
     *
     * @return array Normalized array ready for database upsert operation
     */
    public static function normalizeResponseDataForUpsert(array $divisionsList, LegalEntity $legalEntity): array
    {
        foreach ($divisionsList as $index => $division) {
            unset($divisionsList[$index]['legal_entity_uuid']);
            unset($divisionsList[$index]['addresses']);
            unset($divisionsList[$index]['phones']);

            $divisionsList[$index]['legal_entity_id'] = $legalEntity->id;

            $divisionsList[$index]['location'] = empty($division['location'])
                ? null
                : json_encode($division['location']);

            $divisionsList[$index]['working_hours'] = empty($division['working_hours'])
                ? null
                : json_encode($division['working_hours']);
        }

        return $divisionsList;
    }

    /**
     * Prepares relationship data (table: 'addresses' and 'phones') for bulk database insertion.
     *
     * This method processes a list of division data from the eHealth API,
     * extracts the nested 'addresses' and 'phones' arrays, and transforms them
     * into a flat structure suitable for a bulk `insert()` operation. It uses a
     * provided map to link the eHealth UUIDs to local database primary keys.
     *
     * @param array $dvisionsData The raw division data list from the eHealth API.
     * @param Collection $divisionIds A collection mapping UUIDs (keys) to local division IDs (values).
     *
     * @return array An associative array containing 'divisionIds', 'addresses', and 'phones' data ready for the repository.
     */
    public static function getRelationshipData(array $dvisionsData, Collection $divisionIds): array
    {
        $divisionIdsArray = $divisionIds->values()->toArray();

        $now = now();

        $addressesData = [];
        $phonesData = [];

        foreach ($dvisionsData as $division) {
            $divisionId = $divisionIds->get($division['uuid']);

            // ADDRESSES PPREPARE
            foreach ($division['addresses'] ?? [] as $address) {
                $addressesData[] = [
                    'type' => $address['type'],
                    'country' => $address['country'],
                    'area' => $address['area'],
                    'region' => $address['region'] ?? null,
                    'settlement' => $address['settlement'],
                    'settlement_type' => $address['settlement_type'],
                    'settlement_id' => $address['settlement_id'],
                    'street_type' => $address['street_type'] ?? null,
                    'street' => $address['street'] ?? null,
                    'building' => $address['building'] ?? null,
                    'apartment' => $address['apartment'] ?? null,
                    'zip' => $address['zip'] ?? null,
                    'addressable_id' => $divisionId,
                    'addressable_type'=> DivisionModel::class,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            // PHONES PREPARE
            foreach ($division['phones'] ?? [] as $phone) {
                $phonesData[] = [
                    'type' => $phone['type'],
                    'number' => $phone['number'],
                    'phoneable_id' => $divisionId,
                    'phoneable_type'=> DivisionModel::class,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }

        return [
            'divisionIds' => $divisionIdsArray,
            'addresses' => $addressesData,
            'phones' => $phonesData
        ];
    }

    /**
     * validate get Divisions input,
     * see: https://esoz.docs.apiary.io/#reference/administration/divisions/get-divisions
     */
    protected function validateMany(EHealthResponse $response): array
    {
        if (! $response->successful()) {
            throw new Exception('validateMany: ' . $response->body());
        }

        $replaced = [];

        $divisionsList = $response->getData();

        $validationRules = ['*' => 'required|array'];

        foreach ($this->getValidationRules() as $key => $rule) {
            $validationRules["*.{$key}"] = $rule;
        }

        foreach ($divisionsList as $data) {
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
    protected function validateOne(EHealthResponse $response): array
    {
        if (! $response->successful()) {
            throw new Exception('validateOne: ' . $response->body());
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
            'addresses' => 'required|array',
            'addresses.*.apartment' => 'nullable|string',
            'addresses.*.area' => 'required|string',
            'addresses.*.building' => 'nullable|string',
            'addresses.*.country' => 'required|string',
            'addresses.*.region' => 'nullable|string',
            'addresses.*.settlement' => 'required|string',
            'addresses.*.settlement_id' => 'required|string',
            'addresses.*.settlement_type' => 'required|string',
            'addresses.*.street' => 'nullable|string',
            'addresses.*.street_type' => 'nullable|string',
            'addresses.*.type' => 'required|string',
            'addresses.*.zip' => 'nullable|string',
            'uuid' => 'required|uuid',
            'email' => 'required|string',
            'external_id' => 'nullable|string',
            'location' => 'nullable|array',
            'location.latitude' => 'required_with:location|numeric',
            'location.longitude' => 'required_with:location|numeric',
            'mountain_group' => 'sometimes|boolean',
            'name' => 'required|string',
            'phones' => 'required|array',
            'phones.*.number' => 'required|string',
            'phones.*.type' => 'required|string',
            'phones.*.note' => 'sometimes|string',
            'status' => 'required|string',
            'type' => 'required|string',
            'working_hours' => 'nullable|array',
            'working_hours.sun' => 'nullable|array',
            'working_hours.sun.*.0' => 'required|string',
            'working_hours.sun.*.1' => 'required|string',
            'working_hours.mon' => 'nullable|array',
            'working_hours.mon.*.0' => 'required|string',
            'working_hours.mon.*.1' => 'required|string',
            'working_hours.tue' => 'nullable|array',
            'working_hours.tue.*.0' => 'required|string',
            'working_hours.tue.*.1' => 'required|string',
            'working_hours.wed' => 'nullable|array',
            'working_hours.wed.*.0' => 'required|string',
            'working_hours.wed.*.1' => 'required|string',
            'working_hours.thu' => 'nullable|array',
            'working_hours.thu.*.0' => 'required|string',
            'working_hours.thu.*.1' => 'required|string',
            'working_hours.fri' => 'nullable|array',
            'working_hours.fri.*.0' => 'required|string',
            'working_hours.fri.*.1' => 'required|string',
            'working_hours.sat' => 'nullable|array',
            'working_hours.sat.*.0' => 'required|string',
            'working_hours.sat.*.1' => 'required|string',
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
            switch ($name) {
                case 'id':
                    $replaced['uuid'] = $value;
                    break;
                case 'legal_entity_id':
                    $replaced['legal_entity_uuid'] = $value;
                    break;
                default:
                    $replaced[$name] = $value;
                    break;
            }
        }

        return $replaced;
    }
}
