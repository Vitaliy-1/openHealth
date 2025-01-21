<?php

return [
    'api'                                 => [
        'domain'        => env('EHEALTH_API_URL', 'private-anon-cb2ce4f7fc-uaehealthapi.apiary-mock.com'),
        'token'         => env('EHEALTH_X_CUSTOM_PSK', 'X-Custom-PSK'),
        'api_key'       => env('EHEALTH_API_KEY', ''),
        'callback_prod' => env('EHEALTH_CALBACK_PROD', true),
        'auth_host'     => env('EHEALTH_AUTH_HOST', 'https://auth-preprod.ehealth.gov.ua/sign-in'),
        'redirect_uri'  => env('EHEALTH_REDIRECT_URI', 'https://openhealths.com/ehealth/oauth'),
        'url_dev'       => env('EHEALTH_URL_DEV', 'http://localhost'),
        'timeout'       => 10,
        'queueTimeout'  => 60,
        'cooldown'      => 300,
        'retries'       => 10
    ],
    'capitation_contract_max_period_days' => 366,
    'legal_entity_type'                   => [
        'PRIMARY_CARE' => [
            'roles' => ['OWNER', 'ADMIN', 'DOCTOR', 'HR', 'ASSISTANT', 'RECEPTIONIST', 'MED_ADMIN', 'LABORANT'],
            //            'positions' => [
            //                'P3', 'P274', 'P93', 'P202', 'P215', 'P159', 'P118', 'P46', 'P54', 'P99', 'P109', 'P96', 'P245', 'P279',
            //                'P63', 'P123', 'P17', 'P62', 'P45', 'P10', 'P74', 'P37', 'P114', 'P127', 'P214', 'P179', 'P156', 'P145',
            //                'P103', 'P115', 'P126', 'P120', 'P268', 'P110', 'P43', 'P130', 'P203', 'P81', 'P273', 'P95', 'P191',
            //                'P42',
            //                'P38', 'P105', 'P23', 'P197', 'P154', 'P65', 'P58', 'P175', 'P61', 'P98', 'P13', 'P177', 'P173', 'P72',
            //                'P256', 'P178', 'P153', 'P212', 'P53', 'P48', 'P7', 'P106', 'P122', 'P52', 'P158', 'P15', 'P22', 'P39',
            //                'P92', 'P112', 'P71', 'P164', 'P170', 'P266', 'P224', 'P270', 'P78', 'P242', 'P160', 'P2', 'P213',
            //                'P152',
            //                'P26', 'P247', 'P192', 'P36', 'P67', 'P181', 'P124', 'P73', 'P228', 'P55', 'P117', 'P249', 'P91', 'P70',
            //                'P231', 'P229', 'P97', 'P167', 'P169', 'P238', 'P149', 'P150', 'P128', 'P64', 'P51', 'P83', 'P44',
            //                'P241',
            //                'P4', 'P50', 'P250', 'P116', 'P185', 'P276', 'P76', 'P40', 'P69', 'P84', 'P82', 'P176', 'P174', 'P278',
            //                'P155', 'P9', 'P257', 'P29', 'P252', 'P243', 'P24', 'P180', 'P166', 'P201', 'P16', 'P200', 'P210',
            //                'P34',
            //                'P272', 'P168', 'P275', 'P194', 'P165', 'P146', 'P151', 'P111', 'P85', 'P265', 'P87', 'P246', 'P6',
            //                'P77',
            //                'P41', 'P204', 'P94', 'P240', 'P79', 'P14', 'P216', 'P32', 'P59', 'P230', 'P1', 'P88', 'P248', 'P172',
            //                'P75', 'P113', 'P196', 'P28', 'P129', 'P206', 'P57', 'P162', 'P35', 'P107', 'P184', 'P68', 'P131',
            //                'P189',
            //                'P211', 'P60', 'P25', 'P56', 'P161', 'P5', 'P89', 'P188', 'P183', 'P100', 'P47', 'P269', 'P66', 'P8',
            //                'P207', 'P255', 'P119', 'P90', 'P86', 'P27', 'P199', 'P108', 'P163', 'P157', 'P277', 'P11'
            //            ],
        ],
    ],
    'rate_limit'                          => [
        'employee_request' => 20
    ],
    'employee_type'                       => [
        'OWNER'           => [
            'position' => [
                'P1', 'P2', 'P3', 'P4', 'P5', 'P6', 'P18', 'P19', 'P22', 'P23', 'P24', 'P25', 'P26', 'P32', 'P229',
                'P230', 'P231', 'P232', 'P233', 'P234', 'P235', 'P236', 'P237', 'P238', 'P239', 'P240', 'P247', 'P249',
                'P257'
            ]
        ],
        'ADMIN'           => [
            'position' => [
                ' P5', 'P6', 'P14', 'P18', 'P19'
            ]
        ],
        'HR'              => [
            'position' => ['P14']
        ],
        'ASSISTANT'       => [
            'position'        => [
                'P17', 'P66', 'P169', 'P170', 'P171', 'P173', 'P174', 'P175', 'P176', 'P177', 'P178', 'P179', 'P180',
                'P181', 'P182', 'P183', 'P184', 'P185', 'P186', 'P187', 'P188', 'P189', 'P190', 'P191', 'P192', 'P193',
                'P194', 'P195', 'P196', 'P197', 'P198', 'P199', 'P200', 'P201', 'P202', 'P203', 'P204', 'P205', 'P206',
                'P207', 'P208', 'P209', 'P210', 'P211', 'P212', 'P213', 'P214', 'P215', 'P216', 'P250', 'P251', 'P252',
                'P253', 'P256',
            ],
            'speciality_type' => [
                "ORTHOPEDIC_DENTISTRY", "X_RAY_RADIOLOGY", "SANOLOGY", "STOMATOLOGY", "GENERAL_MEDICINE",
                "MEDICAL_CASE_EMERGENCY_MEDICINE", "PUBLIC_HEALTH_AND_PREVENTIVE_MEDICINE"
            ]
        ],
        'DOCTOR'          => [
            'position'        => ['P7', 'P8', 'P9', 'P10', 'P11'],
            'speciality_type' => ["FAMILY_DOCTOR", "PEDIATRICIAN", "THERAPIST"]
        ],
        'LABORANT'        => [
            'position'        => [
                'P17', 'P170', 'P173', 'P241', 'P242', 'P243', 'P244', 'P251', 'P256', 'P271', 'P272', 'P273', 'P274',
                'P276', 'P277', 'P278', 'P279', 'P281'
            ],
            'speciality_type' => [
                'VIROLOGY', 'MICROBIOLOGY', 'LABORATORY_GENETICS', 'LABORATORY_IMMUNOLOGY', 'CLINICAL_DIAGNOSTIC',
                'PARASITOLOGY', 'BACTERIOLOGY', 'CLINICAL_BIOCHEMISTRY'
            ]
        ],
        'MED_COORDINATOR' => [
            'position' => ['P280']
        ],
        'NHS ADMIN'       => [
            'position' => [
                'P27', 'P28', 'P29', 'P30', 'P31', 'P237', 'P238', 'P239',
            ],
        ],
        'RECEPTIONIST'    => [
            'position' => ['P15']
        ],
        'MED_ADMIN'       => [
            'position' => [
                'P5', 'P6', 'P7', 'P8', 'P9', 'P10', 'P11', 'P12', 'P13', 'P33', 'P34', 'P35', 'P36', 'P37', 'P38',
                'P39',
                'P40', 'P41', 'P42', 'P43', 'P44', 'P45', 'P46', 'P47', 'P48', 'P49', 'P50', 'P51', 'P52', 'P53', 'P54',
                'P55', 'P56', 'P57', 'P58', 'P59', 'P60', 'P61', 'P62', 'P63', 'P64', 'P65', 'P66', 'P67', 'P68', 'P69',
                'P70', 'P71', 'P72', 'P73', 'P74', 'P75', 'P76', 'P77', 'P78', 'P79', 'P80', 'P81', 'P82', 'P83', 'P84',
                'P85', 'P86', 'P87', 'P88', 'P89', 'P90', 'P91', 'P92', 'P93', 'P94', 'P95', 'P96', 'P97', 'P98', 'P99',
                'P100', 'P101', 'P102', 'P103', 'P104', 'P105', 'P106', 'P107', 'P108', 'P109', 'P110', 'P111', 'P112',
                'P113', 'P114', 'P115', 'P116', 'P117', 'P118', 'P119', 'P120', 'P121', 'P122', 'P123', 'P124', 'P125',
                'P126', 'P127', 'P128', 'P129', 'P130', 'P131', 'P132', 'P133', 'P134', 'P135', 'P136', 'P137', 'P138',
                'P139', 'P140', 'P141', 'P142', 'P143', 'P144', 'P145', 'P146', 'P147', 'P148', 'P149', 'P150', 'P151',
                'P152', 'P153', 'P154', 'P155', 'P156', 'P157', 'P158', 'P159', 'P160', 'P161', 'P162', 'P163', 'P164',
                'P165', 'P166', 'P167', 'P228', 'P248', 'P245', 'P258', 'P266', 'P267', 'P268', 'P269', 'P270', 'P1',
                'P2',
                'P3', 'P4', 'P5', 'P6', 'P23', 'P24', 'P25', 'P26', 'P32', 'P229', 'P230', 'P231', 'P249', 'P257'
            ]
        ]
    ],
    'doctors_type' => [
        'LABORANT','DOCTOR','MED_ADMIN','ASSISTANT','MED_COORDINATOR'
    ],

    // Set the test environment
    'test' => [
        'client_id' => env('TEST_CLIENT_ID'),
        'client_secret' => env('TEST_CLIENT_SECRET')
    ],
];
