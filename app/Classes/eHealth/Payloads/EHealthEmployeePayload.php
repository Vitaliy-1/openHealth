<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Payloads;

use App\Core\Arr;
use RuntimeException;

class EHealthEmployeePayload
{
    /**
     * Prepares the nested data structure for the eHealth API.
     *
     * @param array $nestedData The nested data from the Revision.
     * @return array The flattened payload for eHealth, correctly wrapped.
     * @throws RuntimeException If essential data is missing.
     */
    public static function prepare(array $nestedData): array
    {
        $payload = [
            'position' => Arr::get($nestedData, 'employee_request_data.position'),
            'start_date' => Arr::get($nestedData, 'employee_request_data.start_date'),
            'end_date' => Arr::get($nestedData, 'employee_request_data.end_date'),
            'employee_type' => Arr::get($nestedData, 'employee_request_data.employee_type'),
            'division_id' => Arr::get($nestedData, 'employee_request_data.division_id'),
            'legal_entity_id' => Arr::get($nestedData, 'employee_request_data.legal_entity_id'),
            'status' => 'NEW', // Added this field to satisfy API validation
            'party' => [
                'first_name' => Arr::get($nestedData, 'party.first_name'),
                'last_name' => Arr::get($nestedData, 'party.last_name'),
                'second_name' => Arr::get($nestedData, 'party.second_name'),
                'birth_date' => Arr::get($nestedData, 'party.birth_date'),
                'gender' => Arr::get($nestedData, 'party.gender'),
                'no_tax_id' => (bool) Arr::get($nestedData, 'party.no_tax_id'),
                'tax_id' => Arr::get($nestedData, 'party.tax_id'),
                'email' => Arr::get($nestedData, 'party.email'),
                'documents' => Arr::get($nestedData, 'documents'),
                'phones' => Arr::get($nestedData, 'phones'),
                'working_experience' => (int) Arr::get($nestedData, 'party.working_experience'),
                'about_myself' => Arr::get($nestedData, 'party.about_myself'),
            ],
        ];

        // Add the 'doctor' object only if the employee type is 'DOCTOR'
        if (Arr::get($nestedData, 'employee_request_data.employee_type') === 'DOCTOR') {
            $doctorData = [
                'educations' => Arr::get($nestedData, 'doctor.educations'),
                'qualifications' => Arr::get($nestedData, 'doctor.qualifications'),
                'specialities' => Arr::get($nestedData, 'doctor.specialities'),
            ];

            $scienceDegrees = Arr::get($nestedData, 'doctor.scienceDegrees', []);
            if (!empty($scienceDegrees)) {
                $doctorData['science_degree'] = Arr::first($scienceDegrees);
            }

            $payload['doctor'] = $doctorData;
        }

        // Clean up empty fields
        $payload = array_filter($payload, fn($value) => !is_null($value) && $value !== '');
        if (isset($payload['party'])) {
            $payload['party'] = array_filter($payload['party'], fn($value) => !is_null($value) && $value !== '');
        }
        if (isset($payload['doctor'])) {
            $payload['doctor'] = array_filter($payload['doctor'], fn($value) => !is_null($value) && $value !== '');
        }

        return ['employee_request' => $payload];
    }
}
