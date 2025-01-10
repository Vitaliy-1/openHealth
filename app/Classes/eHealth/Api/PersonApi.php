<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;

class PersonApi
{
    protected const string URL_PERSON = '/api/persons';
    protected const string URL_V1 = '/api/person_requests';
    protected const string URL_V2 = '/api/v2/person_requests';

    /**
     * Create Person Request v2 (as part of Person creation w/o declaration process).
     *
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function createPersonRequest(array $params = []): array
    {
        return (new Request('POST', self::URL_V2, $params))->sendRequest();
    }

    /**
     * Create new Confidant Person relationship request.
     *
     * @param  string  $personId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function createConfidantRelationship(string $personId, array $params = []): array
    {
        return (new Request('POST', '/api/persons' . "/$personId/confidant_person_relationship_requests",
            $params))->sendRequest();
    }

    /**
     * Approve previously created Person Request.
     *
     * @param  string  $personId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function approvePersonRequest(string $personId, array $params = []): array
    {
        return (new Request('PATCH', self::URL_V2 . "/$personId/actions/approve", $params))->sendRequest();
    }

    /**
     * Upload file to storage by provided URL.
     *
     * @param  string  $url
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function uploadFileRequest(string $url, array $params = []): array
    {
        return (new Request('PUT', $url, $params))->sendRequest();
    }

    /**
     * Sign previously created Person Request.
     *
     * @param  string  $personId
     * @param  array  $params
     * @param  string  $mspDrfo
     * @return array
     * @throws ApiException
     */
    public static function singPersonRequest(string $personId, array $params = [], string $mspDrfo = ''): array
    {
        return (new Request('PATCH', self::URL_V2 . "/$personId/actions/sign", $params, mspDrfo: $mspDrfo))
            ->sendRequest();
    }

    /**
     * Obtains patient details by setting parameters like status, page, and page size.
     *
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getCreatedPersonsList(array $params = []): array
    {
        return (new Request('GET', self::URL_V1, $params))->sendRequest();
    }

    /**
     * Obtains patient details by ID.
     *
     * @param  string  $personId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getCreatedPersonById(string $personId, array $params = []): array
    {
        return (new Request('GET', self::URL_V2 . "/$personId", $params))->sendRequest();
    }

    /**
     * Re-send SMS to a person who approve creating or updating data about himself.
     *
     * @param  string  $personId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function resendAuthorizationSms(string $personId, array $params = []): array
    {
        return (new Request('POST', self::URL_V1 . "/$personId/actions/resend_otp", $params))->sendRequest();
    }

    /**
     * Search for a person by parameters.
     *
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function searchForPersonByParams(array $params = []): array
    {
        return (new Request('GET', self::URL_PERSON, $params))->sendRequest();
    }

    /**
     * Schema Crate/Update Person Request v2.
     *
     * @return array
     */
    public static function schemaRequest(): array
    {
        return [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [
                'person' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'string'
                        ],
                        'first_name' => [
                            'type' => 'string'
                        ],
                        'last_name' => [
                            'type' => 'string'
                        ],
                        'second_name' => [
                            'type' => 'string'
                        ],
                        'birth_date' => [
                            'type' => 'string'
                        ],
                        'birth_country' => [
                            'type' => 'string'
                        ],
                        'birth_settlement' => [
                            'type' => 'string'
                        ],
                        'gender' => [
                            'enum' => [
                                'MALE',
                                'FEMALE'
                            ]
                        ],
                        'email' => [
                            'type' => 'string'
                        ],
                        'no_tax_id' => [
                            'type' => 'boolean'
                        ],
                        'tax_id' => [
                            'type' => 'string'
                        ],
                        'secret' => [
                            'type' => 'string'
                        ],
                        'documents' => [
                            'type' => 'array'
                        ],
                        'addresses' => [
                            'type' => 'array'
                        ],
                        'phones' => [
                            'type' => 'array'
                        ],
                        'authentication_methods' => [
                            'type' => 'array'
                        ],
                        'unzr' => [
                            'type' => 'string'
                        ],
                        'emergency_contact' => [
                            'type' => 'object',
                            'properties' => [
                                'first_name' => [
                                    'type' => 'string'
                                ],
                                'last_name' => [
                                    'type' => 'string'
                                ],
                                'second_name' => [
                                    'type' => 'string'
                                ],
                                'phones' => [
                                    'type' => 'array'
                                ]
                            ],
                            'required' => [
                                'first_name',
                                'last_name',
                                'phones'
                            ]
                        ],
                        'confidant_person' => [
                            'type' => 'object',
                            'properties' => [
                                'person_id' => [
                                    'type' => 'string'
                                ],
                                'documents_relationship' => [
                                    'type' => 'array'
                                ]
                            ],
                            'required' => [
                                'person_id',
                                'documents_relationship'
                            ]
                        ]
                    ],
                    'required' => [
                        'first_name',
                        'last_name',
                        'birth_date',
                        'birth_country',
                        'birth_settlement',
                        'gender',
                        'no_tax_id',
                        'secret',
                        'documents',
                        'addresses',
                        'emergency_contact'
                    ]
                ],
                'patient_signed' => [
                    'type' => 'boolean'
                ],
                'process_disclosure_data_consent' => [
                    'type' => 'boolean'
                ],
                'authorize_with' => [
                    'type' => 'string'
                ]
            ],
            'required' => [
                'person',
                'patient_signed',
                'process_disclosure_data_consent'
            ]
        ];
    }

    /**
     * Approve Person Request v2.
     *
     * @return array
     */
    public function approveSchemaRequest(): array
    {
        return [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [
                'verification_code' => [
                    'type' => 'number',
                ]
            ]
        ];
    }

    /**
     * Encrypt data for signing person request v2.
     *
     * @return array
     */
    public function encryptSignSchemaRequest(): array
    {
        return [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [
                'status' => [
                    'type' => 'string'
                ],
                'id' => [
                    'type' => 'string'
                ],
                'person' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'string'
                        ],
                        'first_name' => [
                            'type' => 'string'
                        ],
                        'last_name' => [
                            'type' => 'string'
                        ],
                        'second_name' => [
                            'type' => 'string'
                        ],
                        'birth_date' => [
                            'type' => 'string'
                        ],
                        'birth_country' => [
                            'type' => 'string'
                        ],
                        'birth_settlement' => [
                            'type' => 'string'
                        ],
                        'gender' => [
                            'enum' => [
                                'MALE',
                                'FEMALE'
                            ]
                        ],
                        'email' => [
                            'type' => 'string'
                        ],
                        'no_tax_id' => [
                            'type' => 'boolean'
                        ],
                        'tax_id' => [
                            'type' => 'string'
                        ],
                        'secret' => [
                            'type' => 'string'
                        ],
                        'documents' => [
                            'type' => 'array'
                        ],
                        'addresses' => [
                            'type' => 'array'
                        ],
                        'phones' => [
                            'type' => 'array'
                        ],
                        'authentication_methods' => [
                            'type' => 'array'
                        ],
                        'unzr' => [
                            'type' => 'string'
                        ],
                        'emergency_contact' => [
                            'type' => 'object',
                            'properties' => [
                                'first_name' => [
                                    'type' => 'string'
                                ],
                                'last_name' => [
                                    'type' => 'string'
                                ],
                                'second_name' => [
                                    'type' => 'string'
                                ],
                                'phones' => [
                                    'type' => 'array'
                                ]
                            ],
                            'required' => [
                                'first_name',
                                'last_name',
                                'phones'
                            ]
                        ],
                        'confidant_person' => [
                            'type' => 'array'
                        ]
                    ],
                    'required' => [
                        'first_name',
                        'last_name',
                        'birth_date',
                        'birth_country',
                        'birth_settlement',
                        'gender',
                        'no_tax_id',
                        'secret',
                        'documents',
                        'addresses',
                        'emergency_contact'
                    ]
                ],
                'patient_signed' => [
                    'type' => 'boolean'
                ],
                'process_disclosure_data_consent' => [
                    'type' => 'boolean'
                ],
                'content' => [
                    'type' => 'string'
                ],
                'channel' => [
                    'const' => 'MIS'
                ]
            ],
            'required' => [
                'status',
                'id',
                'person',
                'patient_signed',
                'process_disclosure_data_consent',
                'content',
                'channel'
            ]
        ];
    }

    /**
     * Sign Person Request v2.
     *
     * @return array
     */
    public function signSchemaRequest(): array
    {
        return [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [
                'signed_content' => [
                    'type' => 'string'
                ]
            ],
            'required' => [
                'signed_content'
            ]
        ];
    }
}
