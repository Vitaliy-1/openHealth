<?php

declare(strict_types=1);

return [
    'api' => [
        'domain' => env('EHEALTH_API_URL', 'private-anon-cb2ce4f7fc-uaehealthapi.apiary-mock.com'),
        'token' => env('EHEALTH_X_CUSTOM_PSK', 'X-Custom-PSK'),
        'api_key' => env('EHEALTH_API_KEY', ''),
        'callback_prod' => env('EHEALTH_CALBACK_PROD', true),
        'auth_host' => env('EHEALTH_AUTH_HOST', 'https://auth-preprod.ehealth.gov.ua/sign-in'),
        'redirect_uri' => env('EHEALTH_REDIRECT_URI', 'https://openhealths.com/ehealth/oauth'),
        'url_dev' => env('EHEALTH_URL_DEV', 'http://localhost'),
        'auth_ehealth' => env('EHEALTH_CODE_TOKEN', 'user_id_auth_ehealth'),
        'oauth' => [
            'bearer_token' => env('EHEALTH_OAUTH_TOKEN', 'auth_token'),
            'tokens' => env('EHEALTH_OAUTH_TOKENS', '/oauth/tokens'),
            'user' => env('EHEALTH_OAUTH_USER', '/oauth/user'),
            'logout' => env('EHEALTH_OAUTH_LOGOUT', '/auth/logout')
        ],
        'timeout' => 10,
        'queueTimeout' => 60,
        'cooldown' => 300,
        'retries' => 10
    ],

    'capitation_contract_max_period_days' => 366,
    'legal_entity_type' => [
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
    'rate_limit' => [
        'employee_request' => 20
    ],
    'employee_type' => [
        'OWNER' => [
            'position' => [
                'P1', 'P2', 'P3', 'P4', 'P5', 'P6', 'P18', 'P19', 'P22', 'P23', 'P24', 'P25', 'P26', 'P32', 'P229',
                'P230', 'P231', 'P232', 'P233', 'P234', 'P235', 'P236', 'P237', 'P238', 'P239', 'P240', 'P247', 'P249',
                'P257'
            ]
        ],
        'ADMIN' => [
            'position' => [
                ' P5', 'P6', 'P14', 'P18', 'P19'
            ]
        ],
        'HR' => [
            'position' => ['P14']
        ],
        'ASSISTANT' => [
            'position' => [
                'P17', 'P66', 'P169', 'P170', 'P171', 'P173', 'P174', 'P175', 'P176', 'P177', 'P178', 'P179', 'P180',
                'P181', 'P182', 'P183', 'P184', 'P185', 'P186', 'P187', 'P188', 'P189', 'P190', 'P191', 'P192', 'P193',
                'P194', 'P195', 'P196', 'P197', 'P198', 'P199', 'P200', 'P201', 'P202', 'P203', 'P204', 'P205', 'P206',
                'P207', 'P208', 'P209', 'P210', 'P211', 'P212', 'P213', 'P214', 'P215', 'P216', 'P250', 'P251', 'P252',
                'P253', 'P256',
            ],
            'speciality_type' => [
                'ORTHOPEDIC_DENTISTRY', 'X_RAY_RADIOLOGY', 'SANOLOGY', 'STOMATOLOGY', 'GENERAL_MEDICINE',
                'MEDICAL_CASE_EMERGENCY_MEDICINE', 'PUBLIC_HEALTH_AND_PREVENTIVE_MEDICINE'
            ]
        ],
        'DOCTOR' => [
            'position' => ['P7', 'P8', 'P9', 'P10', 'P11'],
            'speciality_type' => ['FAMILY_DOCTOR', 'PEDIATRICIAN', 'THERAPIST']
        ],
        'LABORANT' => [
            'position' => [
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
        'NHS ADMIN' => [
            'position' => [
                'P27', 'P28', 'P29', 'P30', 'P31', 'P237', 'P238', 'P239',
            ],
        ],
        'RECEPTIONIST' => [
            'position' => ['P15']
        ],
        'MED_ADMIN' => [
            'position' => [
                'P5', 'P6', 'P7', 'P8', 'P9', 'P10', 'P11', 'P12', 'P13', 'P33', 'P34', 'P35', 'P36', 'P37', 'P38', 'P39',
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
                'P2', 'P3', 'P4', 'P5', 'P6', 'P23', 'P24', 'P25', 'P26', 'P32', 'P229', 'P230', 'P231', 'P249', 'P257'
            ]
        ]
    ],
    'doctors_type' => [
        'LABORANT', 'DOCTOR', 'MED_ADMIN', 'ASSISTANT', 'MED_COORDINATOR'
    ],
    // https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/583402009/Medical+Events+Dictionaries+and+configurations#legal_entity_encounter_classes
    'legal_entity_encounter_classes' => [
        'PRIMARY_CARE' => ['PHC'],
        'MSP' => ['PHC'],
        'OUTPATIENT' => ['AMB', 'INPATIENT']
    ],
    // https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/583402009/Medical+Events+Dictionaries+and+configurations#employee_encounter_classes
    'employee_encounter_classes' => [
        'DOCTOR' => ['PHC'],
        'SPECIALIST' => ['AMB', 'INPATIENT'],
        'ASSISTANT' => ['PHC', 'AMB', 'INPATIENT'],
        'MED_COORDINATOR' => ['AMB']
    ],
    // https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/583402009/Medical+Events+Dictionaries+and+configurations#encounter_class_encounter_types
    'encounter_class_encounter_types' => [
        'AMB' => ['service_delivery_location', 'virtual', 'patient_identity', 'field', 'home', 'intervention'],
        'INPATIENT' => ['patient_identity', 'discharge', 'service_delivery_location', 'intervention'],
        'PHC' => ['service_delivery_location', 'virtual', 'home', 'field', 'intervention']
    ],
    // https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/583402009/Medical+Events+Dictionaries+and+configurations#legal_entity_%3CLEGAL_ENTITY_TYPE%3E_episode_types
    'legal_entity_episode_types' => [
        'OUTPATIENT' => ['TREATMENT', 'PREVENTION', 'PALLIATIVE_CARE', 'DG', 'REHAB', 'CONDITIONING'],
        'PRIMARY_CARE' => ['TREATMENT', 'PREVENTION', 'PALLIATIVE_CARE', 'PHC'],
        'MSP' => ['TREATMENT', 'PHC', 'PREVENTION', 'PALLIATIVE_CARE'],
        'MSP_PHARMACY' => ['TREATMENT', 'PREVENTION', 'PALLIATIVE_CARE']
    ],
    // https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/583402009/Medical+Events+Dictionaries+and+configurations#employee_%3CEMPLOYEE_TYPE%3E_episode_types
    'employee_episode_types' => [
        'SPECIALIST' => ['TREATMENT', 'PREVENTION', 'PALLIATIVE_CARE', 'DG', 'REHAB', 'CONDITIONING'],
        'DOCTOR' => ['TREATMENT', 'PREVENTION', 'PALLIATIVE_CARE', 'PHC'],
        'ASSISTANT' => ['TREATMENT'],
        'MED_COORDINATOR' => ['TREATMENT', 'DG']
    ],
    // https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/17999298851/RC_+CSI-1323+_Create+Update+person+request+v2#Validate-person-documents
    'expiration_date_exists' => [
        'NATIONAL_ID', 'COMPLEMENTARY_PROTECTION_CERTIFICATE', 'PERMANENT_RESIDENCE_PERMIT', 'REFUGEE_CERTIFICATE',
        'TEMPORARY_CERTIFICATE', 'TEMPORARY_PASSPORT'
    ],
    // https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/17678041168/Observation+dictionaries+and+configurations#observation_categories-vs-observation_codes
    'observation_category_codes' => [
        'exam' => [
            '29463-7', '8302-2', 'sex', 'weight_under_1_year', '8339-4', '8310-5', '21112-8', '56086-2', '80319-7',
            '82810-3', '8462-4', '8480-6', '8867-4', '9279-1'
        ],
        'vital-signs' => [
            'stature', 'eye_colour', 'hair_color', 'hair_length', 'beard', 'mustache', 'peculiarity', '31044-1', '30525-0'
        ],
        'social-history' => ['clothes', '85658-3', 'covid_vac_groups'],
        'survey' => [
            'APGAR_1', 'APGAR_5', '11884-4', '57722-1', '73773-4', '73771-8', '11638-4', '68496-9', '75859-9', '39156-5',
            '38214-3', '38215-0', '913921', 'PPS', '96761-2'
        ],
        'laboratory' => [
            '94762-2', '94558-4', '94500-6', '94562-6', '94564-2', '94563-4', '4548-4', '29572-5', '38473-5', '48633-2',
            '29575-8', '2762-3', '50106-4', '45207-8', '53166-5', '45216-9', '45211-0', '53175-6', '45197-1', '45199-7',
            '45200-3', '53192-1', '53191-3', '53190-5', '45198-9', '50125-4', '50132-0', '50113-0', '53187-1', '29293-8',
            '20661-5', '53160-8', '38481-8', '50157-7', '3077-5', '42906-8', '75217-0', '92002-5', '92006-6', '47679-6',
            '47799-2', '14743-9', '35571-9', '10331-7', '14578-9', '78014-8', '78015-5', '96636-6', '57290-9', '57291-7',
            '77636-9', '96664-8', '57293-3', '78017-1', '57299-0', '73809-6', '73807-0', '73808-8', '35471-2', '34960-5',
            '98007-8', '45153-4', '102113-8', '103154-1', '80698-4', '29770-5', '59822-7', '92728-5', '29247-4', '96462-7',
            '50544-6', '77349-9', '16703-1', '3520-4', '14978-1', '55805-6', '16419-4', '55806-4', '70211-8', '63557-3',
            '5196-1', '22316-4', '13952-7', '42595-9', '29610-3', '5193-8', '10900-9', '22327-1', '13955-0', '11011-4',
            '11259,-9', '63464-2', '22587-0', '7852-7', '22244-8', '30325-5', '7853-5', '13238-1', '49178-7', '8039-0',
            '22580-5', '94819-0', '94309-2', '2947-0', '6298-4', '1996-8', '2069-3', '3040-3', '15074-8', '2885-2',
            '1751-7', '6768-6', '14631-6', '14629-0', '1798-8', '1742-6', '1920-8', '59826-8', '72903-8', '32673-6',
            '2157-6', '42757-5', '2324-2', '1988-5', '14804-9', '14805-6', '2524-7', '33959-8', '33762-6', '48664-7',
            '5902-2', '6302-4', '34714-6', '3173-2', '27811-9', '11558-4', '11557-6', '11556-8', '1959-6', '29590-7',
            '30246-3', '14647-2'
        ],
        'procedure' => ['65897-1', '65893-0'],
        'therapy' => ['65897-1', '65893-0', '74200-7', '87238-2'],
        'imaging' => ['65897-1', '65893-0']
    ],
    // https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/17678041168/Observation+dictionaries+and+configurations#eHealth%2FLOINC%2Fobservation_codes
    'observation_code_values' => [
        'functions' => ['', 'value_codeable_concept', ''],
        'structures' => ['', 'value_codeable_concept', ''],
        'activities' => ['', 'value_codeable_concept', ''],
        'environmental' => ['', 'value_codeable_concept', ''],

        'stature' => ['eHealth/stature', 'value_codeable_concept', ''],
        'eye_colour' => ['eHealth/eye_colour', 'value_codeable_concept', ''],
        'hair_color' => ['eHealth/hair_color', 'value_codeable_concept', ''],
        'hair_length' => ['eHealth/hair_length', 'value_codeable_concept', ''],
        'beard' => ['', 'value_boolean', ''],
        'mustache' => ['', 'value_boolean', ''],
        'clothes' => ['', 'value_string', ''],
        'peculiarity' => ['', 'value_string', ''],
        '31044-1' => ['', 'value_string', ''],
        '29463-7' => ['', 'value_quantity', 'кілограм'],
        '8302-2' => ['', 'value_quantity', 'сантиметр'],
        'sex' => ['GENDER', 'value_codeable_concept', ''],
        '10331-7' => ['eHealth/LOINC/LL360-9', 'value_codeable_concept', ''], // TBD CR-200
        '14578-9' => ['eHealth/LOINC/LL2419-1', 'value_codeable_concept', ''], // TBD CR-200
        '14743-9' => ['', 'value_quantity', 'мілімоль на літр'],
        '39156-5' => ['', 'value_quantity', 'кілограм на метр квадратний'],
        '4548-4' => ['', 'value_quantity', 'відсоток'],
        '56086-2' => ['', 'value_quantity', 'сантиметр'],
//         '80319-7' => ['', 'value_codeable_concept', '], // TBD CR-200
        '82810-3' => ['eHealth/LOINC/LL4129-4', 'value_codeable_concept', ''], // TBD CR-200
        '8310-5' => ['', 'value_quantity', 'градус Цельсія'],
        '8462-4' => ['', 'value_quantity', 'міліметр ртутного стовпчика'],
        '8480-6' => ['', 'value_quantity', 'міліметр ртутного стовпчика'],
        '8867-4' => ['', 'value_quantity', 'удар за хвилину'],
        '9279-1' => ['', 'value_quantity', 'вдих за хвилину'],
        'APGAR_1' => ['0-10', 'value_quantity', 'бал'],
        'APGAR_5' => ['0-10', 'value_quantity', 'бал'],
        '11884-4' => ['', 'value_quantity', 'тиждень'],
        '8339-4' => ['', 'value_quantity', 'грам'],
        '57722-1' => ['', 'value_boolean', ''],
        '73773-4' => ['1-12', 'value_quantity', '# (кількість) - # (кількість)'],
        '73771-8' => ['1-12', 'value_quantity', ''],
        '11638-4' => ['0-20', 'value_quantity', '# (кількість) - # (кількість)'],
        '68496-9' => ['0-20', 'value_quantity', '# (кількість) - # (кількість)'],
        'weight_under_1_year' => ['', 'value_quantity', 'грам'],
        '75859-9' => ['eHealth/rankin_scale', 'value_codeable_concept', ''],
        '94762-2' => ['eHealth/LOINC/LL2009-0', 'value_codeable_concept', ''],
        '94558-4' => ['eHealth/LOINC/LL2021-5', 'value_codeable_concept', ''],
        '94500-6' => ['eHealth/LOINC/LL2021-5', 'value_codeable_concept', ''],
        '94562-6' => ['eHealth/LOINC/LL2009-0', 'value_codeable_concept', ''],
        '94564-2' => ['eHealth/LOINC/LL2009-0', 'value_codeable_concept', ''],
        '94563-4' => ['eHealth/LOINC/LL2009-0', 'value_codeable_concept', ''],
        '85658-3' => ['eHealth/occupation_type', 'value_codeable_concept', ''],
        '65897-1' => ['0-3', 'value_quantity', '# (кількість) - # (кількість)'],
        '65893-0' => ['0-3', 'value_quantity', '# (кількість) - # (кількість)'],
        '30525-0' => ['0-120', 'value_quantity', 'роки'],
        'covid_vac_groups' => ['eHealth/vaccination_covid_groups', 'value_codeable_concept', ''],
        '29572-5' => ['0-50', 'value_quantity', 'мг/дл'],
        '38473-5' => ['0-150', 'value_quantity', 'нг/мл'],
        '48633-2' => ['0-150', 'value_quantity', 'мкг/л'],
        '29575-8' => ['0-15', 'value_quantity', 'мОд/л'],
        '21112-8' => ['', 'value_date_time', ''],
        '2762-3' => ['0-10', 'value_quantity', 'мг/дл'],
        '50106-4' => ['0-1', 'value_quantity', 'мкмоль/л'],
        '45207-8' => ['0-1', 'value_quantity', 'мкмоль/л'],
        '53166-5' => ['0-10', 'value_quantity', 'мкмоль/л'],
        '45216-9' => ['0-10', 'value_quantity', 'мкмоль/л'],
        '45211-0' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '53175-6' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '45197-1' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '45199-7' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '45200-3' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '53192-1' => ['0-1', 'value_quantity', 'мкмоль/л'],
        '53191-3' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '53190-5' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '45198-9' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '50125-4' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '50132-0' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '50113-0' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '53187-1' => ['0-0.5', 'value_quantity', 'мкмоль/л'],
        '29293-8' => ['0-10', 'value_quantity', 'мг/дл'],
        '20661-5' => ['0-300', 'value_quantity', 'мкмоль/л'],
        '53160-8' => ['0-5', 'value_quantity', 'мкмоль/л'],
        '38481-8' => ['0-100', 'value_quantity', 'мкмоль/л'],
        '50157-7' => ['0-50', 'value_quantity', 'мкмоль/л'],
        '3077-5' => ['0-10', 'value_quantity', 'мг/дл'],
        '42906-8' => ['0-100', 'value_quantity', 'нмоль/год/мл'],
        '75217-0' => ['0-0.5', 'value_quantity', 'нмоль/мл/хв'],
        '92002-5' => ['0-100', 'value_quantity', '{Значення_Ct}'],
        '92006-6' => ['0-100', 'value_quantity', '{Значення_Ct}'],
        '47679-6' => ['', 'value_quantity', 'мкмоль/л'],
        '47799-2' => ['', 'value_quantity', 'мкмоль/л'],
        '35571-9' => ['0-1000', 'value_quantity', 'мкмоль/л'],
        '38214-3' => ['1-10', 'value_quantity', 'бал'],
        '38215-0' => ['1-10', 'value_quantity', 'бал'],
        '91392-1' => ['1-10', 'value_quantity', 'бал'],
        '78014-8' => ['', 'value_string', ''],
        '78015-5' => ['', 'value_string', ''],
        '96636-6' => ['', 'value_string', ''],
        '57290-9' => ['', 'value_string', ''],
        '57291-7' => ['', 'value_string', ''],
        '77636-9' => ['', 'value_string', ''],
        '96664-8' => ['', 'value_string', ''],
        '57293-3' => ['', 'value_string', ''],
        '78017-1' => ['', 'value_string', ''],
        '57299-0' => ['', 'value_string', ''],
        '73809-6' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '73807-0' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '73808-8' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '35471-2' => ['eHealth/LOINC/LL360-9', 'value_codeable_concept', ''],
        '34960-5' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '98007-8' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '45153-4' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '102113-8' => ['', 'value_quantity', 'відсоток'],
        '103154-1' => ['', 'value_quantity', 'відсоток'],
        '80698-4' => ['', 'value_quantity', 'відсоток'],
        '29770-5' => ['', 'value_string', ''],
        '59822-7' => ['', 'value_quantity', 'мкг/л'],
        '92728-5' => ['', 'value_quantity', 'мкг/л'],
        '29247-4' => ['', 'value_quantity', 'нг/мл'],
        '96462-7' => ['', 'value_quantity', 'нмоль/л'],
        '50544-6' => ['', 'value_quantity', 'нг/мл'],
        '77349-9' => ['', 'value_quantity', 'нг/мл'],
        '16703-1' => ['', 'value_quantity', 'нг/мл'],
        '3520-4' => ['', 'value_quantity', 'нг/мл'],
        '14978-1' => ['', 'value_quantity', 'мкг/л'],
        '55805-6' => ['', 'value_quantity', 'мкг/л'],
        '16419-4' => ['', 'value_quantity', 'нг/мл'],
        '55806-4' => ['', 'value_quantity', 'мг/л'],
        '70211-8' => ['', 'value_quantity', 'мкмоль/л'],
        '63557-3' => ['', 'value_quantity', '[МО]/л'],
        '5196-1' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '22316-4' => ['', 'value_quantity', '[уО]/мл'],
        '13952-7' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '42595-9' => ['', 'value_quantity', '[МО]/мл'],
        '29610-3' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '5193-8' => ['', 'value_quantity', 'm[МО]/мл'],
        '10900-9' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '22327-1' => ['', 'value_quantity', '[МО]/мл'],
        '13955-0' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '11011-4' => ['', 'value_quantity', '[МО]/мл'],
        '11259-9' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '63464-2' => ['', 'value_quantity', '{значення індексу}'],
        '22587-0' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '7852-7' => ['', 'value_quantity', '[МО]/мл'],
        '22244-8' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '30325-5' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '7853-5' => ['', 'value_quantity', '[МО]/мл'],
        '13238-1' => ['', 'value_quantity', '[МО]/мл'],
        '49178-7' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '8039-0' => ['', 'value_quantity', '[МО]/мл'],
        '22580-5' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '94819-0' => ['', 'value_quantity', '{копій}/мл'],
        '94309-2' => ['eHealth/LOINC/LL3250-9', 'value_codeable_concept', ''],
        '2947-0' => ['', 'value_quantity', 'мілімоль на літр'],
        '6298-4' => ['', 'value_quantity', 'мілімоль на літр'],
        '1996-8' => ['', 'value_quantity', 'мілімоль на літр'],
        '2069-3' => ['', 'value_quantity', 'мілімоль на літр'],
        '3040-3' => ['', 'value_quantity', 'О/л'],
        '15074-8' => ['', 'value_quantity', 'мілімоль на літр'],
        '2885-2' => ['', 'value_quantity', 'г/дл'],
        '1751-7' => ['', 'value_quantity', 'г/дл'],
        '6768-6' => ['', 'value_quantity', 'О/л'],
        '14631-6' => ['', 'value_quantity', 'мкмоль/л'],
        '14629-0' => ['', 'value_quantity', 'мкмоль/л'],
        '1798-8' => ['', 'value_quantity', 'О/л'],
        '1742-6' => ['', 'value_quantity', 'О/л'],
        '1920-8' => ['', 'value_quantity', 'О/л'],
        '59826-8' => ['', 'value_quantity', 'мкмоль/л'],
        '72903-8' => ['', 'value_quantity', 'мкмоль/л'],
        '32673-6' => ['', 'value_quantity', 'О/л'],
        '2157-6' => ['', 'value_quantity', 'О/л'],
        '42757-5' => ['', 'value_quantity', 'нг/мл'],
        '2324-2' => ['', 'value_quantity', 'О/л'],
        '1988-5' => ['', 'value_quantity', 'мг/л'],
        '14804-9' => ['', 'value_quantity', 'О/л'],
        '14805-6' => ['', 'value_quantity', 'О/л'],
        '2524-7' => ['', 'value_quantity', 'мілімоль на літр'],
        '33959-8' => ['', 'value_quantity', 'нг/мл'],
        '33762-6' => ['', 'value_quantity', 'пг/мл'],
        '48664-7' => ['', 'value_quantity', 'г/л'], // деактивовано
        '5902-2' => ['', 'value_quantity', 'секунда'],
        '6302-4' => ['', 'value_quantity', 'відсоток'],
        '34714-6' => ['', 'value_quantity', '{МНВ}'],
        '3173-2' => ['', 'value_quantity', 'секунда'],
        '27811-9' => ['', 'value_quantity', 'відсоток'],
        '11558-4' => ['', 'value_quantity', '[pH]'],
        '11557-6' => ['', 'value_quantity', 'міліметр ртутного стовпчика'],
        '11556-8' => ['', 'value_quantity', 'міліметр ртутного стовпчика'],
        '1959-6' => ['', 'value_quantity', 'мілімоль на літр'],
        '29590-7' => ['', 'value_quantity', 'пг/мл'],
        '30246-3' => ['eHealth/LOINC/LL2451-4', 'value_codeable_concept', ''],
        '14647-2' => ['', 'value_quantity', 'мілімоль на літр'],
        'PPS' => ['0-100', 'value_quantity', 'відсоток'],
        '74200-7' => ['', 'value_quantity', 'доба'],
        '87238-2' => ['', 'value_string', ''],
        '96761-2' => ['0-100', 'value_quantity', 'бал']
    ],

    // Set the test environment
    'test' => [
        'client_id' => env('TEST_CLIENT_ID'),
        'client_secret' => env('TEST_CLIENT_SECRET')
    ],
];
