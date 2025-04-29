<?php

declare(strict_types=1);

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
    'priority' => 'Пріоритет',
    'icpc-2_status_code' => 'Код стану за ICPC-2',
    'code_and_name' => 'Код та назва',
    'write_comment_here' => 'Напишіть коментар тут',
    'diagnoses' => 'Діагнози',
    'date' => 'Дата',

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
    'identity_document' => 'Документ, що засвідчує особу',
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
    'uploading_documents' => 'Завантаження документів',

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

    // Observations record
    'observations' => 'Обстеження',

    // Encounter
    'interaction' => 'Взаємодія',
    'main_data' => 'Основні дані',
    'reasons_for_visit' => 'Причини звернення',
    'examination' => 'Обстеження',
    'vaccinations' => 'Вакцинації',
    'prescriptions' => 'Рецепти',
    'referrals' => 'Направлення',
    'medical_reports' => 'Медичні висновки',
    'procedures' => 'Процедури',
    'treatment_plans' => 'Плани лікування',
    'diagnostic_reports' => 'Діагностичні звіти',
    'clinical_assessments' => 'Клінічні оцінки',

    // Main data
    'referral_available' => 'Є направлення',
    'referral_number' => 'Номер направлення',
    'search_for_referral' => 'Шукати направлення',
    'interaction_class' => 'Клас взаємодії',
    'interaction_type' => 'Тип взаємодії',
    'existing_episode' => 'Існуючий епізод',
    'new_episode' => 'Новий епізод',
    'episode_name' => 'Назва епізоду',
    'episode_type' => 'Тип епізоду',
    'episode_number' => 'Номер епізоду',

    // Reasons
    'reason_for_visit' => 'Причина звернення',

    // Diagnoses
    'icd-10' => 'МКХ-10',
    'clinical_status' => 'Клінічний статус',
    'verification_status' => 'Статус верифікації',
    'entry_date' => 'Дата внесення',
    'entry_time' => 'Час внесення',
    'severity_of_the_condition' => 'Ступінь тяжкості стану',
    'primary_source' => 'Первинне джерело',
    'performer' => 'Виконавець',
    'other_source' => 'Інше джерело',
    'information_source' => 'Джерело інформації',
    'new_primary_diagnose' => "Ви вказали новий основний діагноз.<br> Підтвердження дії змінить основний діагноз епізоду медичної допомоги!",

    // Evidences
    'evidence_conditions' => 'Докази - стани',
    'condition' => 'Стан',

    // Additional data
    'additional_data' => 'Додаткові дані',
    'period_start' => 'Час початку',
    'period_end' => 'Час закінчення',
    'division_name' => 'Місце надання послуг',

    // Immunization
    'immunization' => 'Вакцинації',
    'dosage' => 'Дозування',
    'execution_state' => 'Стан проведення',
    'reason' => 'Причина',
    'has_it_been_done' => 'Чи була проведена',
    'reasons' => 'Причини',
    'source_link' => 'Посилання на джерело',
    'data' => 'Дані',
    'time' => 'Час',
    'manufacturer' => 'Виробник',
    'lot_number' => 'Серія',
    'expiration_date' => 'Дата закінчення придатності',
    'amount_of_injected' => 'Кількість введеної',
    'measurement_units' => 'Одиниці виміру',
    'input_route' => 'Шлях введення',
    'body_part' => 'Частина тіла',
];
