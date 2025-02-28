<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Patients Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are for various messages related to patients,
    | e.g., patient search, patient-related API request messages, etc,
    |
    */

    // Used not once.
    'patients' => 'Пацієнти',
    'patient_legal_representative' => 'Законний представник пацієнта',
    'add_patient' => 'Додати пацієнта',
    'start_interacting' => 'Розпочати взаємодію',
    'nobody_found' => 'Нікого не знайдено',
    'try_change_search_parameters' => 'Спробуйте змінити параметри пошуку',
    'contact_data' => 'Контактні дані',

    'relation_type' => [
        'primary' => 'Основний',
        'secondary' => 'Не основний'
    ],
    'authentication_method' => [
        'otp' => 'через СМС',
        'offline' => 'через документи',
        'third_person' => 'через законного представника'
    ],
    'documents' => [
        'unzr' => 'УНЗР',
        'birth_certificate' => 'Свідоцтво про народження',
        'birth_certificate_foreign' => 'Свідоцтво про народження іноземного зразку',
        'confidant_certificate' => 'Посвідчення опікуна',
        'court_decision' => 'Рішення суду',
        'document' => 'Документ'
    ],
    'encounter_create' => 'Створення медичного запису',
    'save_to_application' => 'Зберегти в заявки',

    // patient search
    'patient_search' => 'Пошук пацієнта',
    'search' => 'Шукати',
    'all' => 'Всі',
    'applications' => 'Заявки',
    'continue_registration' => 'Продовжити реєстрацію',
    'view_record' => 'Переглянути карту',

    // Create patient
    'patient_information' => 'Інформація про пацієнта',
    'unzr' => 'УНЗР',
    'patient_identity_documents' => 'Документи пацієнта, що підтверджують особу',
    'rnokpp_not_found' => 'РНОКПП/ІПН відсутній',
    'secret' => 'Кодове слово',
    'emergency_contact' => 'Контакт для екстреного зв’язку',
    'incapacitated' => 'Недієздатний пацієнт або дитина до 14 років',
    'search_for_confidant' => 'Шукати представника',
    'confidant_person_documents_relationship' => 'Документи, що підтверджують законність представництва',
    'authentication' => 'Автентифікація',
    'alias' => 'Роль',
    'leaflet' => "Пам’ятка",
    'print_leaflet_for_patient' => "Роздрукувати пам’ятку для ознайомлення пацієнтом",

    // PERSON_VERIFICATION_STATUSES
    'CHANGES_NEEDED' => 'Неуспішно верифіковано (потребує змін)',
    'IN_REVIEW' => 'На опрацюванні',
    'NOT_VERIFIED' => 'Не верифіковано',
    'VERIFICATION_NEEDED' => 'Потребує верифікації',
    'VERIFICATION_NOT_NEEDED' => 'Не потребує верифікації',
    'VERIFIED' => 'Верифіковано',

    // patient-data
    'patient_data' => 'Дані пацієнта',
    'verification_in_eHealth' => 'Верифікація в ЕСОЗ',
    'update_status' => 'Оновити статус',
    'passport_data' => 'Паспортні дані',
    'confidant_person_not_exist' => 'Законний представник не був вказаний.',
    'authentication_methods' => 'Методи автентифікації',
    'auth_method' => 'Метод автентифікації',

    // Summary record
    'summary' => 'Зведені дані',
    'get_access_to_medical_data' => 'Отримати доступ до медичних даних',

    // Episodes record
    'episodes' => 'Епізоди',

    // Diagnoses record
    'diagnoses' => 'Діагнози',

    // Observations record
    'observations' => 'Обстеження',
];
