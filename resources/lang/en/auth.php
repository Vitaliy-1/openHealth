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
    'throttle' => 'Too many login attempts. Please try again in :seconds sec',
    'login' => [
        'success' => [
            'user_auth' => 'AUTH: Login successful',
            'new_user_create' => 'User has been successfully created',
            'new_user_auth' => 'AUTH: New user successfully authenticated',
            'verification' => 'User\'s Email has been successfully verified',
            'reset_link' => 'A password reset link has been sent to the specified email'
        ],
        'error' => [
            'server' => [
                'response' => 'AUTH: Failed to process the server response',
                'user_credentials' => 'ESOZ: error in user credentials. Please contact the administrator',
            ],
            'validation' => [
                'auth' => 'Auth Response Schema: wrong data received from the auth server',
                'user_details' => 'User Details Response Schema:',
                'employee_data' => 'Employee Data Response Schema:',
                'employee_request_data' => 'EmployeeRequest Data Response Schema:',
                'auths' => 'Authentication error',
                'credentials' => 'НWrong email or password',
                'confirm_mismatch' => 'Password mismatch'
            ],
            'legal_entity' => [
                'auth_need' => 'Need authorize for access',
                'invalid_session' => 'Invalid session! Please relogin',
                'data_problem' => 'Data problem! Please relogin',
                'wrong_rights' => 'You access rights has changed. Please relogin',
                'wrong_request' => 'No acceptable Legal Entity found for Your account after login'
            ],
            'lockout' => 'User locked out [too many login attempts]',
            'exceed_login_attempts' => 'Exceed login attempts',
            'email_verification' => 'Your email has not been verified. Please check your email!',
            'common' => 'AUTH: Common error',
            'unexpected' => 'AUTH: Unexpected error',
            'user_identity' => 'AUTH: User identity error',
            'legal_entity_identity' => 'AUTH: Legal entity identity error',
            'unexistent_legal_entity' => 'AUTH: Unexistent Legal Entity',
            'user_authentication' => 'AUTH: User authentication error',
            'user_employee_update' => 'AUTH: User employee data fail update',
            'user_test_email' => 'AUTH: User test email entered during login is different from eHealth',
            'data_saving' => 'AUTH: An error occurred while saving authentication data',
            'employee_instance' => 'Not found any employee data for authenticated user',
            'get_employee_instance' => 'Cannot get any Employee or EmployeeRequest Instance',
            'email_already_verified' => 'Email already verified',
            'reset_password' => 'Error resetting password',
            'throttle' => 'Too many attempts. Please, try again later',
            'wrong_session' => 'Wrong session',
            'reset_link' => 'Cannot send reset link'
        ],

        'no_ehealth_login' => 'Without eHealth authentication',
        'forgot_password' => 'Forgot password?',
        'vlink_sent' => 'Verification link has been sent successfully'
    ]
];
