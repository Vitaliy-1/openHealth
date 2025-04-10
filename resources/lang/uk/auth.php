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
    'throttle' => 'Забагато спроб логіну. Будь ласка, спробуйте пізніше через :seconds секунд.',
    'login' => [
        'success' => [
            'user_auth' => 'AUTH: Успішний вхід',
            'new_user_auth' => 'AUTH: Новий користувач успішно аутентифікований'
        ],
        'error' => [
            'server' => [
                'response' => 'AUTH: Помилка при обробці відповіді від сервера',
                'user_credentials' => 'ЕСОЗ: помилка в облікових даних користувача. Зверніться до адміністратора',
            ],
            'validation' => [
                'auth' => 'Auth Response Schema:',
                'user_details' => 'User Details Response Schema:',
                'employee_data' => 'Employee Data Response Schema:'
            ],
            'email_verification' => 'Ваш email не підтверджено. Перевірте свою електронну пошту!',
            'common' => 'AUTH: Загальна помилка',
            'unexpected' => 'AUTH: Невідома помилка',
            'user_identity' => 'AUTH: Помилка ідентифкації користувача',
            'unexistent_legal_entity' => 'AUTH: Неіснуючий Legal Entity',
            'legal_entity_identity' => 'AUTH: Помилка ідентифікації закладу',
            'user_authentication' => 'AUTH: Помилка аутентифікації користувача',
            'data_saving' => 'AUTH: Сталася помилка під час збереження автентифікаційних даних',
            'employee_instance' => 'Не знайдено даних співробітника для автентифікованого користувача',
            'get_employee_instance' => 'Неможливо отримати екземпляр Employee чи EmployeeRequest'
        ]
    ]
];
