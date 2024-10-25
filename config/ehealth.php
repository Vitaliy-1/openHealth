<?php

return [
    'api' => [
        'domain' => env('EHEALTH_API_URL', 'private-anon-cb2ce4f7fc-uaehealthapi.apiary-mock.com'),
        'token' => env('EHEALTH_X_CUSTOM_PSK', 'X-Custom-PSK'),
        'api_key' => env('EHEALTH_API_KEY', ''),
        'callback_prod' => env('EHEALTH_CALLBACK_PROD', true),
        'auth_host' => env('EHEALTH_AUTH_HOST', 'https://auth-preprod.ehealth.gov.ua/sign-in'),
        'redirect_uri' => env('EHEALTH_REDIRECT_URI', 'https://openhealths.com/ehealth/oauth'),
        'url_dev' => env('EHEALTH_URL_DEV', 'http://localhost'),
        'timeout' => 10,
        'queueTimeout' => 60,
        'cooldown' => 300,
        'retries' => 10
    ],

    'capitation_contract_max_period_days' => 366,



];
