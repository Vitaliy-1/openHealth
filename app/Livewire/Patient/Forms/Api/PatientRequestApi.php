<?php

namespace App\Livewire\Patient\Forms\Api;

use App\Classes\eHealth\Api\PersonApi;

class PatientRequestApi extends PersonApi
{
    /**
     * Build a create patient request array based on the provided cache data and flags.
     *
     * @param array $cacheData The cache data containing patient information.
     * @param bool $noTaxId Flag indicating whether the patient has no tax ID.
     * @param bool $isIncapable Flag indicating whether the patient is incapable.
     */
    public static function buildCreatePersonRequest(array $cacheData, bool $noTaxId, bool $isIncapable): array
    {
        $patient = $cacheData['patient'];
        $documents = $cacheData['documents'][0];
        $addresses = $cacheData['addresses'];
        $phones = $patient['phones'];
        $authentication_methods = $patient['authentication_methods'];
        $emergency_contact = $patient['emergency_contact'];
        $documents_relationship = $cacheData['documents_relationship'][0] ?? null;

        $patientData = [
//            'id' => '13001c60-45a0-4b5a-b425-9505e1de18bd',
            'first_name' => $patient['first_name'],
            'last_name' => $patient['last_name'],
            'second_name' => $patient['second_name'] ?? '',
            'birth_date' => $patient['birth_date'],
            'birth_country' => $patient['birth_country'],
            'birth_settlement' => $patient['birth_settlement'],
            'gender' => $patient['gender'],
            'email' => $patient['email'] ?? '',
            'no_tax_id' => $noTaxId,
            'secret' => $patient['secret'],

            'documents' => [
                [
                    'type' => $documents['type'],
                    'number' => $documents['number'],
                    'issued_by' => $documents['issued_by'],
                    'issued_at' => $documents['issued_at'],
                    'expiration_date' => $documents['expiration_date'] ?? '',
                ]
            ],

            'addresses' => [
                [
                    'type' => $addresses['type'],
                    'country' => $addresses['country'],
                    'area' => $addresses['area'],
                    'region' => $addresses['region'] ?? '',
                    'settlement' => $addresses['settlement'],
                    'settlement_type' => $addresses['settlement_type'],
                    'settlement_id' => $addresses['settlement_id'],
                    'street_type' => $addresses['street_type'] ?? '',
                    'street' => $addresses['street'] ?? '',
                    'building' => $addresses['building'] ?? '',
                    'apartment' => $addresses['apartment'] ?? '',
                    'zip' => $addresses['zip'] ?? '',
                ]
            ],

            'phones' => [
                [
                    'type' => $phones['type'],
                    'number' => $phones['number'],
                ]
            ],

            'authentication_methods' => [
                [
                    'type' => $authentication_methods['type'],
                    // required for type = OTP
                    'phone_number' => $authentication_methods['phone_number'] ?? '',
                    // required for type = THIRD_PERSON
                    'value' => $authentication_methods['value'] ?? '',
                    // required it type = THIRD_PERSON, and optional for type = OTP or OFFLINE
                    'alias' => $authentication_methods['alias'] ?? '',
                ]
            ],

            'unzr' => $documents['unzr'] ?? '',

            'emergency_contact' => (object)
            [
                'first_name' => $emergency_contact['first_name'],
                'last_name' => $emergency_contact['last_name'],
                'second_name' => $emergency_contact['second_name'] ?? '',

                'phones' => [
                    [
                        'type' => $emergency_contact['phones']['type'],
                        'number' => $emergency_contact['phones']['number'],
                    ]
                ],
            ],
        ];

        if (!$noTaxId) {
            $patientData['tax_id'] = $patient['tax_id'];
        }

        if ($isIncapable) {
            $patientData['confidant_person'] = (object)
            [
                'person_id' => '',

                'documents_relationship' => [
                    [
                        'type' => $documents_relationship['type'],
                        'number' => $documents_relationship['number'],
                        'issued_by' => $documents_relationship['issued_by'] ?? '',
                        'issued_at' => $documents_relationship['issued_at'] ?? '',
                        // ?? schema does not allow additional properties, але в general MIS API є....
//                        'active_to' => $documents_relationship['expiration_date'] ?? '',
                    ]
                ],
            ];
        }

        self::removeEmptyKeys($patientData);

        return [
            'person' => (object)$patientData,
            'patient_signed' => false,
            'process_disclosure_data_consent' => true,
        ];
    }

    /**
     * Build an array of parameters for approving a patient request.
     *
     * @param int $verificationCode The verification code used to approve the patient request.
     * @return int[]
     */
    public static function buildApprovePersonRequest(int $verificationCode): array
    {
        return ['verification_code' => $verificationCode];
    }

    /**
     * Build an array of parameters for a patient request list.
     *
     * @param string $status The status of the patient requests to fetch (NEW, APPROVED, SIGNED, REJECTED, CANCELLED).
     * @param int $page The page number of the results to fetch.
     * @param int $pageSize A limit on the number of objects to be returned, between 1 and 300. Default: 50.
     * @return array
     */
    public static function buildGetPersonRequestList(string $status, int $page, int $pageSize = 50): array
    {
        return [
            'status' => $status,
            'page' => $page,
            'page_size' => $pageSize,
        ];
    }

    /**
     * Remove keys from an array if their values are empty strings.
     *
     * @param array $data
     * @return void
     */
    protected static function removeEmptyKeys(array &$data): void
    {
        foreach ($data as $key => &$value) {
            if (is_object($value)) {
                // Convert object to array
                $value = (array)$value;
                self::removeEmptyKeys($value);
                // Convert array back to object
                $value = (object)$value;
            } elseif (is_array($value)) {
                self::removeEmptyKeys($value);
            } elseif ($value === '') {
                unset($data[$key]);
            }
        }
    }
}
