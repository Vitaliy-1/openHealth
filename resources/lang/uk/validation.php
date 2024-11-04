<?php

return [

    /*
 |--------------------------------------------------------------------------
 | Мовні ресурси перевірки введення
 |--------------------------------------------------------------------------
 |
 | Наступні ресурси містять стандартні повідомлення перевірки коректності
 | введення даних. Деякі з цих правил мають декілька варіантів, як,
 | наприклад, size. Ви можете змінити будь-яке з цих повідомлень.
 |
 */

    'accepted' => 'Ви повинні прийняти :attribute.',
    'active_url' => 'Поле :attribute не є правильним URL.',
    'after' => 'Поле :attribute має містити дату не раніше :date.',
    'after_or_equal' => 'Поле :attribute має містити дату не раніше або дорівнюватися :date.',
    'alpha' => 'Поле :attribute має містити лише літери.',
    'alpha_dash' => 'Поле :attribute має містити лише літери, цифри та підкреслення.',
    'alpha_num' => 'Поле :attribute має містити лише літери та цифри.',
    'array' => 'Поле :attribute має бути масивом.',
    'before' => 'Поле :attribute має містити дату не пізніше :date.',
    'before_or_equal' => 'Поле :attribute має містити дату не пізніше або дорівнюватися :date.',
    'between' => [
        'numeric' => 'Поле :attribute має бути між :min та :max.',
        'file' => 'Розмір файлу в полі :attribute має бути не менше :min та не більше :max кілобайт.',
        'string' => 'Текст в полі :attribute має бути не менше :min та не більше :max символів.',
        'array' => 'Поле :attribute має містити від :min до :max елементів.',
    ],
    'boolean' => 'Поле :attribute повинне містити логічний тип.',
    'confirmed' => 'Поле :attribute не збігається з підтвердженням.',
    'date' => 'Поле :attribute не є датою.',
    'date_format' => 'Поле :attribute не відповідає формату :format.',
    'different' => 'Поля :attribute та :other повинні бути різними.',
    'digits' => 'Довжина цифрового поля :attribute повинна дорівнювати :digits.',
    'digits_between' => 'Довжина цифрового поля :attribute повинна бути від :min до :max.',
    'dimensions' => 'Поле :attribute містіть неприпустимі розміри зображення.',
    'distinct' => 'Поле :attribute містить значення, яке дублюється.',
    'email' => 'Поле :attribute повинне містити коректну електронну адресу.',
    'file' => 'Поле :attribute має містити файл.',
    'filled' => "Поле :attribute є обов'язковим для заповнення.",
    'exists' => 'Вибране для :attribute значення не коректне.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'Поле :attribute має містити зображення.',
    'in' => 'Вибране для :attribute значення не коректне.',
    'in_array' => 'Значення поля :attribute не міститься в :other.',
    'integer' => 'Поле :attribute має містити ціле число.',
    'ip' => 'Поле :attribute має містити IP адресу.',
    'ipv4' => 'Поле :attribute має містити IPv4 адресу.',
    'ipv6' => 'Поле :attribute має містити IPv6 адресу.',
    'json' => 'Дані поля :attribute мають бути в форматі JSON.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'Поле :attribute має бути не більше :max.',
        'file' => 'Файл в полі :attribute має бути не більше :max кілобайт.',
        'string' => 'Текст в полі :attribute повинен мати довжину не більшу за :max.',
        'array' => 'Поле :attribute повинне містити не більше :max елементів.',
    ],
    'mimes' => 'Поле :attribute повинне містити файл одного з типів: :values.',
    'mimetypes' => 'Поле :attribute повинне містити файл одного з типів: :values.',
    'min' => [
        'numeric' => 'Поле :attribute повинне бути не менше :min.',
        'file' => 'Розмір файлу в полі :attribute має бути не меншим :min кілобайт.',
        'string' => 'Текст в полі :attribute повинен містити не менше :min символів.',
        'array' => 'Поле :attribute повинне містити не менше :min елементів.',
    ],
    'not_in' => 'Вибране для :attribute значення не коректне.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'Поле :attribute повинно містити число.',
    'phone' => 'Поле :attribute має бути дійсним номером телефону з мінімум :min цифрами, без пробілів та крапок.',
    'present' => 'Поле :attribute повинне бути присутнє.',
    'regex' => 'Поле :attribute має хибний формат.',
    'required' => "Поле :attribute є обов'язковим для заповнення.",
    'required_if' => "Поле :attribute є обов'язковим для заповнення, коли :other є рівним :value.",
    'required_unless' => "Поле :attribute є обов'язковим для заповнення, коли :other відрізняється від :values",
    'required_with' => "Поле :attribute є обов'язковим для заповнення, коли :values вказано.",
    'required_with_all' => "Поле :attribute є обов'язковим для заповнення, коли :values вказано.",
    'required_without' => "Поле :attribute є обов'язковим для заповнення, коли :values не вказано.",
    'required_without_all' => "Поле :attribute є обов'язковим для заповнення, коли :values не вказано.",
    'same' => 'Поля :attribute та :other мають співпадати.',
    'size' => [
        'numeric' => 'Поле :attribute має бути довжини :size.',
        'file' => 'Файл в полі :attribute має бути розміром :size кілобайт.',
        'string' => 'Текст в полі :attribute повинен містити :size символів.',
        'array' => 'Поле :attribute повинне містити :size елементів.',
    ],
    'string' => 'Поле :attribute повинне містити текст.',
    'timezone' => 'Поле :attribute повинне містити коректну часову зону.',
    'unique' => 'Таке значення поля :attribute вже існує.',
    'uploaded' => 'Завантаження поля :attribute не вдалося.',
    'url' => 'Формат поля :attribute неправильний.',

    /*
    |--------------------------------------------------------------------------
    | Додаткові ресурси для перевірки введення
    |--------------------------------------------------------------------------
    |
    | Тут Ви можете вказати власні ресурси для підтвердження введення,
    | використовуючи формат "attribute.rule", щоб дати назву текстовим змінним.
    | Так ви зможете легко додати текст повідомлення для заданого атрибуту.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
            'first_name' => 'Прізвище',
        ],
        ':attribute.required' => 'Поле :attribute є обов\'язковим для заповнення.',
        'role_table' => 'Заповніть таблицю Ролі',
        'documents_empty' => 'Заповніть таблицю Документи Паспорт або Номер РНОКПП',
        'educations_table' => 'Заповніть таблицю Освіта',
        'specialities_table' => 'Заповніть таблицю Спеціальності',
        'employee_table' => 'Заповніть данні Працівника',
        '_token' => [
            'csrf_token' => 'Токен CSRF є недійсним.',
        ],
        'edrpou_differ' => 'Код ЄДРПОУ сертифіката відрізняється від вказаного',
    ],

    /*
    |--------------------------------------------------------------------------
    | Власні назви атрибутів
    |--------------------------------------------------------------------------
    |
    | Наступні правила дозволяють налаштувати заміну назв полів введення
    | для зручності користувачів. Наприклад, вказати "Електронна адреса" замість
    | "email".
    |
    */

    'attributes' => [
        'name' => 'ім\'я',
        'phone' => 'телефон',
        'password' => 'пароль',
        'keyContainerUpload' => 'контейнер ключа',
        'knedp' => 'КНЕДП',
        '_token' => 'токен CSRF',
        'edrpou' => 'ЄДРПОУ',
        'email' => 'E-mail',
        'contact.phones.*.number' => 'Телефон',
        'contact.phones.*.type' => 'Тип Номера',
        'contact.email' => 'E-mail',
        '*.type' => 'Тип спеціальності',
        'type' => 'Тип спеціальності',
        'owner' => [
            'first_name' => 'Ім’я',
            'last_name' => 'Прізвище',
            'second_name' => 'По батькові',
            'birth_date' => 'Дата народження',
            'email' => 'E-mail',
            'gender' => 'Стать',
            'position' => 'Посада керівника НМП',
            'tax_id' => 'РНОКПП',
            'documents' => [
                'type' => 'Тип документа',
                'number' => 'Cерія/номер документа',
            ],

        ],
        'employee' => [
            'first_name' => 'Ім’я',
            'last_name' => 'Прізвище',
            'second_name' => 'По батькові',
            'birth_date' => 'Дата народження',
            'email' => 'E-mail',
            'gender' => 'Стать',
            'position' => 'Посада керівника НМП',
            'tax_id' => 'РНОКПП',
            'employee_type' => 'Роль',
            'documents' => [
                'type' => 'Тип документа',
                'number' => 'Cерія/номер документа',
            ],

        ],
        'patient' => [
            'first_name' => 'ім’я',
            'last_name' => 'прізвище',
            'second_name' => 'по батькові',
            'birth_date' => 'дата народження',
            'country_of_birth' => 'країна народження',
            'city_of_birth' => 'місто народження',
            'gender' => 'стать',
        ],
        'identity' => [
            'tax_id' => 'номер РНОКПП',
        ],
        'contact_data' => [
            'email' => 'E-mail',
            'secret' => 'кодове слово',
        ],
        'contact_data.phones.*.type' => 'тип телефону',
        'contact_data.phones.*.number' => 'номер телефону',
        'emergency_contact' => [
            'first_name' => 'ім’я',
            'last_name' => 'прізвище',
            'second_name' => 'по батькові',
        ],
        'patient.emergency_contact.phones.*.type' => 'тип телефону',
        'patient.emergency_contact.phones.*.number' => 'номер телефону',
        'address' => [
            'region' => 'область',
            'city' => 'місто',
            'street_type' => 'тип вулиці',
            'street_name' => 'назва вулиці',
            'building' => 'будинок',
            'apartment' => 'квартира',
            'zip_code' => 'поштовий індекс',
        ],
        'legal_representative' => [
            'first_name' => 'ім’я',
            'last_name' => 'прізвище',
            'second_name' => 'по батькові',
            'birth_date' => 'дата народження',
            'country_of_birth' => 'країна народження',
            'city_of_birth' => 'місто народження',
        ],
        'person_documents' => [
            'type' => 'тип документа',
            'number' => 'серія/номер документа',
            'issued_by' => 'орган яким виданий документ',
            'issued_at' => 'дата видачі документа',
            'valid_to' => 'дійсний до',
        ],
        'legal_representation_documents' => [
            'type' => 'тип документа',
            'number' => 'серія/номер документа',
            'issued_by' => 'орган яким виданий документ',
            'issued_at' => 'дата видачі документа',
        ],
        'legal_representation_contact.phones.*.type' => 'тип телефону',
        'legal_representation_contact.phones.*.number' => 'номер телефону',
        'legal_representation_contact' => [
            'email' => 'E-mail',
        ],
        'documents' => [
            'type' => 'Тип документа',
            'number' => 'Серія/номер документа',
            'issued_by' => 'Орган яким виданий документ',
            'issued_at' => 'Дата видачі документа',
            'valid_to' => 'дійсний до',
        ],
        'passport_data' => [
            'first_name' => 'Ім’я',
            'last_name' => 'Прізвище',
            'second_name' => 'По батькові',
            'birth_date' => 'Дата народження',
            'email' => 'E-mail',
            'gender' => 'Стать',
            'position' => 'Посада керівника НМП',
            'tax_id' => 'РНОКПП',
            'documents' => [
                'type' => 'Тип документа',
                'number' => 'Cерія/номер документа',
            ],

        ],
        'owner.phones.*.number' => 'елефон',
        'owner.phones.*.type' => 'Тип Номера',
        'country' => 'Країна',
        'region' => 'Область',
        'area' => 'Район',
        'settlement' => 'Населений пункт',
        'settlement_type' => 'Тип населеного пункту',
        'street_type' => 'Тип вулиці',
        'street' => 'Вулиця',
        'building' => 'Будинок',
        'apartment' => 'Квартира',
        'zip_code' => 'Поштовий індекс',
        'location' => [
            'latitude' => 'Широта',
            'longitude' => 'Довгота',
        ],
        'division' => [
            'name' => 'Назва',
            'type' => 'Тип',
            'email' => 'E-mail',
            'phones.number' => 'Телефон',
            'phones.type' => 'Тип Номера',
            'location.latitude' => 'Широта',
            'location.longitude' => 'Довгота',
        ],
        'division.phones.*.number' => 'Телефон',
        'division.phones.*.type' => 'Тип Номера',
        'division.location.latitude' => 'Широта',
        'division.location.longitude' => 'Довгота',
        'healthcare_service' => [
            'category' => 'Категорія',
            'conditions' => 'Умови надання',
            'speciality_type' => 'Тип спеціальності',
            'status' => 'Статус',
            'type' => 'Тип спеціальності',
            'providing_condition' => 'Умови надання послуг',
        ],
        'license' => [
            'license_type' => 'Тип',
            'issued_by' => 'Орган яким виданий документ',
            'issued_date' => 'Дата видачі документа',
            'order_no' => 'Номер  наказу ',
            'license_number' => 'Номер ліцензії',
            'active_from_date' => 'Дата початку дії ліцензії',
        ],
        'educations' => [
            'degree' => 'Ступінь',
            'speciality' => 'Спеціальність',
            'institution_name' => 'Назва закладу',
            'country' => 'Країна',
            'city' => 'Місто',
            'institution_type' => 'Тип закладу',
            'speciality_type' => 'Тип спеціальності',
            'institute_type' => 'Тип закладу',
            'speciality_level' => 'Рівень спеціальності',
            'diploma_number' => 'Номер диплому',
        ],
        'contract_type' => 'Тип договору',
        'contractor_payment_details' => [
            'mfo' => 'МФО',
            'bank_name' => 'Назва банку',
            'payer_account' => 'IBAN',
        ],
        'start_date' => 'Дата початку',
        'end_date' => 'Дата завершення',
        'status' => 'Статус',
        'contractor_rmsp_amount' => 'Кількість населення',
        'contractor_base' => 'На якій підставі діє підписант',
        'statute_md5' => 'Статут',
        'additional_document_md5' => 'Додатковий документ',
        'contractor_divisions' => 'Місця надання послуг',
        'external_contractors' => [
            'contract' => [
                'number' => 'Номер договору з субпідрядником',
                'issued_at' => 'Дата початку договору',
                'expires_at' => 'Дата закінчення договору',

            ],
            'legal_entity' => [
                'name' => 'Медична організація',

            ],
            'divisions' => [
                'name' => 'Назва Підрозділу',
                'medical_service' => 'Медична послуга'
            ]

        ],
        //! Licence
        'issued_by' => 'ким видано ліцензію',
        'issued_date' => 'дата видачі ліцензії',
        'active_from_date' => 'дата початку дії ліцензії',
        'order_no' => 'номер наказу',
        'expiry_date' => 'дата завершення дії ліцензії',
        'what_licensed' => 'напрям діяльності, що ліцензовано',
    ],
    'consent_text' => 'Я погоджуюсь з умовами'
    //
];
