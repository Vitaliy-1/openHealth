<?php

return [
    'api' => [
        'domain' => env('EHEALTH_API_URL', 'private-anon-cb2ce4f7fc-uaehealthapi.apiary-mock.com'),
        'token' => env('EHEALTH_X_CUSTOM_PSK', 'X-Custom-PSK'),
        'timeout' => 10,
        'queueTimeout' => 60,
        'cooldown' => 300,
        'retries' => 10
    ],

    'capitation_contract_max_period_days' => 366,



];
