<?php

declare(strict_types=1);

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

    'failed' => 'Ці облікові дані не збігаються із нашими записами.',
    'password' => 'Невірно вказаний пароль.',
    'throttle' => 'Забагато спроб логіну. Будь ласка, спробуйте пізніше через :seconds сек.',
    'login' => [
        'success' => [
            'user_auth' => 'AUTH: Успішний вхід',
            'new_user_create' => 'Нового користувача успішно створено',
            'new_user_auth' => 'AUTH: Новий користувач успішно аутентифікований',
            'verification' => 'Email користувача успішно верифіковано',
            'reset_link' => 'Посилання на скидання пароля надіслано на вказаний email'
        ],
        'error' => [
            'server' => [
                'response' => 'AUTH: Помилка при обробці відповіді від сервера',
                'user_credentials' => 'ЕСОЗ: помилка в облікових даних користувача. Зверніться до адміністратора',
            ],
            'validation' => [
                'auth' => 'Auth Response Schema: неповні або хибні дані у відповіді від сервера',
                'user_details' => 'User Details Response Schema:',
                'employee_data' => 'Employee Data Response Schema:',
                'employee_request_data' => 'EmployeeRequest Data Response Schema:',
                'auths' => 'Помилка автентифікації',
                'credentials' => 'Невірний логін або пароль',
                'confirm_mismatch' => 'Паролі не збігаються'
            ],
            'legal_entity' => [
                'auth_need' => 'Необхідна авторизація для доступу',
                'invalid_session' => 'Сесія не дійсна! Будь-ласка увійдіть знову',
                'data_problem' => 'Виникла проблема з даними! Будь-ласка увійдіть знову',
                'wrong_rights' => 'Ваші права доступу змінилися. Будь ласка, увійдіть знову',
                'wrong_request' => 'Для вашого облікового запису не визначено доступної юридичної особи після входу'
            ],
            'vlink_not_sent' => 'Не вдалося надіслати посилання на верифікацію',
            'lockout' => 'Користувача заблоковано [забагато спроб логіну]',
            'exceed_login_attempts' => 'Перевищено кількість спроб входу',
            'email_verification' => 'Ваш email не підтверджено. Перевірте свою електронну пошту!',
            'common' => 'AUTH: Загальна помилка',
            'unexpected' => 'AUTH: Невідома помилка',
            'user_identity' => 'AUTH: Помилка ідентифкації користувача',
            'unexistent_legal_entity' => 'AUTH: Неіснуючий Legal Entity',
            'legal_entity_identity' => 'AUTH: Помилка ідентифікації закладу',
            'user_authentication' => 'AUTH: Помилка аутентифікації користувача',
            'user_employee_update' => 'AUTH: Помилка під час оновлення даних співробітника',
            'user_test_email' => 'AUTH: тестовий імейл користувача, введений під час входу, відрізняється від такого в eHealth',
            'data_saving' => 'AUTH: Сталася помилка під час збереження автентифікаційних даних',
            'employee_instance' => 'Не знайдено даних співробітника для автентифікованого користувача',
            'get_employee_instance' => 'Неможливо отримати екземпляр Employee чи EmployeeRequest',
            'email_already_verified' => 'Вказаний Email вже верифікоавно',
            'reset_password' => 'Помилка при встановленні нового пароля',
            'throttle' => 'Забагато спроб. Будь ласка, спробуйте пізніше',
            'wrong_session' => 'Сесія недійсна',
            'reset_link' => 'Неможливо надіслати посилання'
        ],
        'no_ehealth_login' => 'Без авторизації у eHealth',
        'forgot_password' => 'Забули свій пароль?',
        'vlink_sent' => 'Посилання на верифікацію надіслано успішно',
    ]
];
