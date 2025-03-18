<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class LegalEntitiesApi extends Request
{
    public const URL_V2 = '/api/v2/legal_entities';
    public const URL = '/api/legal_entities';


    public static function _get(array $params = []): array
    {
       return (new Request('GET', self::URL_V2, $params))->sendRequest();
    }

    public static function _getById(string $id): array
    {
        $params = [
            'legal_entity_id' => $id
        ];
        return (new Request('GET', self::URL_V2.'/'.$id,$params))->sendRequest();
    }

    public static function _verify(string $id): array
    {
        return (new Request('PATCH', self::URL.'/'.$id.'/actions/nhs_verify',[]))->sendRequest();
    }

    public static function _createOrUpdate(array $params = []): array
    {
        return [
            "data" => [
                "accreditation" => [
                    "category" => "SECOND",
                    "expiry_date" => "2017-02-28",
                    "issued_date" => "2017-02-28",
                    "order_date" => "2017-02-28",
                    "order_no" => "fd123443",
                ],
                "archive" => [
                    0 => [
                        "date" => "2025-03-04",
                        "place" => "вул. Грушевського, 12"
                    ]
                ],
                "beneficiary" => "Мірошник Юрій",
                "edr" => [
                    "edrpou" => "2711210915",
                    "id" => "24a36ee9-d7ef-4e9e-9f1c-210621d63eea",
                    "kveds" => [
                        0 => [
                            "code" => "62.01",
                            "is_primary" => true,
                            "name" => "Комп'ютерне програмування",
                        ],
                        1 => [
                            "code" => "62.09",
                            "is_primary" => false,
                            "name" => "Інша діяльність у сфері інформаційних технологій і комп'ютерних систем"
                        ],
                        2 => [
                            "code" => "63.11",
                            "is_primary" => false,
                            "name" => "Оброблення даних, розміщення інформації на веб-вузлах і пов'язана з ними діяльність"
                        ]
                    ],
                    "legal_form" => null,
                    "name" => "МІРОШНИК ЮРІЙ МИКОЛАЙОВИЧ",
                    "public_name" => "МІРОШНИК ЮРІЙ МИКОЛАЙОВИЧ",
                    "registration_address" => [
                        "address" => "Україна, 20202, Черкаська обл., Звенигородський р-н, місто Звенигородка, вул. Кримського, будинок 2, квартира 36",
                        "country" => "Україна",
                        "parts" => [
                            "atu" => "Черкаська обл., Звенигородський р-н, місто Звенигородка",
                            "atu_code" => "71020130010081908",
                            "building" => null,
                            "building_type" => null,
                            "house" => "2",
                            "house_type" => "будинок",
                            "num" => "36",
                            "num_type" => "квартира",
                            "street" => "вул. Кримського"
                        ],
                        "zip" => "20202"
                    ],
                    "short_name" => null,
                    "state" => 1
                ],
                "edr_verified" => null,
                "edrpou" => "2711210915",
                "email" => "admin@local.net",
                "id" => "3f0cd541-6687-4201-87df-16d2ca0aaf26",
                "inserted_at" => "2025-01-30T13:12:42.368309Z",
                "inserted_by" => "4261eacf-8008-4e62-899f-de1e2f7065f0",
                "is_active" => true,
                "license" => [
                    "active_from_date" => "2025-04-02",
                    "expiry_date" => "2025-05-10",
                    "id" => "98259101-c132-4db6-b747-040f473ef7b6",
                    "inserted_at" => "2025-01-30T13:12:42Z",
                    "inserted_by" => "4261eacf-8008-4e62-899f-de1e2f7065f0",
                    "is_active" => true,
                    "issued_by" => "Кваліфікаційна комісія",
                    "issued_date" => "2025-03-01",
                    "issuer_status" => null,
                    "license_number" => "fd123443",
                    "order_no" => "АА90803",
                    "type" => "MSP",
                    "updated_at" => "2025-03-13T13:39:14Z",
                    "updated_by" => "4261eacf-8008-4e62-899f-de1e2f7065f0",
                    "what_licensed" => "Медична практика"
                ],
                "nhs_comment" => "",
                "nhs_reviewed" => false,
                "nhs_verified" => false,
                "phones" => [
                    0 => [
                        "number" => "+380444444444",
                        "type" => "LAND_LINE"
                    ]
                ],
                "receiver_funds_code" => "777",
                "residence_address" => [
                    "apartment" => "42",
                    "area" => "ОДЕСЬКА",
                    "building" => "2",
                    "country" => "UA",
                    "region" => "БІЛГОРОД-ДНІСТРОВСЬКИЙ",
                    "settlement" => "БІЛГОРОД-ДНІСТРОВСЬКИЙ",
                    "settlement_id" => "b921142c-ef38-4c22-bdf3-7011df718c1c",
                    "settlement_type" => "CITY",
                    "street" => "Шевченка",
                    "street_type" => "STREET",
                    "type" => "RESIDENCE",
                    "zip" => "33333"
                ],
                "status" => "SUSPENDED",
                "type" => "PRIMARY_CARE",
                "updated_at" => "2025-03-13T13:39:14.275378Z",
                "updated_by" => "4261eacf-8008-4e62-899f-de1e2f7065f0",
                "website" => "www.openhealth.com.ua",
            ],
            "urgent" => [
                "security" => [
                    "secret_key" => "VnBoQ29hWm05UDF5UENySW000WdOdz09",
                    "client_id" => "3f0cd541-6687-4201-87df-16d2ca0aaf26",
                    "redirect_uri" => "https://openhealths.com"
                ],
                "employee_request_id" => "d098aee7-5ab3-4a24-a6ba-811f9cf94c6d"
            ]
        ];

        // return (array) new Request('PUT', self::URL_V2, $params,false)->sendRequest();
    }
}
