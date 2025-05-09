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
            'validation' => [
                'auth' => 'Auth Response Schema:',
                'user_details' => 'User Details Response Schema:',
                'employee_data' => 'Employee Data Response Schema:'
            ],
            'email_verification' => 'Your email has not been verified. Please check your email!',
            'common' => 'AUTH: Common error',
            'unexpected' => 'AUTH: Unexpected error',
            'user_identity' => 'AUTH: User identity error',
            'legal_entity_identity' => 'AUTH: Legal entity identity error',
            'unexistent_legal_entity' => 'AUTH: Unexistent Legal Entity',
            'user_authentication' => 'AUTH: User authentication error',
            'data_saving' => 'AUTH: An error occurred while saving authentication data',
            'employee_instance' => 'Not found any employee data for authenticated user',
            'get_employee_instance' => 'Cannot get any Employee or EmployeeRequest Instance'
        ]
    ]
];
