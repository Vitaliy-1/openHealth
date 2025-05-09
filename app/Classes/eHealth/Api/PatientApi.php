<?php

declare(strict_types=1);

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class PatientApi
{
    protected const string ENDPOINT_PATIENT = '/api/patients';

    /**
     * Get the processing status of the async job.
     *
     * @param  string  $jobId
     * @return array
     * @throws ApiException
     */
    public static function getJobsDetailsById(string $jobId): array
    {
        return new Request(HttpRequest::METHOD_GET, "/api/jobs/$jobId", [])->sendRequest();
    }

    /**
     * Create episode.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function createEpisode(string $patientId, array $params): array
    {
        return new Request(
            HttpRequest::METHOD_POST,
            self::ENDPOINT_PATIENT . "/$patientId/episodes",
            $params
        )->sendRequest();
    }

    /**
     * Submit encounter data package.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function submitEncounter(string $patientId, array $params): array
    {
        return new Request(
            HttpRequest::METHOD_POST,
            self::ENDPOINT_PATIENT . "/$patientId/encounter_package",
            $params
        )->sendRequest();
    }

    /**
     * Get short encounter data by provided parameters.
     *
     * @param  string  $patientId
     * @return array
     * @throws ApiException
     */
    public static function getShortEncounterBySearchParams(string $patientId): array
    {
        return new Request(
            HttpRequest::METHOD_GET,
            self::ENDPOINT_PATIENT . "/$patientId/summary/encounters",
            []
        )->sendRequest();
    }

    /**
     * Get brief information about episodes, in order not to disclose confidential and sensitive data.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getShortEpisodes(string $patientId, array $params): array
    {
        return new Request(
            HttpRequest::METHOD_GET,
            self::ENDPOINT_PATIENT . "/$patientId/summary/episodes",
            $params
        )->sendRequest();
    }

    /**
     * Get patient episode by relevant IDs.
     *
     * @param  string  $patientId
     * @param  string  $episodeId
     * @return array
     * @throws ApiException
     */
    public static function getEpisodeById(string $patientId, string $episodeId): array
    {
        return new Request(
            HttpRequest::METHOD_GET,
            self::ENDPOINT_PATIENT . "/$patientId/episodes/$episodeId",
            []
        )->sendRequest();
    }

    /**
     * Get episodes, that patient gave approvals to read by a current employee.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getApprovedEpisodes(string $patientId, array $params): array
    {
        return new Request(
            HttpRequest::METHOD_GET,
            self::ENDPOINT_PATIENT . "/$patientId/episodes/approved",
            $params
        )->sendRequest();
    }

    /**
     * Use period_from period_to to find episodes that were active in a certain period of time.
     * Scopes required: episode:read.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getEpisodeBySearchParams(string $patientId, array $params): array
    {
        return new Request(
            HttpRequest::METHOD_GET,
            self::ENDPOINT_PATIENT . "/$patientId/episodes",
            $params
        )->sendRequest();
    }

    /**
     * Get the current diagnoses related only to active episodes.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getActiveDiagnoses(string $patientId, array $params): array
    {
        return new Request(
            HttpRequest::METHOD_GET,
            self::ENDPOINT_PATIENT . "/$patientId/summary/diagnoses",
            $params
        )->sendRequest();
    }

    /**
     * Get conditions by search parameters.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getConditions(string $patientId, array $params): array
    {
        return new Request(
            HttpRequest::METHOD_GET,
            self::ENDPOINT_PATIENT . "/$patientId/conditions",
            $params
        )->sendRequest();
    }

    /**
     * Get the current diagnoses related only to active episodes.
     *
     * @param  string  $patientId
     * @param  array  $params
     * @return array
     * @throws ApiException
     */
    public static function getObservations(string $patientId, array $params): array
    {
        return new Request(
            HttpRequest::METHOD_GET,
            self::ENDPOINT_PATIENT . "/$patientId/summary/observations",
            $params
        )->sendRequest();
    }

    public function schemaRequest(): array
    {
        return [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [
                'encounter' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'string'
                        ],
                        'status' => [
                            'type' => 'string'
                        ],
                        'period' => [
                            'type' => 'object',
                            'properties' => [
                                'start' => [
                                    'type' => 'string'
                                ],
                                'end' => [
                                    'type' => 'string'
                                ]
                            ],
                            'required' => [
                                'start',
                                'end'
                            ]
                        ],
                        'incoming_referral' => [
                            'type' => 'object',
                            'properties' => [
                                'identifier' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'coding' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        'anyOf' => [
                                                            [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'system' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'code' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'system'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'text' => [
                                                    'type' => 'string'
                                                ]
                                            ]
                                        ],
                                        'value' => [
                                            'type' => 'string'
                                        ]
                                    ],
                                    'required' => [
                                        'value'
                                    ]
                                ]
                            ],
                            'required' => [
                                'identifier'
                            ],
                            'additionalProperties' => false
                        ],
                        'paper_referral' => [
                            'type' => 'object',
                            'properties' => [
                                'requisition' => [
                                    'type' => 'string'
                                ],
                                'requester_legal_entity_name' => [
                                    'type' => 'string'
                                ],
                                'requester_legal_entity_edrpou' => [
                                    'type' => 'string'
                                ],
                                'requester_employee_name' => [
                                    'type' => 'string'
                                ],
                                'service_request_date' => [
                                    'type' => 'string'
                                ],
                                'note' => [
                                    'type' => 'string'
                                ]
                            ],
                            'required' => [
                                'requester_legal_entity_edrpou',
                                'requester_employee_name',
                                'service_request_date'
                            ],
                            'additionalProperties' => false
                        ],
                        'visit' => [
                            'type' => 'object',
                            'properties' => [
                                'identifier' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'coding' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        'anyOf' => [
                                                            [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'system' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'code' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'system'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'text' => [
                                                    'type' => 'string'
                                                ]
                                            ]
                                        ],
                                        'value' => [
                                            'type' => 'string'
                                        ]
                                    ],
                                    'required' => [
                                        'value'
                                    ]
                                ]
                            ],
                            'required' => [
                                'identifier'
                            ],
                            'additionalProperties' => false
                        ],
                        'episode' => [
                            'type' => 'object',
                            'properties' => [
                                'identifier' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'coding' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        'anyOf' => [
                                                            [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'system' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'code' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'system'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'text' => [
                                                    'type' => 'string'
                                                ]
                                            ]
                                        ],
                                        'value' => [
                                            'type' => 'string'
                                        ]
                                    ],
                                    'required' => [
                                        'value'
                                    ]
                                ]
                            ],
                            'required' => [
                                'identifier'
                            ],
                            'additionalProperties' => false
                        ],
                        'class' => [
                            'type' => 'object',
                            'properties' => [
                                'system' => [
                                    'type' => 'string'
                                ],
                                'code' => [
                                    'type' => 'string'
                                ]
                            ]
                        ],
                        'type' => [
                            'type' => 'object',
                            'properties' => [
                                'coding' => [
                                    'type' => 'array',
                                    'items' => [
                                        'anyOf' => [
                                            [
                                                'type' => 'object',
                                                'properties' => [
                                                    'system' => [
                                                        'type' => 'string'
                                                    ],
                                                    'code' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'text' => [
                                    'type' => 'string'
                                ]
                            ]
                        ],
                        'priority' => [
                            'type' => 'object',
                            'properties' => [
                                'coding' => [
                                    'type' => 'array',
                                    'items' => [
                                        'anyOf' => [
                                            [
                                                'type' => 'object',
                                                'properties' => [
                                                    'system' => [
                                                        'type' => 'string'
                                                    ],
                                                    'code' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'text' => [
                                    'type' => 'string'
                                ]
                            ]
                        ],
                        'performer' => [
                            'type' => 'object',
                            'properties' => [
                                'identifier' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'coding' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        'anyOf' => [
                                                            [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'system' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'code' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'system'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'text' => [
                                                    'type' => 'string'
                                                ]
                                            ]
                                        ],
                                        'value' => [
                                            'type' => 'string'
                                        ]
                                    ],
                                    'required' => [
                                        'value'
                                    ]
                                ]
                            ]
                        ],
                        'reasons' => [
                            'type' => 'array'
                        ],
                        'diagnoses' => [
                            'type' => 'array',
                            'items' => [
                                'anyOf' => [
                                    [
                                        'type' => 'object',
                                        'properties' => [
                                            'condition' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'identifier' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'type' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'coding' => [
                                                                        'type' => 'array',
                                                                        'items' => [
                                                                            'anyOf' => [
                                                                                [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'system'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'text' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ]
                                                            ],
                                                            'value' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'value'
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'role' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'coding' => [
                                                        'type' => 'array',
                                                        'items' => [
                                                            'anyOf' => [
                                                                [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'system' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'code' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    'text' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ],
                                            'rank' => [
                                                'type' => 'number'
                                            ]
                                        ],
                                        'required' => [
                                            'condition',
                                            'role'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'actions' => [
                            'type' => 'array',
                            'items' => [
                                'anyOf' => [
                                    [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'action_references' => [
                            'type' => 'array'
                        ],
                        'division' => [
                            'type' => 'object',
                            'properties' => [
                                'identifier' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'type' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'coding' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        'anyOf' => [
                                                            [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'system' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'code' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'system'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'text' => [
                                                    'type' => 'string'
                                                ]
                                            ]
                                        ],
                                        'value' => [
                                            'type' => 'string'
                                        ]
                                    ],
                                    'required' => [
                                        'value'
                                    ]
                                ]
                            ],
                            'required' => [
                                'identifier'
                            ],
                            'additionalProperties' => false
                        ],
                        'prescriptions' => [
                            'type' => 'string'
                        ],
                        'supporting_info' => [
                            'type' => 'array',
                            'items' => [
                                'anyOf' => [
                                    [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'hospitalization' => [
                            'type' => 'object',
                            'properties' => [
                                'admit_source' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'coding' => [
                                            'type' => 'array',
                                            'items' => [
                                                'anyOf' => [
                                                    [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'system' => [
                                                                'type' => 'string'
                                                            ],
                                                            'code' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                're_admission' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'coding' => [
                                            'type' => 'array',
                                            'items' => [
                                                'anyOf' => [
                                                    [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'system' => [
                                                                'type' => 'string'
                                                            ],
                                                            'code' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'destination' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'identifier' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'type' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'coding' => [
                                                            'type' => 'array',
                                                            'items' => [
                                                                'anyOf' => [
                                                                    [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'system' => [
                                                                                'type' => 'string'
                                                                            ],
                                                                            'code' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ],
                                                                        'required' => [
                                                                            'system'
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ],
                                                        'text' => [
                                                            'type' => 'string'
                                                        ]
                                                    ]
                                                ],
                                                'value' => [
                                                    'type' => 'string'
                                                ]
                                            ],
                                            'required' => [
                                                'value'
                                            ]
                                        ]
                                    ]
                                ],
                                'discharge_disposition' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'coding' => [
                                            'type' => 'array',
                                            'items' => [
                                                'anyOf' => [
                                                    [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'system' => [
                                                                'type' => 'string'
                                                            ],
                                                            'code' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'discharge_department' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'coding' => [
                                            'type' => 'array',
                                            'items' => [
                                                'anyOf' => [
                                                    [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'system' => [
                                                                'type' => 'string'
                                                            ],
                                                            'code' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'pre_admission_identifier' => [
                                    'type' => 'string'
                                ]
                            ]
                        ]
                    ],
                    'required' => [
                        'id',
                        'status',
                        'period',
                        'visit',
                        'episode',
                        'class',
                        'type',
                        'performer',
                        'diagnoses'
                    ],
                    'additionalProperties' => false
                ],
                'conditions' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'primary_source' => [
                                        'type' => 'boolean'
                                    ],
                                    'context' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'clinical_status' => [
                                        'type' => 'string'
                                    ],
                                    'verification_status' => [
                                        'type' => 'string'
                                    ],
                                    'severity' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'body_sites' => [
                                        'type' => 'array',
                                        'items' => [
                                            'anyOf' => [
                                                [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'coding' => [
                                                            'type' => 'array',
                                                            'items' => [
                                                                'anyOf' => [
                                                                    [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'system' => [
                                                                                'type' => 'string'
                                                                            ],
                                                                            'code' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ],
                                                        'text' => [
                                                            'type' => 'string'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    'onset_date' => [
                                        'type' => 'string'
                                    ],
                                    'asserted_date' => [
                                        'type' => 'string'
                                    ],
                                    'stage' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'summary' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'coding' => [
                                                        'type' => 'array',
                                                        'items' => [
                                                            'anyOf' => [
                                                                [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'system' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'code' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    'text' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ]
                                        ],
                                        'required' => [
                                            'summary'
                                        ]
                                    ],
                                    'evidences' => [
                                        'type' => 'array',
                                        'items' => [
                                            'anyOf' => [
                                                [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'codes' => [
                                                            'type' => 'array',
                                                            'items' => [
                                                                'anyOf' => [
                                                                    [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'coding' => [
                                                                                'type' => 'array',
                                                                                'items' => [
                                                                                    'anyOf' => [
                                                                                        [
                                                                                            'type' => 'object',
                                                                                            'properties' => [
                                                                                                'system' => [
                                                                                                    'type' => 'string'
                                                                                                ],
                                                                                                'code' => [
                                                                                                    'type' => 'string'
                                                                                                ]
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'text' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ],
                                                        'details' => [
                                                            'type' => 'array',
                                                            'items' => [
                                                                'anyOf' => [
                                                                    [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'identifier' => [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'type' => [
                                                                                        'type' => 'object',
                                                                                        'properties' => [
                                                                                            'coding' => [
                                                                                                'type' => 'array',
                                                                                                'items' => [
                                                                                                    'anyOf' => [
                                                                                                        [
                                                                                                            'type' => 'object',
                                                                                                            'properties' => [
                                                                                                                'system' => [
                                                                                                                    'type' => 'string'
                                                                                                                ],
                                                                                                                'code' => [
                                                                                                                    'type' => 'string'
                                                                                                                ]
                                                                                                            ],
                                                                                                            'required' => [
                                                                                                                'system'
                                                                                                            ]
                                                                                                        ]
                                                                                                    ]
                                                                                                ]
                                                                                            ],
                                                                                            'text' => [
                                                                                                'type' => 'string'
                                                                                            ]
                                                                                        ]
                                                                                    ],
                                                                                    'value' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ],
                                                                                'required' => [
                                                                                    'value'
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'allOf' => [
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'asserter' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'identifier' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'type' => [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'coding' => [
                                                                                'type' => 'array',
                                                                                'items' => [
                                                                                    'anyOf' => [
                                                                                        [
                                                                                            'type' => 'object',
                                                                                            'properties' => [
                                                                                                'system' => [
                                                                                                    'type' => 'string'
                                                                                                ],
                                                                                                'code' => [
                                                                                                    'type' => 'string'
                                                                                                ]
                                                                                            ],
                                                                                            'required' => [
                                                                                                'system'
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'text' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'value' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'report_origin' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'primary_source',
                                    'context',
                                    'code',
                                    'clinical_status',
                                    'verification_status',
                                    'onset_date'
                                ]
                            ]
                        ]
                    ]
                ],
                'observations' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'status' => [
                                        'type' => 'string'
                                    ],
                                    'diagnostic_report' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'categories' => [
                                        'type' => 'array'
                                    ],
                                    'code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'issued' => [
                                        'type' => 'string'
                                    ],
                                    'primary_source' => [
                                        'type' => 'boolean'
                                    ],
                                    'interpretation' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'comment' => [
                                        'type' => 'string'
                                    ],
                                    'body_site' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'method' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'reference_ranges' => [
                                        'type' => 'array',
                                        'items' => [
                                            'anyOf' => [
                                                [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'low' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'value' => [
                                                                    'type' => 'number'
                                                                ],
                                                                'comparator' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'unit' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ],
                                                            'required' => [
                                                                'value',
                                                                'system',
                                                                'code'
                                                            ]
                                                        ],
                                                        'high' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'value' => [
                                                                    'type' => 'number'
                                                                ],
                                                                'comparator' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'unit' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ],
                                                            'required' => [
                                                                'value',
                                                                'comparator',
                                                                'system',
                                                                'code'
                                                            ]
                                                        ],
                                                        'type' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'coding' => [
                                                                    'type' => 'array',
                                                                    'items' => [
                                                                        'anyOf' => [
                                                                            [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                'text' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ],
                                                        'applies_to' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'coding' => [
                                                                    'type' => 'array',
                                                                    'items' => [
                                                                        'anyOf' => [
                                                                            [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                'text' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ],
                                                        'age' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'low' => [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'value' => [
                                                                            'type' => 'number'
                                                                        ],
                                                                        'comparator' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'unit' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'system' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'code' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value'
                                                                    ]
                                                                ],
                                                                'high' => [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'value' => [
                                                                            'type' => 'number'
                                                                        ],
                                                                        'comparator' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'unit' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'system' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'code' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value'
                                                                    ]
                                                                ]
                                                            ]
                                                        ],
                                                        'text' => [
                                                            'type' => 'string'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    'reaction_on' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'components' => [
                                        'type' => 'array',
                                        'items' => [
                                            'anyOf' => [
                                                [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'code' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'coding' => [
                                                                    'type' => 'array',
                                                                    'items' => [
                                                                        'anyOf' => [
                                                                            [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ],
                                                                                'required' => [
                                                                                    'system',
                                                                                    'code'
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                'text' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ],
                                                            'required' => [
                                                                'coding'
                                                            ]
                                                        ],
                                                        'interpretation' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'coding' => [
                                                                    'type' => 'array',
                                                                    'items' => [
                                                                        'anyOf' => [
                                                                            [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                'text' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ],
                                                        'reference_ranges' => [
                                                            'type' => 'array',
                                                            'items' => [
                                                                'anyOf' => [
                                                                    [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'low' => [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'value' => [
                                                                                        'type' => 'number'
                                                                                    ],
                                                                                    'comparator' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'unit' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ],
                                                                                'required' => [
                                                                                    'value',
                                                                                    'system',
                                                                                    'code'
                                                                                ]
                                                                            ],
                                                                            'high' => [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'value' => [
                                                                                        'type' => 'number'
                                                                                    ],
                                                                                    'comparator' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'unit' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ],
                                                                                'required' => [
                                                                                    'value',
                                                                                    'comparator',
                                                                                    'system',
                                                                                    'code'
                                                                                ]
                                                                            ],
                                                                            'type' => [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'coding' => [
                                                                                        'type' => 'array',
                                                                                        'items' => [
                                                                                            'anyOf' => [
                                                                                                [
                                                                                                    'type' => 'object',
                                                                                                    'properties' => [
                                                                                                        'system' => [
                                                                                                            'type' => 'string'
                                                                                                        ],
                                                                                                        'code' => [
                                                                                                            'type' => 'string'
                                                                                                        ]
                                                                                                    ]
                                                                                                ]
                                                                                            ]
                                                                                        ]
                                                                                    ],
                                                                                    'text' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'applies_to' => [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'coding' => [
                                                                                        'type' => 'array',
                                                                                        'items' => [
                                                                                            'anyOf' => [
                                                                                                [
                                                                                                    'type' => 'object',
                                                                                                    'properties' => [
                                                                                                        'system' => [
                                                                                                            'type' => 'string'
                                                                                                        ],
                                                                                                        'code' => [
                                                                                                            'type' => 'string'
                                                                                                        ]
                                                                                                    ]
                                                                                                ]
                                                                                            ]
                                                                                        ]
                                                                                    ],
                                                                                    'text' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'age' => [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'low' => [
                                                                                        'type' => 'object',
                                                                                        'properties' => [
                                                                                            'value' => [
                                                                                                'type' => 'number'
                                                                                            ],
                                                                                            'comparator' => [
                                                                                                'type' => 'string'
                                                                                            ],
                                                                                            'unit' => [
                                                                                                'type' => 'string'
                                                                                            ],
                                                                                            'system' => [
                                                                                                'type' => 'string'
                                                                                            ],
                                                                                            'code' => [
                                                                                                'type' => 'string'
                                                                                            ]
                                                                                        ],
                                                                                        'required' => [
                                                                                            'value'
                                                                                        ]
                                                                                    ],
                                                                                    'high' => [
                                                                                        'type' => 'object',
                                                                                        'properties' => [
                                                                                            'value' => [
                                                                                                'type' => 'number'
                                                                                            ],
                                                                                            'comparator' => [
                                                                                                'type' => 'string'
                                                                                            ],
                                                                                            'unit' => [
                                                                                                'type' => 'string'
                                                                                            ],
                                                                                            'system' => [
                                                                                                'type' => 'string'
                                                                                            ],
                                                                                            'code' => [
                                                                                                'type' => 'string'
                                                                                            ]
                                                                                        ],
                                                                                        'required' => [
                                                                                            'value'
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'text' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    'allOf' => [
                                                        [
                                                            'oneOf' => [
                                                                [
                                                                    'properties' => [
                                                                        'value_string' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value_string'
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'value_quantity' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'value' => [
                                                                                    'type' => 'number'
                                                                                ],
                                                                                'comparator' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'unit' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'value',
                                                                                'system',
                                                                                'code'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value_quantity'
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'value_codeable_concept' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'coding' => [
                                                                                    'type' => 'array',
                                                                                    'items' => [
                                                                                        'anyOf' => [
                                                                                            [
                                                                                                'type' => 'object',
                                                                                                'properties' => [
                                                                                                    'system' => [
                                                                                                        'type' => 'string'
                                                                                                    ],
                                                                                                    'code' => [
                                                                                                        'type' => 'string'
                                                                                                    ]
                                                                                                ],
                                                                                                'required' => [
                                                                                                    'system',
                                                                                                    'code'
                                                                                                ]
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ],
                                                                                'text' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'coding'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value_codeable_concept'
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'value_sampled_data' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'origin' => [
                                                                                    'type' => 'number'
                                                                                ],
                                                                                'period' => [
                                                                                    'type' => 'number'
                                                                                ],
                                                                                'factor' => [
                                                                                    'type' => 'number'
                                                                                ],
                                                                                'lowerLimit' => [
                                                                                    'type' => 'number'
                                                                                ],
                                                                                'upperLimit' => [
                                                                                    'type' => 'number'
                                                                                ],
                                                                                'dimensions' => [
                                                                                    'type' => 'number'
                                                                                ],
                                                                                'data' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'data'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value_sampled_data'
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'value_boolean' => [
                                                                            'type' => 'boolean'
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value_boolean'
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'value_range' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'low' => [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'value' => [
                                                                                            'type' => 'number'
                                                                                        ],
                                                                                        'comparator' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'unit' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'value',
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ],
                                                                                'high' => [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'value' => [
                                                                                            'type' => 'number'
                                                                                        ],
                                                                                        'comparator' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'unit' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'value',
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'low',
                                                                                'high'
                                                                            ],
                                                                            'additionalProperties' => false
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value_range'
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'value_ratio' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'numerator' => [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'value' => [
                                                                                            'type' => 'number'
                                                                                        ],
                                                                                        'comparator' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'unit' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'value',
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ],
                                                                                'denominator' => [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'value' => [
                                                                                            'type' => 'number'
                                                                                        ],
                                                                                        'comparator' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'unit' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'value',
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'numerator',
                                                                                'denominator'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value_ratio'
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'value_time' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value_time'
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'value_date_time' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value_date_time'
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'value_period' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'start' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'end' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'start'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value_period'
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    'required' => [
                                                        'code'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    'specimen' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'device' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ],
                                            'display_value' => [
                                                'type' => 'string'
                                            ]
                                        ],
                                        'required' => [
                                            'display_value'
                                        ]
                                    ],
                                    'context' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'allOf' => [
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'effective_date_time' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'effective_period' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'start' => [
                                                                'type' => 'string'
                                                            ],
                                                            'end' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'start'
                                                        ],
                                                        'additionalProperties' => false
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'performer' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'identifier' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'type' => [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'coding' => [
                                                                                'type' => 'array',
                                                                                'items' => [
                                                                                    'anyOf' => [
                                                                                        [
                                                                                            'type' => 'object',
                                                                                            'properties' => [
                                                                                                'system' => [
                                                                                                    'type' => 'string'
                                                                                                ],
                                                                                                'code' => [
                                                                                                    'type' => 'string'
                                                                                                ]
                                                                                            ],
                                                                                            'required' => [
                                                                                                'system'
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'text' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'value' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'report_origin' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'value_quantity' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'value' => [
                                                                'type' => 'number'
                                                            ],
                                                            'comparator' => [
                                                                'type' => 'string'
                                                            ],
                                                            'unit' => [
                                                                'type' => 'string'
                                                            ],
                                                            'system' => [
                                                                'type' => 'string'
                                                            ],
                                                            'code' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'value',
                                                            'system',
                                                            'code'
                                                        ]
                                                    ]
                                                ],
                                                'required' => [
                                                    'value_quantity'
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'value_codeable_concept' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system',
                                                                                'code'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'coding'
                                                        ]
                                                    ]
                                                ],
                                                'required' => [
                                                    'value_codeable_concept'
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'value_sampled_data' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'origin' => [
                                                                'type' => 'number'
                                                            ],
                                                            'period' => [
                                                                'type' => 'number'
                                                            ],
                                                            'factor' => [
                                                                'type' => 'number'
                                                            ],
                                                            'lower_limit' => [
                                                                'type' => 'number'
                                                            ],
                                                            'upper_limit' => [
                                                                'type' => 'number'
                                                            ],
                                                            'dimensions' => [
                                                                'type' => 'number'
                                                            ],
                                                            'data' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'data'
                                                        ]
                                                    ]
                                                ],
                                                'required' => [
                                                    'value_sampled_data'
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'value_string' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value_string'
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'value_boolean' => [
                                                        'type' => 'boolean'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value_boolean'
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'value_range' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'low' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'value' => [
                                                                        'type' => 'number'
                                                                    ],
                                                                    'comparator' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'unit' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'system' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'code' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value',
                                                                    'system',
                                                                    'code'
                                                                ]
                                                            ],
                                                            'high' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'value' => [
                                                                        'type' => 'number'
                                                                    ],
                                                                    'comparator' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'unit' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'system' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'code' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value',
                                                                    'system',
                                                                    'code'
                                                                ]
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'low',
                                                            'high'
                                                        ],
                                                        'additionalProperties' => false
                                                    ]
                                                ],
                                                'required' => [
                                                    'value_range'
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'value_ratio' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'numerator' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'value' => [
                                                                        'type' => 'number'
                                                                    ],
                                                                    'comparator' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'unit' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'system' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'code' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value',
                                                                    'system',
                                                                    'code'
                                                                ]
                                                            ],
                                                            'denominator' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'value' => [
                                                                        'type' => 'number'
                                                                    ],
                                                                    'comparator' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'unit' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'system' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'code' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value',
                                                                    'system',
                                                                    'code'
                                                                ]
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'numerator',
                                                            'denominator'
                                                        ]
                                                    ]
                                                ],
                                                'required' => [
                                                    'value_ratio'
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'value_time' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value_time'
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'value_date_time' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value_date_time'
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'value_period' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'start' => [
                                                                'type' => 'string'
                                                            ],
                                                            'end' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'start'
                                                        ]
                                                    ]
                                                ],
                                                'required' => [
                                                    'value_period'
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'status',
                                    'categories',
                                    'code',
                                    'issued',
                                    'primary_source',
                                    'context'
                                ]
                            ]
                        ]
                    ]
                ],
                'allergy_intolerances' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'clinical_status' => [
                                        'type' => 'string'
                                    ],
                                    'verification_status' => [
                                        'type' => 'string'
                                    ],
                                    'type' => [
                                        'type' => 'string'
                                    ],
                                    'category' => [
                                        'type' => 'string'
                                    ],
                                    'criticality' => [
                                        'type' => 'string'
                                    ],
                                    'context' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'onset_date_time' => [
                                        'type' => 'string'
                                    ],
                                    'asserted_date' => [
                                        'type' => 'string'
                                    ],
                                    'primary_source' => [
                                        'type' => 'boolean'
                                    ],
                                    'last_occurrence' => [
                                        'type' => 'string'
                                    ]
                                ],
                                'allOf' => [
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'asserter' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'identifier' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'type' => [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'coding' => [
                                                                                'type' => 'array',
                                                                                'items' => [
                                                                                    'anyOf' => [
                                                                                        [
                                                                                            'type' => 'object',
                                                                                            'properties' => [
                                                                                                'system' => [
                                                                                                    'type' => 'string'
                                                                                                ],
                                                                                                'code' => [
                                                                                                    'type' => 'string'
                                                                                                ]
                                                                                            ],
                                                                                            'required' => [
                                                                                                'system'
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'text' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'value' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'report_origin' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'clinical_status',
                                    'verification_status',
                                    'type',
                                    'category',
                                    'criticality',
                                    'context',
                                    'code',
                                    'onset_date_time',
                                    'asserted_date',
                                    'primary_source'
                                ]
                            ]
                        ]
                    ]
                ],
                'immunizations' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'status' => [
                                        'type' => 'string'
                                    ],
                                    'not_given' => [
                                        'type' => 'boolean'
                                    ],
                                    'vaccine_code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'context' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'date' => [
                                        'type' => 'string'
                                    ],
                                    'primary_source' => [
                                        'type' => 'boolean'
                                    ],
                                    'manufacturer' => [
                                        'type' => 'string'
                                    ],
                                    'lot_number' => [
                                        'type' => 'string'
                                    ],
                                    'expiration_date' => [
                                        'type' => 'string'
                                    ],
                                    'site' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'route' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'dose_quantity' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'value' => [
                                                'type' => 'number'
                                            ],
                                            'comparator' => [
                                                'type' => 'string'
                                            ],
                                            'unit' => [
                                                'type' => 'string'
                                            ],
                                            'system' => [
                                                'type' => 'string'
                                            ],
                                            'code' => [
                                                'type' => 'string'
                                            ]
                                        ],
                                        'required' => [
                                            'value'
                                        ]
                                    ],
                                    'explanation' => [
                                        'type' => 'object',
                                        'allOf' => [
                                            [
                                                'oneOf' => [
                                                    [
                                                        'properties' => [
                                                            'reasons' => [
                                                                'type' => 'array'
                                                            ]
                                                        ]
                                                    ],
                                                    [
                                                        'properties' => [
                                                            'reasons_not_given' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'coding' => [
                                                                        'type' => 'array',
                                                                        'items' => [
                                                                            'anyOf' => [
                                                                                [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'system'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'text' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    'vaccination_protocols' => [
                                        'type' => 'array'
                                    ]
                                ],
                                'allOf' => [
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'performer' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'identifier' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'type' => [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'coding' => [
                                                                                'type' => 'array',
                                                                                'items' => [
                                                                                    'anyOf' => [
                                                                                        [
                                                                                            'type' => 'object',
                                                                                            'properties' => [
                                                                                                'system' => [
                                                                                                    'type' => 'string'
                                                                                                ],
                                                                                                'code' => [
                                                                                                    'type' => 'string'
                                                                                                ]
                                                                                            ],
                                                                                            'required' => [
                                                                                                'system'
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'text' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'value' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'report_origin' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'status',
                                    'not_given',
                                    'vaccine_code',
                                    'context',
                                    'date',
                                    'primary_source'
                                ]
                            ]
                        ]
                    ]
                ],
                'devices' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'identifier' => [
                                        'type' => 'array'
                                    ],
                                    'definition' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'coding'
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'type',
                                                    'value'
                                                ],
                                                'additionalProperties' => false
                                            ]
                                        ]
                                    ],
                                    'name' => [
                                        'type' => 'array'
                                    ],
                                    'status' => [
                                        'type' => 'string'
                                    ],
                                    'serial_number' => [
                                        'type' => 'string'
                                    ],
                                    'parent' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'coding'
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'type',
                                                    'value'
                                                ],
                                                'additionalProperties' => false
                                            ]
                                        ]
                                    ],
                                    'recorder' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'coding'
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'type',
                                                    'value'
                                                ],
                                                'additionalProperties' => false
                                            ]
                                        ]
                                    ],
                                    'context' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'property' => [
                                        'type' => 'array'
                                    ],
                                    'type' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'lot_number' => [
                                        'type' => 'string'
                                    ],
                                    'manufacturer' => [
                                        'type' => 'string'
                                    ],
                                    'manufacture_date' => [
                                        'type' => 'string'
                                    ],
                                    'expiration_date' => [
                                        'type' => 'string'
                                    ],
                                    'model_number' => [
                                        'type' => 'string'
                                    ],
                                    'note' => [
                                        'type' => 'string'
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'name',
                                    'status',
                                    'recorder',
                                    'context',
                                    'type'
                                ]
                            ]
                        ]
                    ]
                ],
                'risk_assessments' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'status' => [
                                        'type' => 'string'
                                    ],
                                    'method' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'context' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'asserted_date' => [
                                        'type' => 'string'
                                    ],
                                    'primary_source' => [
                                        'type' => 'boolean'
                                    ],
                                    'basis' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'reference' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'identifier' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'type' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'coding' => [
                                                                        'type' => 'array',
                                                                        'items' => [
                                                                            'anyOf' => [
                                                                                [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'text' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'coding'
                                                                ]
                                                            ],
                                                            'value' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'type',
                                                            'value'
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ],
                                            'references' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'identifier' => [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'type' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'coding' => [
                                                                                    'type' => 'array',
                                                                                    'items' => [
                                                                                        'anyOf' => [
                                                                                            [
                                                                                                'type' => 'object',
                                                                                                'properties' => [
                                                                                                    'system' => [
                                                                                                        'type' => 'string'
                                                                                                    ],
                                                                                                    'code' => [
                                                                                                        'type' => 'string'
                                                                                                    ]
                                                                                                ],
                                                                                                'required' => [
                                                                                                    'system'
                                                                                                ]
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ],
                                                                                'text' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ]
                                                                        ],
                                                                        'value' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value'
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    'predictions' => [
                                        'type' => 'array',
                                        'items' => [
                                            'anyOf' => [
                                                [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'outcome' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'coding' => [
                                                                    'type' => 'array',
                                                                    'items' => [
                                                                        'anyOf' => [
                                                                            [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                'text' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ],
                                                        'qualitative_risk' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'coding' => [
                                                                    'type' => 'array',
                                                                    'items' => [
                                                                        'anyOf' => [
                                                                            [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                'text' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ],
                                                        'relative_risk' => [
                                                            'type' => 'number'
                                                        ],
                                                        'rationale' => [
                                                            'type' => 'string'
                                                        ]
                                                    ],
                                                    'allOf' => [
                                                        [
                                                            'oneOf' => [
                                                                [
                                                                    'properties' => [
                                                                        'probability_range' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'low' => [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'value' => [
                                                                                            'type' => 'number'
                                                                                        ],
                                                                                        'comparator' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'unit' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'value',
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ],
                                                                                'high' => [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'value' => [
                                                                                            'type' => 'number'
                                                                                        ],
                                                                                        'comparator' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'unit' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'value',
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'low',
                                                                                'high'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'probability_decimal' => [
                                                                            'type' => 'number'
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ],
                                                        [
                                                            'oneOf' => [
                                                                [
                                                                    'properties' => [
                                                                        'when_range' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'low' => [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'value' => [
                                                                                            'type' => 'number'
                                                                                        ],
                                                                                        'comparator' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'unit' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'value',
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ],
                                                                                'high' => [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'value' => [
                                                                                            'type' => 'number'
                                                                                        ],
                                                                                        'comparator' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'unit' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'value',
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'low',
                                                                                'high'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'when_period' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'start' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'end' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'start'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    'mitigation' => [
                                        'type' => 'string'
                                    ],
                                    'comment' => [
                                        'type' => 'string'
                                    ]
                                ],
                                'allOf' => [
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'performer' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'identifier' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'type' => [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'coding' => [
                                                                                'type' => 'array',
                                                                                'items' => [
                                                                                    'anyOf' => [
                                                                                        [
                                                                                            'type' => 'object',
                                                                                            'properties' => [
                                                                                                'system' => [
                                                                                                    'type' => 'string'
                                                                                                ],
                                                                                                'code' => [
                                                                                                    'type' => 'string'
                                                                                                ]
                                                                                            ],
                                                                                            'required' => [
                                                                                                'system'
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'text' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'value' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'report_origin' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'reason_references' => [
                                                        'type' => 'array'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'reason_codes' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'status',
                                    'code',
                                    'context',
                                    'asserted_date',
                                    'primary_source'
                                ]
                            ]
                        ]
                    ]
                ],
                'medication_statements' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'based_on' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'asserted_date' => [
                                        'type' => 'string'
                                    ],
                                    'status' => [
                                        'type' => 'string'
                                    ],
                                    'context' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'primary_source' => [
                                        'type' => 'boolean'
                                    ],
                                    'effective_period' => [
                                        'type' => 'string'
                                    ],
                                    'medication_code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'note' => [
                                        'type' => 'string'
                                    ],
                                    'dosage' => [
                                        'type' => 'string'
                                    ]
                                ],
                                'allOf' => [
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'asserter' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'identifier' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'type' => [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'coding' => [
                                                                                'type' => 'array',
                                                                                'items' => [
                                                                                    'anyOf' => [
                                                                                        [
                                                                                            'type' => 'object',
                                                                                            'properties' => [
                                                                                                'system' => [
                                                                                                    'type' => 'string'
                                                                                                ],
                                                                                                'code' => [
                                                                                                    'type' => 'string'
                                                                                                ]
                                                                                            ],
                                                                                            'required' => [
                                                                                                'system'
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'text' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'value' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'report_origin' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'asserted_date',
                                    'status',
                                    'context',
                                    'primary_source',
                                    'medication_code'
                                ]
                            ]
                        ]
                    ]
                ],
                'medication_administrations' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'part_of' => [
                                        'type' => 'array'
                                    ],
                                    'status' => [
                                        'enum' => [
                                            'completed',
                                            'entered_in_error'
                                        ]
                                    ],
                                    'category' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'medication' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'context' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'performed_date_time' => [
                                        'type' => 'string'
                                    ],
                                    'performer' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'reason_references' => [
                                        'type' => 'array'
                                    ],
                                    'request' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'note' => [
                                        'type' => 'string'
                                    ],
                                    'dosage' => [
                                        'type' => 'array',
                                        'items' => [
                                            'anyOf' => [
                                                [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'text' => [
                                                            'type' => 'string'
                                                        ],
                                                        'site' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'coding' => [
                                                                    'type' => 'array',
                                                                    'items' => [
                                                                        'anyOf' => [
                                                                            [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                'text' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ],
                                                        'route' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'coding' => [
                                                                    'type' => 'array',
                                                                    'items' => [
                                                                        'anyOf' => [
                                                                            [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                'text' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ],
                                                        'method' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'coding' => [
                                                                    'type' => 'array',
                                                                    'items' => [
                                                                        'anyOf' => [
                                                                            [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'system' => [
                                                                                        'type' => 'string'
                                                                                    ],
                                                                                    'code' => [
                                                                                        'type' => 'string'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                'text' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ],
                                                        'dose' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'value' => [
                                                                    'type' => 'number'
                                                                ],
                                                                'unit' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ],
                                                            'required' => [
                                                                'value'
                                                            ]
                                                        ]
                                                    ],
                                                    'allOf' => [
                                                        [
                                                            'oneOf' => [
                                                                [
                                                                    'properties' => [
                                                                        'rate_ratio' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'numerator' => [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'value' => [
                                                                                            'type' => 'number'
                                                                                        ],
                                                                                        'unit' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'value'
                                                                                    ]
                                                                                ],
                                                                                'denominator' => [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'value' => [
                                                                                            'type' => 'number'
                                                                                        ],
                                                                                        'unit' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'value'
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'numerator',
                                                                                'denominator'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                [
                                                                    'properties' => [
                                                                        'rate_quantity' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'value' => [
                                                                                    'type' => 'number'
                                                                                ],
                                                                                'unit' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'value'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'status',
                                    'category',
                                    'medication',
                                    'context',
                                    'performed_date_time',
                                    'performer',
                                    'dosage'
                                ]
                            ]
                        ]
                    ]
                ],
                'diagnostic_reports' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'based_on' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'paper_referral' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'requisition' => [
                                                'type' => 'string'
                                            ],
                                            'requester_legal_entity_name' => [
                                                'type' => 'string'
                                            ],
                                            'requester_legal_entity_edrpou' => [
                                                'type' => 'string'
                                            ],
                                            'requester_employee_name' => [
                                                'type' => 'string'
                                            ],
                                            'service_request_date' => [
                                                'type' => 'string'
                                            ],
                                            'note' => [
                                                'type' => 'string'
                                            ]
                                        ],
                                        'required' => [
                                            'requester_legal_entity_edrpou',
                                            'requester_employee_name',
                                            'service_request_date'
                                        ],
                                        'additionalProperties' => false
                                    ],
                                    'status' => [
                                        'type' => 'string'
                                    ],
                                    'code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'category' => [
                                        'type' => 'array'
                                    ],
                                    'effective_period' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'start' => [
                                                'type' => 'string'
                                            ],
                                            'end' => [
                                                'type' => 'string'
                                            ]
                                        ],
                                        'required' => [
                                            'start'
                                        ]
                                    ],
                                    'issued' => [
                                        'type' => 'string'
                                    ],
                                    'conclusion' => [
                                        'type' => 'string'
                                    ],
                                    'conclusion_code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'recorded_by' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'coding'
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'type',
                                                    'value'
                                                ],
                                                'additionalProperties' => false
                                            ]
                                        ]
                                    ],
                                    'specimens' => [
                                        'type' => 'array'
                                    ],
                                    'used_references' => [
                                        'type' => 'array'
                                    ],
                                    'encounter' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'primary_source' => [
                                        'type' => 'boolean'
                                    ],
                                    'managing_organization' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'division' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ],
                                        'required' => [
                                            'identifier'
                                        ],
                                        'additionalProperties' => false
                                    ],
                                    'results_interpreter' => [
                                        'type' => 'object',
                                        'allOf' => [
                                            [
                                                'oneOf' => [
                                                    [
                                                        'properties' => [
                                                            'reference' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'identifier' => [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'type' => [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'coding' => [
                                                                                        'type' => 'array',
                                                                                        'items' => [
                                                                                            'anyOf' => [
                                                                                                [
                                                                                                    'type' => 'object',
                                                                                                    'properties' => [
                                                                                                        'system' => [
                                                                                                            'type' => 'string'
                                                                                                        ],
                                                                                                        'code' => [
                                                                                                            'type' => 'string'
                                                                                                        ]
                                                                                                    ],
                                                                                                    'required' => [
                                                                                                        'system'
                                                                                                    ]
                                                                                                ]
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'value' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ],
                                                                        'required' => [
                                                                            'value'
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    [
                                                        'properties' => [
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'allOf' => [
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'performer' => [
                                                        'type' => 'object',
                                                        'allOf' => [
                                                            [
                                                                'oneOf' => [
                                                                    [
                                                                        'properties' => [
                                                                            'reference' => [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'identifier' => [
                                                                                        'type' => 'object',
                                                                                        'properties' => [
                                                                                            'type' => [
                                                                                                'type' => 'object',
                                                                                                'properties' => [
                                                                                                    'coding' => [
                                                                                                        'type' => 'array',
                                                                                                        'items' => [
                                                                                                            'anyOf' => [
                                                                                                                [
                                                                                                                    'type' => 'object',
                                                                                                                    'properties' => [
                                                                                                                        'system' => [
                                                                                                                            'type' => 'string'
                                                                                                                        ],
                                                                                                                        'code' => [
                                                                                                                            'type' => 'string'
                                                                                                                        ]
                                                                                                                    ],
                                                                                                                    'required' => [
                                                                                                                        'system'
                                                                                                                    ]
                                                                                                                ]
                                                                                                            ]
                                                                                                        ]
                                                                                                    ],
                                                                                                    'text' => [
                                                                                                        'type' => 'string'
                                                                                                    ]
                                                                                                ]
                                                                                            ],
                                                                                            'value' => [
                                                                                                'type' => 'string'
                                                                                            ]
                                                                                        ],
                                                                                        'required' => [
                                                                                            'value'
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    [
                                                                        'properties' => [
                                                                            'text' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'report_origin' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'status',
                                    'code',
                                    'category',
                                    'issued',
                                    'recorded_by',
                                    'encounter',
                                    'primary_source'
                                ]
                            ]
                        ]
                    ]
                ],
                'procedures' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'status' => [
                                        'enum' => [
                                            'completed',
                                            'entered_in_error'
                                        ]
                                    ],
                                    'status_reason' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'based_on' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'encounter' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'recorded_by' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'coding'
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'type',
                                                    'value'
                                                ],
                                                'additionalProperties' => false
                                            ]
                                        ]
                                    ],
                                    'primary_source' => [
                                        'type' => 'boolean'
                                    ],
                                    'division' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ],
                                        'required' => [
                                            'identifier'
                                        ],
                                        'additionalProperties' => false
                                    ],
                                    'managing_organization' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'reason_references' => [
                                        'type' => 'array'
                                    ],
                                    'outcome' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'complication_details' => [
                                        'type' => 'array'
                                    ],
                                    'note' => [
                                        'type' => 'string'
                                    ],
                                    'category' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'paper_referral' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'requisition' => [
                                                'type' => 'string'
                                            ],
                                            'requester_legal_entity_name' => [
                                                'type' => 'string'
                                            ],
                                            'requester_legal_entity_edrpou' => [
                                                'type' => 'string'
                                            ],
                                            'requester_employee_name' => [
                                                'type' => 'string'
                                            ],
                                            'service_request_date' => [
                                                'type' => 'string'
                                            ],
                                            'note' => [
                                                'type' => 'string'
                                            ]
                                        ],
                                        'required' => [
                                            'requester_legal_entity_edrpou',
                                            'requester_employee_name',
                                            'service_request_date'
                                        ],
                                        'additionalProperties' => false
                                    ],
                                    'used_codes' => [
                                        'type' => 'array'
                                    ],
                                    'used_references' => [
                                        'type' => 'array'
                                    ],
                                    'focal_device' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'action' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'coding' => [
                                                        'type' => 'array',
                                                        'items' => [
                                                            'anyOf' => [
                                                                [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'system' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'code' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    'text' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ],
                                            'manipulated' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'identifier' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'type' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'coding' => [
                                                                        'type' => 'array',
                                                                        'items' => [
                                                                            'anyOf' => [
                                                                                [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'system'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'text' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ]
                                                            ],
                                                            'value' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'value'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        'required' => [
                                            'manipulated'
                                        ]
                                    ]
                                ],
                                'allOf' => [
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'performed_date_time' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'performed_period' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'start' => [
                                                                'type' => 'string'
                                                            ],
                                                            'end' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'start',
                                                            'end'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'performer' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'identifier' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'type' => [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'coding' => [
                                                                                'type' => 'array',
                                                                                'items' => [
                                                                                    'anyOf' => [
                                                                                        [
                                                                                            'type' => 'object',
                                                                                            'properties' => [
                                                                                                'system' => [
                                                                                                    'type' => 'string'
                                                                                                ],
                                                                                                'code' => [
                                                                                                    'type' => 'string'
                                                                                                ]
                                                                                            ],
                                                                                            'required' => [
                                                                                                'system'
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'text' => [
                                                                                'type' => 'string'
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'value' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'value'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'report_origin' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'status',
                                    'code',
                                    'encounter',
                                    'recorded_by',
                                    'primary_source',
                                    'managing_organization',
                                    'category'
                                ]
                            ]
                        ]
                    ]
                ],
                'clinical_impressions' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'status' => [
                                        'type' => 'string'
                                    ],
                                    'description' => [
                                        'type' => 'string'
                                    ],
                                    'code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'encounter' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'assessor' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ],
                                            'display_value' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'previous' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'problems' => [
                                        'type' => 'array',
                                        'items' => [
                                            'anyOf' => [
                                                [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'identifier' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'type' => [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'coding' => [
                                                                            'type' => 'array',
                                                                            'items' => [
                                                                                'anyOf' => [
                                                                                    [
                                                                                        'type' => 'object',
                                                                                        'properties' => [
                                                                                            'system' => [
                                                                                                'type' => 'string'
                                                                                            ],
                                                                                            'code' => [
                                                                                                'const' => 'condition'
                                                                                            ]
                                                                                        ],
                                                                                        'required' => [
                                                                                            'system'
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ],
                                                                        'text' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ]
                                                                ],
                                                                'value' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ],
                                                            'required' => [
                                                                'value'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    'summary' => [
                                        'type' => 'string'
                                    ],
                                    'findings' => [
                                        'type' => 'array',
                                        'items' => [
                                            'anyOf' => [
                                                [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'item_reference' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'identifier' => [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'type' => [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'coding' => [
                                                                                    'type' => 'array',
                                                                                    'items' => [
                                                                                        'anyOf' => [
                                                                                            [
                                                                                                'type' => 'object',
                                                                                                'properties' => [
                                                                                                    'system' => [
                                                                                                        'type' => 'string'
                                                                                                    ],
                                                                                                    'code' => [
                                                                                                        'enum' => [
                                                                                                            'condition',
                                                                                                            'observaton'
                                                                                                        ]
                                                                                                    ]
                                                                                                ],
                                                                                                'required' => [
                                                                                                    'system'
                                                                                                ]
                                                                                            ]
                                                                                        ]
                                                                                    ]
                                                                                ],
                                                                                'text' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ]
                                                                        ],
                                                                        'value' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ],
                                                                    'required' => [
                                                                        'value'
                                                                    ]
                                                                ]
                                                            ]
                                                        ],
                                                        'basis' => [
                                                            'type' => 'string'
                                                        ]
                                                    ],
                                                    'required' => [
                                                        'item_reference'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    'supporting_info' => [
                                        'type' => 'array',
                                        'items' => [
                                            'anyOf' => [
                                                [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'identifier' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'type' => [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'coding' => [
                                                                            'type' => 'array',
                                                                            'items' => [
                                                                                'anyOf' => [
                                                                                    [
                                                                                        'type' => 'object',
                                                                                        'properties' => [
                                                                                            'system' => [
                                                                                                'type' => 'string'
                                                                                            ],
                                                                                            'code' => [
                                                                                                'enum' => [
                                                                                                    'episode_of_care',
                                                                                                    'procedure',
                                                                                                    'diagnostic_report',
                                                                                                    'encounter'
                                                                                                ]
                                                                                            ]
                                                                                        ],
                                                                                        'required' => [
                                                                                            'system'
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ],
                                                                        'text' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ]
                                                                ],
                                                                'value' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ],
                                                            'required' => [
                                                                'value'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    'note' => [
                                        'type' => 'string'
                                    ]
                                ],
                                'allOf' => [
                                    [
                                        'oneOf' => [
                                            [
                                                'properties' => [
                                                    'effective_date_time' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'properties' => [
                                                    'effective_period' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'start' => [
                                                                'type' => 'string'
                                                            ],
                                                            'end' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'start',
                                                            'end'
                                                        ],
                                                        'additionalProperties' => false
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'status',
                                    'code',
                                    'encounter',
                                    'assessor'
                                ]
                            ]
                        ]
                    ]
                ],
                'specimens' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'status' => [
                                        'enum' => [
                                            'available',
                                            'unavailable'
                                        ]
                                    ],
                                    'type' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'condition' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'note' => [
                                        'type' => 'string'
                                    ],
                                    'managing_organization' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'registered_by' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system',
                                                                                'code'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ],
                                        'required' => [
                                            'identifier'
                                        ]
                                    ],
                                    'parent' => [
                                        'type' => 'array'
                                    ],
                                    'request' => [
                                        'type' => 'array'
                                    ],
                                    'collection' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'collector' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'identifier' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'type' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'coding' => [
                                                                        'type' => 'array',
                                                                        'items' => [
                                                                            'anyOf' => [
                                                                                [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'enum' => [
                                                                                                'employee',
                                                                                                'patient'
                                                                                            ]
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'text' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ]
                                                            ],
                                                            'value' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'value'
                                                        ]
                                                    ]
                                                ],
                                                'required' => [
                                                    'identifier'
                                                ]
                                            ],
                                            'duration' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'value' => [
                                                        'type' => 'number'
                                                    ],
                                                    'system' => [
                                                        'type' => 'string'
                                                    ],
                                                    'code' => [
                                                        'type' => 'string'
                                                    ],
                                                    'comparator' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value',
                                                    'system',
                                                    'code'
                                                ]
                                            ],
                                            'quantity' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'value' => [
                                                        'type' => 'number'
                                                    ],
                                                    'system' => [
                                                        'type' => 'string'
                                                    ],
                                                    'code' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value',
                                                    'system',
                                                    'code'
                                                ]
                                            ],
                                            'method' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'coding' => [
                                                        'type' => 'array',
                                                        'items' => [
                                                            'anyOf' => [
                                                                [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'system' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'code' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    'text' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ],
                                            'body_site' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'coding' => [
                                                        'type' => 'array',
                                                        'items' => [
                                                            'anyOf' => [
                                                                [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'system' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'code' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    'text' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ],
                                            'fasting_status_codeable_concept' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'coding' => [
                                                        'type' => 'array',
                                                        'items' => [
                                                            'anyOf' => [
                                                                [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'system' => [
                                                                            'type' => 'string'
                                                                        ],
                                                                        'code' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    'text' => [
                                                        'type' => 'string'
                                                    ]
                                                ]
                                            ],
                                            'procedure' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'identifier' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'type' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'coding' => [
                                                                        'type' => 'array',
                                                                        'items' => [
                                                                            'anyOf' => [
                                                                                [
                                                                                    'type' => 'object',
                                                                                    'properties' => [
                                                                                        'system' => [
                                                                                            'type' => 'string'
                                                                                        ],
                                                                                        'code' => [
                                                                                            'type' => 'string'
                                                                                        ]
                                                                                    ],
                                                                                    'required' => [
                                                                                        'system',
                                                                                        'code'
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ],
                                                                    'text' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ]
                                                            ],
                                                            'value' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'value'
                                                        ]
                                                    ]
                                                ],
                                                'required' => [
                                                    'identifier'
                                                ]
                                            ]
                                        ],
                                        'allOf' => [
                                            [
                                                'oneOf' => [
                                                    [
                                                        'properties' => [
                                                            'collected_date_time' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    [
                                                        'properties' => [
                                                            'collected_period' => [
                                                                'type' => 'object',
                                                                'properties' => [
                                                                    'start' => [
                                                                        'type' => 'string'
                                                                    ],
                                                                    'end' => [
                                                                        'type' => 'string'
                                                                    ]
                                                                ],
                                                                'required' => [
                                                                    'start'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        'required' => [
                                            'collector',
                                            'quantity'
                                        ]
                                    ],
                                    'container' => [
                                        'type' => 'array'
                                    ],
                                    'context' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'received_time' => [
                                        'type' => 'string'
                                    ],
                                    'status_reason' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'status',
                                    'type',
                                    'managing_organization',
                                    'registered_by',
                                    'collection',
                                    'container',
                                    'context',
                                    'collection',
                                    'status'
                                ]
                            ]
                        ]
                    ]
                ],
                'device_dispenses' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'status' => [
                                        'type' => 'string'
                                    ],
                                    'performer' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ],
                                        'required' => [
                                            'identifier'
                                        ]
                                    ],
                                    'location' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ],
                                        'required' => [
                                            'identifier'
                                        ]
                                    ],
                                    'note' => [
                                        'type' => 'string'
                                    ],
                                    'based_on' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ],
                                        'required' => [
                                            'identifier'
                                        ]
                                    ],
                                    'when_handed_over' => [
                                        'type' => 'string'
                                    ],
                                    'part_of' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ],
                                            'display_value' => [
                                                'type' => 'string'
                                            ]
                                        ],
                                        'required' => [
                                            'identifier',
                                            'display_value'
                                        ]
                                    ],
                                    'encounter' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ],
                                            'display_value' => [
                                                'type' => 'string'
                                            ]
                                        ],
                                        'required' => [
                                            'identifier',
                                            'display_value'
                                        ]
                                    ],
                                    'supporting_info' => [
                                        'type' => 'array',
                                        'items' => [
                                            'anyOf' => [
                                                [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'identifier' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'type' => [
                                                                    'type' => 'object',
                                                                    'properties' => [
                                                                        'coding' => [
                                                                            'type' => 'array',
                                                                            'items' => [
                                                                                'anyOf' => [
                                                                                    [
                                                                                        'type' => 'object',
                                                                                        'properties' => [
                                                                                            'system' => [
                                                                                                'type' => 'string'
                                                                                            ],
                                                                                            'code' => [
                                                                                                'enum' => [
                                                                                                    'condition',
                                                                                                    'observation',
                                                                                                    'diagnostic_report',
                                                                                                    'procedure',
                                                                                                    'encounter',
                                                                                                    'episode',
                                                                                                    'device',
                                                                                                    'device_association'
                                                                                                ]
                                                                                            ]
                                                                                        ],
                                                                                        'required' => [
                                                                                            'system'
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ],
                                                                        'text' => [
                                                                            'type' => 'string'
                                                                        ]
                                                                    ]
                                                                ],
                                                                'value' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ],
                                                            'required' => [
                                                                'value'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    'details' => [
                                        'type' => 'array',
                                        'minItems' => 1,
                                        'items' => [
                                            [
                                                'type' => 'object',
                                                'properties' => [
                                                    'quantity' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'value' => [
                                                                'const' => 10
                                                            ],
                                                            'system' => [
                                                                'const' => 'device_unit'
                                                            ],
                                                            'code' => [
                                                                'const' => 'piece'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'value',
                                                            'system',
                                                            'code'
                                                        ],
                                                        'additionalProperties' => false
                                                    ],
                                                    'sell_price' => [
                                                        'const' => 18.65
                                                    ]
                                                ],
                                                'allOf' => [
                                                    [
                                                        'oneOf' => [
                                                            [
                                                                'properties' => [
                                                                    'device' => [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'identifier' => [
                                                                                'type' => 'object',
                                                                                'properties' => [
                                                                                    'type' => [
                                                                                        'type' => 'object',
                                                                                        'properties' => [
                                                                                            'coding' => [
                                                                                                'type' => 'array',
                                                                                                'items' => [
                                                                                                    'anyOf' => [
                                                                                                        [
                                                                                                            'type' => 'object',
                                                                                                            'properties' => [
                                                                                                                'system' => [
                                                                                                                    'const' => 'eHealth/resources'
                                                                                                                ],
                                                                                                                'code' => [
                                                                                                                    'enum' => [
                                                                                                                        'device_definition',
                                                                                                                        'device'
                                                                                                                    ]
                                                                                                                ]
                                                                                                            ],
                                                                                                            'required' => [
                                                                                                                'system',
                                                                                                                'code'
                                                                                                            ],
                                                                                                            'additionalProperties' => false
                                                                                                        ]
                                                                                                    ]
                                                                                                ]
                                                                                            ]
                                                                                        ],
                                                                                        'required' => [
                                                                                            'coding'
                                                                                        ],
                                                                                        'additionalProperties' => false
                                                                                    ],
                                                                                    'value' => [
                                                                                        'const' => 'b4a6d991-0bf7-476f-b3cf-bec83f044b1b'
                                                                                    ]
                                                                                ],
                                                                                'required' => [
                                                                                    'value'
                                                                                ],
                                                                                'additionalProperties' => false
                                                                            ]
                                                                        ],
                                                                        'additionalProperties' => false
                                                                    ]
                                                                ]
                                                            ],
                                                            [
                                                                'properties' => [
                                                                    'device_code' => [
                                                                        'type' => 'object',
                                                                        'properties' => [
                                                                            'coding' => [
                                                                                'type' => 'array',
                                                                                'items' => [
                                                                                    'anyOf' => [
                                                                                        [
                                                                                            'type' => 'object',
                                                                                            'properties' => [
                                                                                                'system' => [
                                                                                                    'const' => 'device_definition_classification_type'
                                                                                                ],
                                                                                                'code' => [
                                                                                                    'const' => '30221'
                                                                                                ]
                                                                                            ],
                                                                                            'required' => [
                                                                                                'system',
                                                                                                'code'
                                                                                            ],
                                                                                            'additionalProperties' => false
                                                                                        ]
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ],
                                                                        'additionalProperties' => false
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'required' => [
                                                    'quantity'
                                                ],
                                                'additionalProperties' => false
                                            ]
                                        ],
                                        'additionalItems' => false
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'status',
                                    'performer',
                                    'location',
                                    'when_handed_over',
                                    'encounter',
                                    'details'
                                ]
                            ]
                        ]
                    ]
                ],
                'device_associations' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'device' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'status' => [
                                        'type' => 'string'
                                    ],
                                    'body_site' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'association_date' => [
                                        'type' => 'string'
                                    ],
                                    'recorded' => [
                                        'type' => 'string'
                                    ],
                                    'context' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'recorder' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'coding'
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'type',
                                                    'value'
                                                ],
                                                'additionalProperties' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'device',
                                    'status',
                                    'recorded',
                                    'context',
                                    'recorder'
                                ]
                            ]
                        ]
                    ]
                ],
                'detected_issues' => [
                    'type' => 'array',
                    'items' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string'
                                    ],
                                    'status' => [
                                        'type' => 'string'
                                    ],
                                    'code' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'coding' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'anyOf' => [
                                                        [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'system' => [
                                                                    'type' => 'string'
                                                                ],
                                                                'code' => [
                                                                    'type' => 'string'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ],
                                            'text' => [
                                                'type' => 'string'
                                            ]
                                        ]
                                    ],
                                    'encounter' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'identified_date_time' => [
                                        'type' => 'string'
                                    ],
                                    'recorder' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'coding'
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'type',
                                                    'value'
                                                ],
                                                'additionalProperties' => false
                                            ]
                                        ]
                                    ],
                                    'author' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'enum' => [
                                                                                        'employee',
                                                                                        'patient'
                                                                                    ]
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system',
                                                                                'code'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ],
                                        'required' => [
                                            'identifier'
                                        ]
                                    ],
                                    'implicated' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'coding'
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'type',
                                                    'value'
                                                ],
                                                'additionalProperties' => false
                                            ]
                                        ]
                                    ],
                                    'detail' => [
                                        'type' => 'string'
                                    ],
                                    'subject' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'value'
                                                ]
                                            ]
                                        ]
                                    ],
                                    'based_on' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'identifier' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'type' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'coding' => [
                                                                'type' => 'array',
                                                                'items' => [
                                                                    'anyOf' => [
                                                                        [
                                                                            'type' => 'object',
                                                                            'properties' => [
                                                                                'system' => [
                                                                                    'type' => 'string'
                                                                                ],
                                                                                'code' => [
                                                                                    'type' => 'string'
                                                                                ]
                                                                            ],
                                                                            'required' => [
                                                                                'system'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ],
                                                            'text' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'coding'
                                                        ]
                                                    ],
                                                    'value' => [
                                                        'type' => 'string'
                                                    ]
                                                ],
                                                'required' => [
                                                    'type',
                                                    'value'
                                                ],
                                                'additionalProperties' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => [
                                    'id',
                                    'status',
                                    'encounter',
                                    'recorder',
                                    'author',
                                    'subject'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'required' => [
                'encounter'
            ]
        ];
    }

    public function schemaEpisodeRequest(): array
    {
        return [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string'
                ],
                'type' => [
                    'type' => 'object',
                    'properties' => [
                        'system' => [
                            'type' => 'string'
                        ],
                        'code' => [
                            'type' => 'string'
                        ]
                    ]
                ],
                'status' => [
                    'type' => 'string'
                ],
                'name' => [
                    'type' => 'string'
                ],
                'managing_organization' => [
                    'type' => 'object',
                    'properties' => [
                        'identifier' => [
                            'type' => 'object',
                            'properties' => [
                                'type' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'coding' => [
                                            'type' => 'array',
                                            'items' => [
                                                'anyOf' => [
                                                    [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'system' => [
                                                                'type' => 'string'
                                                            ],
                                                            'code' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'system'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        'text' => [
                                            'type' => 'string'
                                        ]
                                    ]
                                ],
                                'value' => [
                                    'type' => 'string'
                                ]
                            ],
                            'required' => [
                                'value'
                            ]
                        ]
                    ]
                ],
                'period' => [
                    'type' => 'object',
                    'properties' => [
                        'start' => [
                            'type' => 'string'
                        ]
                    ],
                    'required' => [
                        'start'
                    ]
                ],
                'care_manager' => [
                    'type' => 'object',
                    'properties' => [
                        'identifier' => [
                            'type' => 'object',
                            'properties' => [
                                'type' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'coding' => [
                                            'type' => 'array',
                                            'items' => [
                                                'anyOf' => [
                                                    [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'system' => [
                                                                'type' => 'string'
                                                            ],
                                                            'code' => [
                                                                'type' => 'string'
                                                            ]
                                                        ],
                                                        'required' => [
                                                            'system'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        'text' => [
                                            'type' => 'string'
                                        ]
                                    ]
                                ],
                                'value' => [
                                    'type' => 'string'
                                ]
                            ],
                            'required' => [
                                'value'
                            ]
                        ]
                    ]
                ]
            ],
            'required' => [
                'id',
                'type',
                'status',
                'name',
                'managing_organization',
                'period',
                'care_manager'
            ]
        ];
    }
}
