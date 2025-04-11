<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'login' => [
        'success' => [
            'user_auth' => 'AUTH: Login successful',
            'new_user_auth' => 'AUTH: New user successfully authenticated'
        ],
        'error' => [
            'server' => [
                'response' => 'AUTH: Failed to process the server response',
                'user_credentials' => 'ESOZ: error in user credentials. Please contact the administrator',
            ],
            'email_verification' => 'Your email has not been verified. Please check your email!',
            'common' => 'AUTH: Common error',
            'user_identity' => 'AUTH: User identity error',
            'legal_entity_identity' => 'AUTH: Legal entity identity error',
            'user_authentication' => 'AUTH: User authentication error',
            'data_saving' => 'AUTH: An error occurred while saving authentication data'
        ]
    ],
    
];
