<?php

return [
    'api' => [
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
    'legal_entity_type' => [
        'primary_care' => [
            'roles'     => ['OWNER', 'ADMIN', 'DOCTOR', 'HR'],
            'positions' => [
                "P3", "P274", "P93", "P202", "P215", "P159", "P118", "P46", "P54", "P99", "P109", "P96", "P245", "P279",
                "P63", "P123", "P17", "P62", "P45", "P10", "P74", "P37", "P114", "P127", "P214", "P179", "P156", "P145",
                "P103", "P115", "P126", "P120", "P268", "P110", "P43", "P130", "P203", "P81", "P273", "P95", "P191",
                "P42",
                "P38", "P105", "P23", "P197", "P154", "P65", "P58", "P175", "P61", "P98", "P13", "P177", "P173", "P72",
                "P256", "P178", "P153", "P212", "P53", "P48", "P7", "P106", "P122", "P52", "P158", "P15", "P22", "P39",
                "P92", "P112", "P71", "P164", "P170", "P266", "P224", "P270", "P78", "P242", "P160", "P2", "P213",
                "P152",
                "P26", "P247", "P192", "P36", "P67", "P181", "P124", "P73", "P228", "P55", "P117", "P249", "P91", "P70",
                "P231", "P229", "P97", "P167", "P169", "P238", "P149", "P150", "P128", "P64", "P51", "P83", "P44",
                "P241",
                "P4", "P50", "P250", "P116", "P185", "P276", "P76", "P40", "P69", "P84", "P82", "P176", "P174", "P278",
                "P155", "P9", "P257", "P29", "P252", "P243", "P24", "P180", "P166", "P201", "P16", "P200", "P210",
                "P34",
                "P272", "P168", "P275", "P194", "P165", "P146", "P151", "P111", "P85", "P265", "P87", "P246", "P6",
                "P77",
                "P41", "P204", "P94", "P240", "P79", "P14", "P216", "P32", "P59", "P230", "P1", "P88", "P248", "P172",
                "P75", "P113", "P196", "P28", "P129", "P206", "P57", "P162", "P35", "P107", "P184", "P68", "P131",
                "P189",
                "P211", "P60", "P25", "P56", "P161", "P5", "P89", "P188", "P183", "P100", "P47", "P269", "P66", "P8",
                "P207", "P255", "P119", "P90", "P86", "P27", "P199", "P108", "P163", "P157", "P277", "P11"
            ],
        ],
    ],
    'rate_limit' =>[
        'employee_request' => 20
    ]
];
