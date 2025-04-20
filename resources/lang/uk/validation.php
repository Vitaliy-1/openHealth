<?php

declare(strict_types=1);

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
    'activeUrl' => 'Поле :attribute не є правильним URL.',
    'after' => 'Поле :attribute має містити дату не раніше :date.',
    'afterOrEqual' => 'Поле :attribute має містити дату не раніше або дорівнюватися :date.',
    'alpha' => 'Поле :attribute має містити лише літери.',
    'alphaDash' => 'Поле :attribute має містити лише літери, цифри та підкреслення.',
    'alphaNum' => 'Поле :attribute має містити лише літери та цифри.',
    'array' => 'Поле :attribute має бути масивом.',
    'before' => 'Поле :attribute має містити дату не пізніше :date.',
    'between' => [
        'numeric' => 'Поле :attribute має бути між :min та :max.',
        'file' => 'Розмір файлу в полі :attribute має бути не менше :min та не більше :max кілобайт.',
        'string' => 'Текст в полі :attribute має бути не менше :min та не більше :max символів.',
        'array' => 'Поле :attribute має містити від :min до :max елементів.',
    ],
    'boolean' => 'Поле :attribute повинне містити логічний тип.',
    'confirmed' => 'Поле :attribute не збігається з підтвердженням.',
    'date' => 'Поле :attribute не є датою.',
    'dateFormat' => 'Поле :attribute не відповідає формату :format.',
    "declined_if" => "Це значення має бути відхилено, якщо :other є :value.",
    'different' => 'Поля :attribute та :other повинні бути різними.',
    'digits' => 'Довжина цифрового поля :attribute повинна дорівнювати :digits.',
    'dimensions' => 'Поле :attribute містить неприпустимі розміри зображення.',
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
    'inArray' => 'Значення поля :attribute не міститься в :other.',
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
    'numeric' => 'Поле :attribute повинно містити число.',
    'phone' => 'Поле :attribute має бути дійсним номером телефону з мінімум :min цифрами, без пробілів та крапок, наприклад: +380555555555.',
    'present' => 'Поле :attribute повинне бути присутнє.',
    'regex' => 'Поле :attribute має хибний формат.',
    'required' => "Поле :attribute є обов'язковим для заповнення.",
    'required_if' => "Поле :attribute є обов'язковим для заповнення, коли :other є рівним :value.",
    'prohibited_if' => 'Поле :attribute заборонено, якщо :other дорівнює :value.',
    'prohibited_unless' => "Поле :attribute заборонено, якщо :other не є одним із значень: :values.",
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
    'uuid' => 'Поле :attribute повинно містити коректний UUID.',

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
            'firstName' => 'Прізвище',
        ],
        ':attribute.required' => 'Поле :attribute є обов\'язковим для заповнення.',
        'roleTable' => 'Заповніть таблицю Ролі',
        'documentsEmpty' => 'Заповніть таблицю Документи Паспорт або Номер РНОКПП',
        'educationsTable' => 'Заповніть таблицю Освіта',
        'specialitiesTable' => 'Заповніть таблицю Спеціальності',
        'employeeTable' => 'Заповніть данні Працівника',
        'Token' => [
            'csrfToken' => 'Токен CSRF є недійсним.',
        ],
        'cipher' => [
            'initiatorDifferBusiness' => 'Завантажений ключ електронного підпису не є ключем юридичної організації чи ФОП ',
            'initiatorDifferPrerson' => 'Завантажений ключ електронного підпису не є ключем фізичної особи',
            'edrpouDiffer' => 'ЄДРПОУ ключа електронного підпису відрізняється від вказаного',
            'drfouDiffer' => 'ІПН ключа електронного підпису відрізняється від вказаного',
            'kepTimeExpired' => 'Термін дії ключа електронного підпису закінчився',
            'kepNotValid' => 'Завантажений ключ не може використовуватись для електронного підпису'
        ],
        'patient' => [
            'confidantPersonRequiredForChildren' => "Довірена особа є обов'язковою для дітей.",
            'confidantPersonRequiredForMinor' => "Довірена особа є обов'язковою для неповнолітніх пацієнтів.",
            'confidantPersonMustBeCapable' => 'Довіреною особою не може бути особа, яка має документ, що підтверджує її дієздатність.',
            'birthDocumentsRequired' => 'Документи повинні містити один з наступних документів: СВІДОЦТВО ПРО НАРОДЖЕННЯ, ЗАКОРДОННЕ СВІДОЦТВО ПРО НАРОДЖЕННЯ.',
            'personalDocumentsRequired' => 'Необхідно подати документ, що підтверджує персональні дані.'
        ]
    ],

    'employee' => [
        'birth_date_iso' => 'Дата народження має бути в форматі ISO 8601',
        'party' => [
            'birth_date_value' => 'Дата народження має бути пізніше 1900-01-01',
        ]
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
        'keyContainerUpload' => 'контейнер ключа',
        'knedp' => 'КНЕДП',
        'Token' => 'токен CSRF',
        'edrpou' => 'ЄДРПОУ',
        'email' => 'E-mail',
        'contact.phones.*.number' => 'Телефон',
        'contact.phones.*.type' => 'Тип Номера',
        'contact.email' => 'E-mail',
        '*.type' => 'Тип спеціальності',
        'type' => 'Тип спеціальності',
        'owner' => [
            'firstName' => 'Ім’я',
            'lastName' => 'Прізвище',
            'secondName' => 'По батькові',
            'birthDate' => 'Дата народження',
            'email' => 'E-mail',
            'gender' => 'Стать',
            'position' => 'Посада керівника НМП',
            'taxId' => 'РНОКПП',
            'documents' => [
                'type' => 'Тип документа',
                'number' => 'Серія/номер документа'
            ]
        ],
        'party' => [
            'firstName' => 'Ім’я',
            'lastName' => 'Прізвище',
            'secondName' => 'По батькові',
            'birthDate' => 'Дата народження',
            'email' => 'E-mail',
            'gender' => 'Стать',
            'position' => 'Посада керівника НМП',
            'taxId' => 'РНОКПП',
            'employeeType' => 'Роль',
        ],
        'party.phones.*.type' => 'Тип телефону',
        'party.phones.*.number' => 'Номер телефону',
        'party.documents.*.type' => 'Тип документа',
        'party.documents.*.number' => 'Серія/номер документа',
        'position' => 'Посада',
        'patient' => [
            'firstName' => 'ім’я',
            'lastName' => 'прізвище',
            'secondName' => 'по батькові',
            'birthDate' => 'дата народження',
            'birthCountry' => 'країна народження',
            'birthSettlement' => 'місто народження',
            'gender' => 'стать',
            'email' => 'E-mail',
            'unzr' => 'УНЗР',
            'noTaxId' => 'РНОКПП/ІПН відсутній',
            'taxId' => 'номер РНОКПП',
            'secret' => 'кодове слово',

            'emergencyContact' => [
                'firstName' => 'ім’я',
                'lastName' => 'прізвище',
                'secondName' => 'по батькові'
            ],
        ],
        'patient.phones.*.type' => 'тип телефону',
        'patient.phones.*.number' => 'номер телефону',
        'patient.emergencyContact.phones.*.type' => 'тип телефону',
        'patient.emergencyContact.phones.*.number' => 'номер телефону',
        'patient.authenticationMethods.*' => [
            'type' => 'метод автентифікації',
            'phoneNumber' => 'номер телефону',
            'value' => 'законний представник пацієнта',
            'alias' => 'роль'
        ],

        'addresses' => [
            'area' => 'область',
            'settlement' => 'місто',
            'streetType' => 'тип вулиці',
            'street' => 'назва вулиці',
            'building' => 'будинок',
            'apartment' => 'квартира',
            'zip' => 'поштовий індекс'
        ],
        'documentsRelationship.*' => [
            'type' => 'тип документа',
            'number' => 'серія/номер документа',
            'issuedBy' => 'орган яким виданий документ',
            'issuedAt' => 'дата видачі документа',
            'activeTo' => 'дійсний до'
        ],
        'documents' => 'документ, що засвідчує особу',
        'documents.*' => [
            'type' => 'тип документа',
            'number' => 'серія/номер документа',
            'issuedBy' => 'орган яким виданий документ',
            'issuedAt' => 'дата видачі документа',
            'expirationDate' => 'дійсний до'
        ],
        'patientsFilter' => [
            'firstName' => 'ім’я',
            'lastName' => 'прізвище',
            'birthDate' => 'дата народження',
            'secondName' => 'по батькові',
            'taxId' => 'РНОКПП(ІПН)',
            'phoneNumber' => 'номер телефону',
            'birthCertificate' => 'свідоцтво про народження'
        ],
        'document' => [
            'type' => 'Тип документа',
            'number' => 'Серія/номер документа',
            'issuedBy' => 'Орган яким виданий документ',
            'issuedAt' => 'Дата видачі документа',
            'expirationDate' => 'дійсний до'
        ],
        'passportData' => [
            'firstName' => 'Ім’я',
            'lastName' => 'Прізвище',
            'secondName' => 'По батькові',
            'birthDate' => 'Дата народження',
            'email' => 'E-mail',
            'gender' => 'Стать',
            'position' => 'Посада керівника НМП',
            'taxId' => 'РНОКПП',
            'documents' => [
                'type' => 'Тип документа',
                'number' => 'Серія/номер документа'
            ]
        ],
        'owner.phones.*.number' => 'телефон',
        'owner.phones.*.type' => 'Тип Номера',
        'country' => 'Країна',
        'region' => 'Область',
        'area' => 'Район',
        'settlement' => 'Населений пункт',
        'settlementType' => 'Тип населеного пункту',
        'streetType' => 'Тип вулиці',
        'street' => 'Вулиця',
        'building' => 'Будинок',
        'apartment' => 'Квартира',
        'zipCode' => 'Поштовий індекс',
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
        'healthcareService' => [
            'category' => 'Категорія',
            'conditions' => 'Умови надання',
            'specialityType' => 'Тип спеціальності',
            'status' => 'Статус',
            'type' => 'Тип спеціальності',
            'providingCondition' => 'Умови надання послуг',
            'error' => [
                'legalEntity' => [
                    'status' => 'Заклад має неактивний статус. Створення послуги неможливо.'
                ],
                'division' => [
                    'commonError' => 'Місце надання послуг: загальна помилка',
                    'status' => 'Місце надання послуг має неактивний статус. Створення послуги неможливо.',
                    'location' => 'Для даного типу медичного закладу заповнення полів Location - обов\'язкове',
                    'type' => 'Не вірний тип місця надання послуг',
                    'mapping' => 'Вказаний тип місця надання послуг не дозволяється для поточного закладу',
                    'workingHours' => [
                        'commonError' => 'Помилка для вказаних часових проміжків',
                        'wrongRange' => 'Час закінчення менше часу початку',
                        'wrongShiftStart' => 'Початок поточної зміни не може бути раніше кінця попередньої'
                    ],
                    'address' => [
                        'commonError' => 'Помилка заповнення даних адреси',
                        'type' => 'Неіснуючий тип адреси для місця надання послуг',
                        'settlementType' => 'Неіснуючий тип населеного пункту для місця надання послуг',
                        'streetType' => 'Неіснуючий тип вулично-дорожної системи для місця надання послуг',
                        'zip' => 'Неправильний формат індексу',
                        'mapping' => 'Вказаний тип адреси не дозволяється для даного типу місця надання послуг і поточного закладу',
                    ],
                    'phone' => [
                        'commonError' => 'Помилка заповнення телефонного номеру',
                        'type' => 'Невірний тип телефонного зв\'язку',
                        'number' => 'Невірно вказаний телефонний номер'
                    ]
                ],
                'inDictionary' => [
                    'category' => 'Вказаної категорії не існує',
                    'specialityType' => 'Вказаного типу спеціальності не існує'
                ],
                'constraint' => [
                    'category' => 'Така послуга вже є для цієї категорії',
                    'categoryPharmacy' => 'Категорія PHARMACY вже використовується у цьому місці надання послуг',
                    'providingCondition' => 'Така послуга вже є для цього місця надання послуг',
                ],
                'category' => [
                    'license' => 'Дана категорія не має хоча б однієї відповідної ліцензії'
                ],
                'time' => [
                    'available' => 'Закінчення часу доступності менше часу початку',
                    'notAvailable' => 'Кінцева дата часу недоступності менше початкової'
                ]
            ]
        ],
        'license' => [
            'licenseType' => 'Тип',
            'issuedBy' => 'Орган яким виданий документ',
            'issuedDate' => 'Дата видачі документа',
            'orderNo' => 'Номер  наказу ',
            'licenseNumber' => 'Номер ліцензії',
            'activeFromDate' => 'Дата початку дії ліцензії',
        ],
        'educations' => [
            'degree' => 'Ступінь',
            'speciality' => 'Спеціальність',
            'institutionName' => 'Назва закладу',
            'country' => 'Країна',
            'city' => 'Місто',
            'institutionType' => 'Тип закладу',
            'specialityType' => 'Тип спеціальності',
            'instituteType' => 'Тип закладу',
            'specialityLevel' => 'Рівень спеціальності',
            'diplomaNumber' => 'Номер диплому',
        ],
        'education' => [
            'degree' => 'Ступінь',
            'speciality' => 'Спеціальність',
            'institutionName' => 'Назва закладу',
            'country' => 'Країна',
            'city' => 'Місто',
            'institutionType' => 'Тип закладу',
            'specialityType' => 'Тип спеціальності',
            'instituteType' => 'Тип закладу',
            'specialityLevel' => 'Рівень спеціальності',
            'diplomaNumber' => 'Номер диплому',
        ],
        'contractType' => 'Тип договору',
        'contractorPaymentDetails' => [
            'mfo' => 'МФО',
            'bankName' => 'Назва банку',
            'payerAccount' => 'IBAN',
        ],
        'startDate' => 'Дата початку',
        'endDate' => 'Дата завершення',
        'status' => 'Статус',
        'contractorRmspAmount' => 'Кількість населення',
        'contractorBase' => 'На якій підставі діє підписант',
        'statuteMd5' => 'Статут',
        'additionalDocumentMd5' => 'Додатковий документ',
        'contractorDivisions' => 'Місця надання послуг',
        'externalContractors' => [
            'contract' => [
                'number' => 'Номер договору з субпідрядником',
                'issuedAt' => 'Дата початку договору',
                'expiresAt' => 'Дата закінчення договору',

            ],
            'legalEntity' => [
                'name' => 'Медична організація',

            ],
            'divisions' => [
                'name' => 'Назва Підрозділу',
                'medicalService' => 'Медична послуга'
            ]

        ],
        //! Licence
        'issuedBy' => 'ким видано ліцензію',
        'issuedDate' => 'дата видачі ліцензії',
        'activeFromDate' => 'дата початку дії ліцензії',
        'orderNo' => 'номер наказу',
        'expiryDate' => 'дата завершення дії ліцензії',
        'whatLicensed' => 'напрям діяльності, що ліцензовано',

        'uploadedDocuments.*' => 'для завантаження файлів',
        'verificationCode' => 'код підтвердження з СМС',
        'encounter' => [
            'division.identifier.value' => 'місце надання послуг',
            'class.code' => 'клас взаємодії',
            'type.coding.code' => 'тип взаємодії',
            'period' => [
                'date' => 'дата',
                'start' => 'час початку',
                'end' => 'час закінчення'
            ],
            'priority.coding.code' => 'пріоритет',
            'reasons' => 'причини звернення',
            'diagnoses.role.coding.*.code' => 'тип',
            'diagnoses.rank' => 'пріоритет'
        ],
        'conditions' => 'діагнози',
        'conditions.*.code.coding.0.code' => 'код стану за ICPC-2',
        'conditions.*.code.coding.1.code' => 'код стану за МКХ-10',
        'conditions.*.onsetDate' => 'дата початку',
        'conditions.*.onsetTime' => 'час початку',
        'conditions.*.assertedDate' => 'дата внесення',
        'conditions.*.assertedTime' => 'час внесення',
        'conditions.*.clinicalStatus' => 'клінічний статус',
        'conditions.*.verificationStatus' => 'статус верифікації',
        'conditions.*.severity.coding.*.code' => 'ступінь тяжкості стану',
        'episode' => [
            'name' => 'назва епізоду',
            'type' => [
                'code' => 'тип епізоду'
            ]
        ],
        'immunizations.*' => [
            'primarySource' => 'джерело інформації',
            'performer' => 'виконавець',
            'reportOrigin' => 'пацієнт',
            'notGiven' => 'чи була проведена',
            'explanation.reasons' => 'причини',
            'manufacturer' => '',
            'lotNumber' => '',
            'expirationDate' => '',
            'doseQuantity.value' => '',
            'doseQuantity.unit' => '',
            'site' => '',
            'route' => '',
            'vaccinationProtocols.doseSequence' => '',
            'vaccinationProtocols.authority' => '',
            'vaccinationProtocols.series' => '',
            'vaccinationProtocols.seriesDoses' => '',
            'vaccinationProtocols.targetDiseases' => '',
            'explanation.reasonsNotGiven' => '',
            'date' => 'дата вакцинації',
            'time' => 'час вакцинації',
        ],
        'errors' => [
            'email' => 'Неправильний формат електронної адреси',
            'wrongNumberFormat' => 'Неправильний формат номеру',
            'expiryDateGreat' => 'Дата не може бути менше поточної дати',
            'expiryDateLess' => 'Дата не може бути менше дати початку',
            'invalidNationalId' => 'Номер паспорта має бути: або 2 літери та 6 цифр, або 9 цифр',
            'invalidTaxId' => 'Ідентифікаційний номер повинен містити рівно 10 цифр',
            'date_iso' => 'Дата має бути в форматі ISO 8601',
        ]
    ]
];
