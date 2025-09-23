<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\EHealthRequest;
use App\Classes\eHealth\EHealthResponse;
use App\Core\Arr;
use App\Models\Employee\EmployeeRequest;
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
        }

        return $validator->validated();
    }

    /**
     * Validates the response for a single employee.
     *
     * @param EHealthResponse $response The response from the eHealth API.
     *
     * @return array The validated and transformed data.
     */
    protected function validateDetails(EHealthResponse $response): array
    {
        $transformedData = self::replaceEHealthPropNames($response->getData());

        $employeeTypeKey = strtolower($transformedData['employee_type'] ?? '');
        $doctorTypes = implode(',', config('ehealth.doctors_type', []));

        // =================================================================================
        //  COMMENT REGARDING DATA VALIDATION FROM E-HEALTH
        // =================================================================================
        //  The validation logic below has been relaxed compared to the original implementation.
        //  Reason: The E-Health API sometimes returns incomplete or logically incorrect data.
        //  For example, for documents, the issue date (issued_at) may be missing,
        //  and for qualifications, the expiration date (valid_to) may be earlier than the issue date.
        //
        //  To avoid synchronization failures due to such data issues on the E-Health side,
        //  we accept this data but leave this comment as a warning.
        //  In an ideal world, these rules should be stricter (e.g., 'required' instead of 'nullable').
        // =================================================================================

        $rules = [
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
            'party.tax_id' => 'required|string',
            'party.first_name' => 'required|string',
            'party.last_name' => 'required|string',
            'party.second_name' => 'nullable|string',
            'party.about_myself' => 'sometimes|nullable|string',
            'party.birth_date' => 'required|date_format:Y-m-d',
            'party.declaration_count' => 'required|integer',
            'party.declaration_limit' => 'required|integer',

            'party.phones' => 'required|array|min:1',
            'party.phones.*.type' => 'required|string',
            'party.phones.*.number' => 'required|string',

            'party.documents' => 'required|array|min:1',
            'party.documents.*.type' => 'required|string',
            'party.documents.*.number' => 'required|string',
//            'party.documents.*.issued_by' => 'sometimes|string',
            'party.documents.*.issued_by' => 'sometimes|nullable|string',
            //            'party.documents.*.issued_at' => 'required|date_format:Y-m-d',
            'party.documents.*.issued_at' => 'nullable|date_format:Y-m-d',
        ];

        if (!empty($employeeTypeKey)) {
            $rules[$employeeTypeKey] = 'required_if:employee_type,' . $doctorTypes . '|array';

            $rules["{$employeeTypeKey}.specialities"] = "required_with:{$employeeTypeKey}|array|min:1";
            $rules["{$employeeTypeKey}.specialities.*.speciality"] = "required|string";
            $rules["{$employeeTypeKey}.specialities.*.speciality_officio"] = "required|boolean";
            $rules["{$employeeTypeKey}.specialities.*.attestation_date"] = "required|date_format:Y-m-d";
            $rules["{$employeeTypeKey}.specialities.*.attestation_name"] = "required|string";
            $rules["{$employeeTypeKey}.specialities.*.certificate_number"] = "required|string";
            $rules["{$employeeTypeKey}.specialities.*.level"] = "required|string";
            $rules["{$employeeTypeKey}.specialities.*.qualification_type"] = "required|string";

            $rules["{$employeeTypeKey}.educations"] = "required_with:{$employeeTypeKey}|array|min:1";
            $rules["{$employeeTypeKey}.educations.*.city"] = "required|string";
            $rules["{$employeeTypeKey}.educations.*.country"] = "required|string";
            $rules["{$employeeTypeKey}.educations.*.degree"] = "required|string";
            $rules["{$employeeTypeKey}.educations.*.diploma_number"] = "required|string";
            $rules["{$employeeTypeKey}.educations.*.institution_name"] = "required|string";
            $rules["{$employeeTypeKey}.educations.*.speciality"] = "required|string";
            $rules["{$employeeTypeKey}.educations.*.issued_date"] = "nullable|date_format:Y-m-d";

            $rules["{$employeeTypeKey}.science_degree"] = 'sometimes|nullable|array';
            $rules["{$employeeTypeKey}.science_degree.country"] = "required_with:{$employeeTypeKey}.science_degree|string";
            $rules["{$employeeTypeKey}.science_degree.city"] = "required_with:{$employeeTypeKey}.science_degree|string";
            $rules["{$employeeTypeKey}.science_degree.degree"] = "required_with:{$employeeTypeKey}.science_degree|string";
            $rules["{$employeeTypeKey}.science_degree.institution_name"] = "required_with:{$employeeTypeKey}.science_degree|string";
            $rules["{$employeeTypeKey}.science_degree.diploma_number"] = "required_with:{$employeeTypeKey}.science_degree|string";
            $rules["{$employeeTypeKey}.science_degree.speciality"] = "required_with:{$employeeTypeKey}.science_degree|string";
            $rules["{$employeeTypeKey}.science_degree.issued_date"] = 'nullable|date_format:Y-m-d';

            $rules["{$employeeTypeKey}.qualifications"] = 'sometimes|array';
            $rules["{$employeeTypeKey}.qualifications.*.type"] = "required_with:{$employeeTypeKey}.qualifications|string";
            $rules["{$employeeTypeKey}.qualifications.*.institution_name"] = "required_with:{$employeeTypeKey}.qualifications|string";
            $rules["{$employeeTypeKey}.qualifications.*.speciality"] = "required_with:{$employeeTypeKey}.qualifications|string";
            $rules["{$employeeTypeKey}.qualifications.*.issued_date"] = "required_with:{$employeeTypeKey}.qualifications|date_format:Y-m-d";
            $rules["{$employeeTypeKey}.qualifications.*.certificate_number"] = "required_with:{$employeeTypeKey}.qualifications|string";
            //            $rules["{$employeeTypeKey}.qualifications.*.valid_to"] = "nullable|date_format:Y-m-d|after_or_equal:{$employeeTypeKey}.qualifications.*.issued_date";
            $rules["{$employeeTypeKey}.qualifications.*.valid_to"] = "nullable|date_format:Y-m-d";
            $rules["{$employeeTypeKey}.qualifications.*.additional_info"] = 'nullable|string';
        }

        $validator = Validator::make($transformedData, $rules);

        if ($validator->fails()) {
            Log::channel('e_health_errors')->error(
                'EHealth Employee validation failed: ' . implode(', ', $validator->errors()->all())
            );
        }

        $validated = $validator->validated();

        if (!$this->groupByEntities) {
            return $validated;
        }

        $party = Arr::pull($validated, 'party', []);
        $documents = Arr::pull($party, 'documents', []);
        $phones = Arr::pull($party, 'phones', []);

        $doctorData = Arr::pull($validated, $employeeTypeKey, []);

        return [
            'employee' => $validated,
            'party' => $party,
            'documents' => $documents,
            'phones' => $phones,
            'educations' => $doctorData['educations'] ?? [],
            'specialities' => $doctorData['specialities'] ?? [],
            'qualifications' => $doctorData['qualifications'] ?? [],
            'scienceDegree' => $doctorData['science_degree'] ?? [],
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

    /**
     * Prepares data for the Party model from an API response.
     */
    public static function mapPartyData(array $apiPartyData): array
    {
        $partyApiKeys = ['id', 'first_name', 'last_name', 'second_name', 'birth_date', 'gender', 'no_tax_id', 'tax_id', 'email'];
        $partyData = array_intersect_key($apiPartyData, array_flip($partyApiKeys));

        if (isset($partyData['id'])) {
            $partyData['uuid'] = $partyData['id'];
            unset($partyData['id']);
        }

        return $partyData;
    }

    /**
     * Prepares a complete data structure for creating a new Employee
     * by combining data from the signed Revision and the approved API response.
     *
     * @param EmployeeRequest $employeeRequest The local request containing the revision.
     * @param array           $approvedData    The final, confirmed data from the E-Health API.
     *
     * @return array A structured array with data for employee, party, doctor, etc.
     */
    public static function mapCreate(EmployeeRequest $employeeRequest, array $approvedData): array
    {
        // THE SOURCE OF TRUTH: Data from the revision, which the user signed.
        $revisionData = $employeeRequest->revision->data;

        // 1. Prepare Employee data
        $employeeData = [
            // Data from the Revision (what user signed)
            'position' => $revisionData['employee_request_data']['position'],
            'employee_type' => $revisionData['employee_request_data']['employee_type'],
            'start_date' => $revisionData['employee_request_data']['start_date'],
            'end_date' => $revisionData['employee_request_data']['end_date'],
            'division_id' => $revisionData['employee_request_data']['division_id'],

            // Data from existing relationships
            'legal_entity_id' => $employeeRequest->legal_entity_id,
            'legal_entity_uuid' => $employeeRequest->legal_entity_uuid,
            'inserted_at' => now(),
            'party_id' => $employeeRequest->party_id,
            'user_id' => $employeeRequest->party->user_id,

            // Final, confirmed data from the live E-Health response
            'uuid' => $approvedData['uuid'],
            'status' => $approvedData['status'],
            'is_active' => $approvedData['is_active'] ?? true,
        ];

        // 2. Prepare related data using existing mappers
        $partyData = self::mapPartyData($revisionData['party'] ?? []);
        $doctorData = $revisionData['doctor'] ?? []; // This already contains nested structure

        // 3. Return a structured payload
        return [
            'employee' => $employeeData,
            'party' => $partyData,
            'doctor' => $doctorData,
            // We can also return documents and phones directly from revision
            'documents' => $revisionData['documents'] ?? [],
            'phones' => $revisionData['phones'] ?? [],
        ];
    }

    /**
     * Prepares the nested data structure for a Revision from flat form data.
     */
    public static function mapRevisionData(array $flatData): array
    {
        $employeeChunk = Arr::only($flatData, ['position', 'employee_type', 'start_date', 'end_date', 'division_id']);
        $partyChunk = Arr::only($flatData, ['last_name', 'first_name', 'second_name', 'gender', 'birth_date', 'tax_id', 'no_tax_id', 'email', 'working_experience', 'about_myself']);
        $documentsChunk = $flatData['documents'] ?? [];
        $phonesChunk = $flatData['phones'] ?? [];
        $doctorChunk = $flatData['doctor'] ?? [];

        return [
            'employee_request_data' => $employeeChunk,
            'party' => $partyChunk,
            'documents' => $documentsChunk,
            'phones' => $phonesChunk,
            'doctor' => $doctorChunk,
        ];
    }
}
