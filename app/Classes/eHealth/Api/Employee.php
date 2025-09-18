<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\EHealthRequest;
use App\Classes\eHealth\EHealthResponse;
use App\Core\Arr;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Employee extends EHealthRequest
{
    public const string URL = '/api/employees';

    /**
     * If true, groups the response by entities associated with the employee, e.g., employee itself, party, educations, specialities, etc.
     */
    public bool $groupByEntities = false;

    /**
     * Gets a single page of employees from E-Health.
     * It now attaches a validator to process the response.
     *
     * @param array $filters An associative array of query parameters to filter the results.
     * @param int   $page    The page number to fetch.
     *
     * @return PromiseInterface|EHealthResponse The EHealthResponse object containing the validated and transformed data.
     * @throws ConnectionException
     */
    public function getMany(array $filters, int $page = 1): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateMany(...));
        $this->setDefaultPageSize();

        $mergedQuery = array_merge(
            $this->options['query'] ?? [],
            $filters,
            ['page' => $page]
        );

        return $this->get(self::URL, $mergedQuery);
    }

    /**
     * @throws ConnectionException
     */
    public function getDetails(string $uuid, $query = null, bool $groupByEntities = false): PromiseInterface|EHealthResponse
    {
        $this->setValidator($this->validateDetails(...));
        $this->groupByEntities = $groupByEntities;

        return $this->get(self::URL . '/' . $uuid, $query);
    }

    /**
     * Validates the response for a list of employees.
     *
     * @param EHealthResponse $response The response from the eHealth API.
     * @return array The validated and transformed data.
     */
    protected function validateMany(EHealthResponse $response): array
    {
        $transformedData = [];
        foreach ($response->getData() as $item) {
            $transformedData[] = self::replaceEHealthPropNames($item);
        }

        $validator = Validator::make($transformedData, [
            '*' => 'required|array',
            '*.uuid' => 'required|uuid',
            '*.status' => 'required|string',
            '*.position' => 'required|string',
            '*.employee_type' => 'required|string',
            '*.start_date' => 'required|date_format:Y-m-d',
            '*.end_date' => 'nullable|date_format:Y-m-d',
            '*.is_active' => 'required|boolean',
            '*.party' => 'required|array',
            '*.party.uuid' => 'required|uuid',
            '*.party.no_tax_id' => 'required|boolean',
            '*.party.first_name' => 'required|string',
            '*.party.last_name' => 'required|string',
            '*.party.second_name' => 'nullable|string',
            '*.doctor' => 'sometimes|array',
            '*.doctor.specialities' => 'required_with:*.doctor|array',
            '*.doctor.specialities.*.speciality' => 'required_with:*.doctor|string',
            '*.doctor.specialities.*.speciality_officio' => 'required_with:*.doctor|boolean',
            '*.doctor.specialities.*.attestation_date' => 'required_with:*.doctor|date_format:Y-m-d',
            '*.doctor.specialities.*.attestation_name' => 'required_with:*.doctor|string',
            '*.doctor.specialities.*.certificate_number' => 'required_with:*.doctor|string',
            '*.doctor.specialities.*.level' => 'string',
        ]);

        if ($validator->fails()) {
            Log::channel('e_health_errors')->error(
                'EHealth Employee validation failed: ' . implode(', ', $validator->errors()->all())
            );
            // Ви можете тут кинути виняток, щоб зупинити процес
            // throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Validates the response for a single employee.
     *
     * @param EHealthResponse $response The response from the eHealth API.
     * @return array The validated and transformed data.
     */
    protected function validateDetails(EHealthResponse $response): array
    {
        $transformedData = self::replaceEHealthPropNames($response->getData());

        $validator = Validator::make($transformedData, [
            'uuid' => 'required|uuid',
            'status' => 'required|string',
            'position' => 'required|string',
            'employee_type' => 'required|string',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'is_active' => 'required|boolean',

            'party' => 'required|array',
            'party.uuid' => 'required|uuid',
            'party.no_tax_id' => 'required|boolean',
            'party.tax_id' => 'required|string', // might be passport data if no_tax_id is true
            'party.first_name' => 'required|string',
            'party.last_name' => 'required|string',
            'party.second_name' => 'nullable|string',
            'party.about_myself' => 'sometimes|nullable|string',
            'party.birth_date' => 'required|date_format:Y-m-d',
            'party.declaration_count' => 'required|integer',
            'party.declaration_limit' => 'required|integer',

            'party.phones' => 'required|array',
            'party.phones.*.type' => 'required|string',
            'party.phones.*.number' => 'required|string',

            'party.documents' => 'required|array',
            'party.documents.*.type' => 'required|string',
            'party.documents.*.number' => 'required|string',
            'party.document.*.issued_at' => 'required|date_format:Y-m-d',
            'party.document.*.issued_by' => 'sometimes|string',

            'doctor' => 'sometimes|array',
            'doctor.specialities' => 'required_with:*.doctor|array',
            'doctor.specialities.*.speciality' => 'required_with:*.doctor|string',
            'doctor.specialities.*.speciality_officio' => 'required_with:*.doctor|boolean',
            'doctor.specialities.*.attestation_date' => 'required_with:*.doctor|date_format:Y-m-d',
            'doctor.specialities.*.attestation_name' => 'required_with:*.doctor|string',
            'doctor.specialities.*.certificate_number' => 'required_with:*.doctor|string',
            'doctor.specialities.*.level' => 'string',

            'doctor.educations' => 'required_with:*.doctor|array',
            'doctor.educations.*.city' => 'required_with:*.doctor|string',
            'doctor.educations.*.country' => 'required_with:*.doctor|string',
            'doctor.educations.*.degree' => 'required_with:*.doctor|string',
            'doctor.educations.*.diploma_number' => 'required_with:*.doctor|string',
            'doctor.educations.*.institution_name' => 'required_with:*.doctor|string',
            'doctor.educations.*.speciality' => 'required_with:*.doctor|string',
        ]);

        if ($validator->fails()) {
            Log::channel('e_health_errors')->error(
                'EHealth Employee validation failed: ' . implode(', ', $validator->errors()->all())
            );
        }

        $validated = $validator->validated();

        if (!$this->groupByEntities) {
            return $validated;
        }

        // Party related data
        $party = Arr::pull($validated, 'party');
        $documents = Arr::pull($party, 'documents', []);
        $phones = Arr::pull($party, 'phones', []);

        // Doctor related data
        $doctor = Arr::pull($validated, 'doctor', []);
        $educations = Arr::pull($doctor, 'educations', []);
        $specialities = Arr::pull($doctor, 'specialties', []);
        $qualifications = Arr::pull($doctor, 'qualifications', []);
        $scienceDegrees = Arr::pull($doctor, 'scienceDegrees', []);

        return [
            'employee' => $validated,
            'party' => $party,
            'documents' => $documents,
            'phones' => $phones,
            'educations' => $educations,
            'specialities' => $specialities,
            'qualifications' => $qualifications,
            'scienceDegrees' => $scienceDegrees,
        ];
    }

    /**
     * Replaces eHealth property names with the ones used in the application (e.g., id -> uuid).
     *
     * @param array $properties Raw properties from a single API item.
     * @return array Properties with application-friendly names.
     */
    protected static function replaceEHealthPropNames(array $properties): array
    {
        $replaced = [];
        foreach ($properties as $name => $value) {
            switch ($name) {
                case 'id':
                    $replaced['uuid'] = $value;
                    break;
                case 'party':
                    $value['uuid'] = $value['id'];
                    unset($value['id']);
                    $replaced['party'] = $value;
                    break;
                case 'division':
                    if (is_array($value)) {
                        $value['uuid'] = $value['id'];
                        unset($value['id']);
                    }
                    $replaced['division'] = $value;
                    break;
                case 'legal_entity':
                    if (is_array($value)) {
                        $value['uuid'] = $value['id'];
                        unset($value['id']);
                    }
                    $replaced['legal_entity'] = $value;
                    break;
                default:
                    $replaced[$name] = $value;
                    break;
            }
        }

        return $replaced;
    }
}
