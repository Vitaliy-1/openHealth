<?php

namespace App\Livewire\Patient\Forms\Api;

use App\Classes\eHealth\Api\PersonApi;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
        $documents = $cacheData['documents'];
        $addresses = $cacheData['addresses'];
        $phones = $patient['phones'] ?? null;
        $authenticationMethods = $patient['authenticationMethods'];
        $emergencyContact = $patient['emergencyContact'];
        $documentsRelationship = $cacheData['documentsRelationship'] ?? null;

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
                    'zip' => $addresses['zip'] ?? ''
                ]
            ],

            'authentication_methods' => [
                [
                    'type' => $authenticationMethods['type'],
                    // required for type = OTP
                    'phone_number' => $authenticationMethods['phoneNumber'] ?? '',
                    // required for type = THIRD_PERSON
                    'value' => $documentsRelationship['confidantPersonId'] ?? '',
                    // required it type = THIRD_PERSON, and optional for type = OTP or OFFLINE
                    'alias' => $authenticationMethods['alias'] ?? ''
                ]
            ],

            'unzr' => $patient['unzr'] ?? '',

            'emergency_contact' => (object)
            [
                'first_name' => $emergencyContact['firstName'],
                'last_name' => $emergencyContact['lastName'],
                'second_name' => $emergencyContact['secondName'] ?? '',

                'phones' => [
                    [
                        'type' => $emergencyContact['phones']['type'],
                        'number' => $emergencyContact['phones']['number']
                    ]
                ]
            ]
        ];

        foreach ($documents as $document) {
            $patientData['documents'][] = [
                'type' => $document['type'],
                'number' => $document['number'],
                'issued_by' => $document['issuedBy'],
                'issued_at' => $document['issuedAt'],
                'expiration_date' => $document['expirationDate'] ?? ''
            ];
        }

        if (isset($phones)) {
            $patientData['phones'] = [
                [
                    'type' => $phones['type'] ?? '',
                    'number' => $phones['number'] ?? ''
                ]
            ];
        }

        if (!$noTaxId) {
            $patientData['tax_id'] = $patient['taxId'];
        }

        if ($isIncapable) {
            $patientData['confidant_person'] = (object)
            [
                'person_id' => $documentsRelationship['confidantPersonId'],
                'documents_relationship' => []
            ];

            foreach ($documentsRelationship as $key => $documentRelationship) {
                if ($key === 'confidantPersonId') {
                    continue;
                }

                $patientData['confidant_person']->documents_relationship[] = [
                    'type' => $documentRelationship['type'],
                    'number' => $documentRelationship['number'],
                    'issued_by' => $documentRelationship['issuedBy'],
                    'issued_at' => $documentRelationship['issuedAt'],
                    'active_to' => $documentRelationship['activeTo'] ?? ''
                ];
            }
        }

        self::removeEmptyKeys($patientData);

        return [
            'person' => (object) $patientData,
            'patient_signed' => false,
            'process_disclosure_data_consent' => true
        ];
    }

    /**
     * Build an array of parameters for uploading files to storage.
     *
     * @param  TemporaryUploadedFile  $uploadedFile
     * @return array[]
     */
    public static function buildUploadFileRequest(TemporaryUploadedFile $uploadedFile): array
    {
        return [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($uploadedFile->getRealPath(), 'rb'),
                    'filename' => $uploadedFile->getClientOriginalName()
                ],
            ],
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
     * @param $encryptedData
     * @return array
     */
    public static function buildSignPersonRequest($encryptedData): array
    {
        return ['signed_content' => $encryptedData];
    }

    /**
     * Build an array of parameters for encrypting a patient request.
     *
     * @param  array  $patientData
     * @return array
     */
    public static function buildEncryptedSignPersonRequest(array $patientData): array
    {
        return [
            'status' => $patientData['data']['status'],
            'id' => $patientData['data']['id'],
            'person' => (object) $patientData['data']['person'],
            'patient_signed' => true,
            'process_disclosure_data_consent' => $patientData['data']['process_disclosure_data_consent'],
            'content' => $patientData['data']['content'],
            'channel' => $patientData['data']['channel']
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
            'page_size' => $pageSize
        ];
    }

    /**
     * Build an array of parameters for a patient request list.
     *
     * @param  array  $filters
     * @return array
     */
    public static function buildSearchForPerson(array $filters): array
    {
        foreach ($filters as $key => $filter) {
            $result[Str::snake($key)] = $filter;
        }

        self::removeEmptyKeys($result);

        return $result;
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
