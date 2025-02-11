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

    'add_patient' => 'Додати пацієнта',
    'patients' => 'Пацієнти',
    'firstName' => 'Ім’я',
    'lastName' => 'Прізвище',
    'secondName' => 'По батькові',
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
    "CHANGES_NEEDED" => "Неуспішно верифіковано (потребує змін)",
    "IN_REVIEW" => "На опрацюванні",
    "NOT_VERIFIED" => "Не верифіковано",
    "VERIFICATION_NEEDED" => "Потребує верифікації",
    "VERIFICATION_NOT_NEEDED" => "Не потребує верифікації",
    "VERIFIED" => "Верифіковано"
];
