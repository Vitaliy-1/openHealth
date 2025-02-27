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
    'contact_data' => 'Контактні дані',
    'confidant_person_not_exist' => 'Законний представник не був вказаний.',
    'authentication_methods' => 'Методи автентифікації',
    'auth_method' => 'Метод автентифікації',

    // summary
    'summary' => 'Зведені дані',
    'get_access_to_medical_data' => 'Отримати доступ до медичних даних',

    //episodes
    'episodes' => 'Епізоди',

    //diagnoses
    'diagnoses' => 'Діагнози',

    // observations
    'observations' => 'Обстеження'
];
