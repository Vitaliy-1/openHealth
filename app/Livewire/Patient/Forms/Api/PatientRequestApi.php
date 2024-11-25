<?php

namespace App\Livewire\Patient\Forms\Api;

use App\Classes\eHealth\Api\PersonApi;

class PatientRequestApi extends PersonApi
{
    /**
     * Build a create patient request array based on the provided cache data and flags.
     *
     * @param  array  $cacheData  The cache data containing patient information.
     * @param  bool  $noTaxId  Flag indicating whether the patient has no tax ID.
     * @param  bool  $isIncapable  Flag indicating whether the patient is incapable.
     * @return array
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
            'first_name' => $patient['firstName'],
            'last_name' => $patient['lastName'],
            'second_name' => $patient['secondName'] ?? '',
            'birth_date' => $patient['birthDate'],
            'birth_country' => $patient['birthCountry'],
            'birth_settlement' => $patient['birthSettlement'],
            'gender' => $patient['gender'],
            'email' => $patient['email'] ?? '',
            'no_tax_id' => $noTaxId,
            'secret' => $patient['secret'],

            'documents' => [
                [
                    'type' => $documents['type'],
                    'number' => $documents['number'],
                    'issued_by' => $documents['issuedBy'],
                    'issued_at' => $documents['issuedAt'],
                    'expiration_date' => $documents['expirationDate'] ?? '',
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
                    'phone_number' => $authentication_methods['phoneNumber'] ?? '',
                    // required for type = THIRD_PERSON
                    'value' => $authentication_methods['value'] ?? '',
                    // required it type = THIRD_PERSON, and optional for type = OTP or OFFLINE
                    'alias' => $authentication_methods['alias'] ?? '',
                ]
            ],

            'unzr' => $patient['unzr'] ?? '',

            'emergency_contact' => (object)
            [
                'first_name' => $emergency_contact['firstName'],
                'last_name' => $emergency_contact['lastName'],
                'second_name' => $emergency_contact['secondName'] ?? '',

                'phones' => [
                    [
                        'type' => $emergency_contact['phones']['type'],
                        'number' => $emergency_contact['phones']['number'],
                    ]
                ],
            ],
        ];

        if (!$noTaxId) {
            $patientData['tax_id'] = $patient['taxId'];
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
            'person' => (object) $patientData,
            'patient_signed' => false,
            'process_disclosure_data_consent' => true,
        ];
    }

    /**
     * Build an array of parameters for approving a patient request.
     *
     * @param  int  $verificationCode  The verification code used to approve the patient request.
     * @return int[]
     */
    public static function buildApprovePersonRequest(int $verificationCode): array
    {
        return ['verification_code' => $verificationCode];
    }

    /**
     * Build an array of parameters for signing a patient request.
     *
     * @param $cacheData
     * @return array
     */
    public static function buildSignPersonRequest($cacheData): array
    {
        return ['signed_content' => $cacheData];
    }

    /**
     * Build an array of parameters for encrypting a patient request.
     *
     * @param  array  $cacheData
     * @return array
     */
    public static function buildEncryptedSignPersonRequest(array $cacheData): array
    {
        return [
            'status' => $cacheData['data']['status'],
            'id' => $cacheData['data']['id'],
            'person' => (object) $cacheData['data']['person'],
            'patient_signed' => true,
            'process_disclosure_data_consent' => $cacheData['data']['process_disclosure_data_consent'],
            'content' => $cacheData['data']['content'],
            'channel' => $cacheData['data']['channel'],
        ];
    }

    /**
     * Build an array of parameters for a patient request list.
     *
     * @param  string  $status  The status of the patient requests to fetch (NEW, APPROVED, SIGNED, REJECTED, CANCELLED).
     * @param  int  $page  The page number of the results to fetch.
     * @param  int  $pageSize  A limit on the number of objects to be returned, between 1 and 300. Default: 50.
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
     * @param  array  $data
     * @return void
     */
    protected static function removeEmptyKeys(array &$data): void
    {
        foreach ($data as $key => &$value) {
            if (is_object($value)) {
                // Convert object to array
                $value = (array) $value;
                self::removeEmptyKeys($value);
                // Convert array back to object
                $value = (object) $value;
            } elseif (is_array($value)) {
                self::removeEmptyKeys($value);
            } elseif ($value === '') {
                unset($data[$key]);
            }
        }
    }
}
