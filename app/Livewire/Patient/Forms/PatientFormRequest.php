<?php

declare(strict_types=1);

namespace App\Livewire\Patient\Forms;

use App\Rules\AlphaNumericWithSymbols;
use App\Rules\Cyrillic;
use App\Rules\InDictionary;
use App\Rules\TwoLettersFourToSixDigitsOrComplex;
use App\Rules\TwoLettersSixDigits;
use App\Rules\EightDigitsHyphenFiveDigits;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PatientFormRequest extends Form
{
    protected const int NO_SELF_AUTH_AGE = 14;
    protected const int NO_SELF_REGISTRATION_AGE = 14;
    protected const int PERSON_FULL_LEGAL_CAPACITY_AGE = 18;

    #[Validate([
        'patient.firstName' => ['required', 'min:3', new Cyrillic()],
        'patient.lastName' => ['required', 'min:3', new Cyrillic()],
        'patient.secondName' => ['nullable', 'min:3', new Cyrillic()],
        'patient.birthDate' => ['required', 'date'],
        'patient.birthCountry' => ['required', 'string'],
        'patient.birthSettlement' => ['required', 'string'],
        'patient.gender' => ['required', 'string', new InDictionary('GENDER')],
        'patient.unzr' => ['nullable', new EightDigitsHyphenFiveDigits()],
        'patient.noTaxId' => ['nullable', 'boolean'],
        'patient.taxId' => ['required_if:patient.noTaxId,false', 'numeric', 'digits:10'],
        'patient.secret' => ['required', 'string', 'min:6'],
        'patient.email' => ['nullable', 'email', 'string'],

        'patient.phones.*.type' => ['nullable', 'string'],
        'patient.phones.*.number' => ['nullable', 'string', 'regex:/^\+38[0-9]{10}$/'],

        'patient.emergencyContact.firstName' => ['required', 'min:3', new Cyrillic()],
        'patient.emergencyContact.lastName' => ['required', 'min:3', new Cyrillic()],
        'patient.emergencyContact.secondName' => ['nullable', 'min:3', new Cyrillic()],
        'patient.emergencyContact.phones.*.type' => ['required', 'string'],
        'patient.emergencyContact.phones.*.number' => ['required', 'string', 'regex:/^\+38[0-9]{10}$/'],

        'patient.authenticationMethods.*.type' => ['required', 'string', new InDictionary('AUTHENTICATION_METHOD')],
        'patient.authenticationMethods.*.phoneNumber' => [
            'required_if:patient.authenticationMethods.*.type,OTP', 'regex:/^\+38[0-9]{10}$/'
        ],
        'patient.authenticationMethods.*.value' => [
            'nullable', 'required_if:patient.authenticationMethods.*.type,THIRD_PERSON', 'string'
        ],
        'patient.authenticationMethods.*.alias' => [
            'nullable', 'required_if:patient.authenticationMethods.*.type,THIRD_PERSON', 'string'
        ]
    ])]
    public array $patient = [
        'phones' => [
            ['type' => null, 'number' => null]
        ],
        'emergencyContact' => [
            'phones' => [
                ['type' => null, 'number' => null]
            ]
        ],
        'authenticationMethods' => [
            ['type' => null]
        ]
    ];

    #[Validate([
        'patientsFilter.firstName' => ['required', 'min:3', new Cyrillic()],
        'patientsFilter.lastName' => ['required', 'min:3', new Cyrillic()],
        'patientsFilter.secondName' => ['nullable', 'min:3', new Cyrillic()],
        'patientsFilter.birthDate' => ['required', 'date'],
        'patientsFilter.taxId' => ['nullable', 'numeric', 'digits:10'],
        'patientsFilter.phoneNumber' => ['nullable', 'string', 'min:13', 'max:13'],
        'patientsFilter.birthCertificate' => ['nullable', 'string']
    ])]
    public array $patientsFilter = [];

    #[Validate([
        'documents' => ['required', 'array'],
        'documents.*.type' => ['required', 'string', new InDictionary('DOCUMENT_TYPE')],
        'documents.*.number' => ['required', 'string', 'max:255'],
        'documents.*.issuedBy' => ['required', 'string', 'max:255'],
        'documents.*.issuedAt' => ['required', 'date', 'before:today', 'after:patient.birthDate'],
        'documents.*.expirationDate' => ['nullable', 'date', 'after:today']
    ])]
    public array $documents = [];

    public array $addresses = [];

    #[Validate([
        'documentsRelationship.*.type' => ['required', 'string', new InDictionary('DOCUMENT_RELATIONSHIP_TYPE')],
        'documentsRelationship.*.number' => ['required', 'string', 'max:255'],
        'documentsRelationship.*.issuedBy' => ['required', 'string', 'max:255'],
        'documentsRelationship.*.issuedAt' => ['required', 'date', 'before:today', 'after:patient.birthDate'],
        'documentsRelationship.*.activeTo' => ['nullable', 'date', 'after:tomorrow']
    ])]
    public array $documentsRelationship = [];

    public array $confidantPerson = [];

    #[Validate([
        'verificationCode' => ['required', 'numeric', 'digits:4']
    ])]
    public string $verificationCode;

    #[Validate([
        'uploadedDocuments.*' => ['nullable', 'file', 'mimes:jpeg,jpg', 'max:10000']
    ])]
    public array $uploadedDocuments;

    /**
     * Validate data for chosen model.
     *
     * @param  string|array  $fields
     * @return array
     * @throws ValidationException
     */
    public function rulesForModelValidate(string|array $fields): array
    {
        if (is_string($fields)) {
            $rules = $this->rulesForModel($fields)->toArray();
        } else {
            $rules = [];

            foreach ($fields as $model) {
                $rules += $this->rulesForModel($model)->toArray();
            }

            if (in_array('patient', $fields, true)) {
                $this->addNoTaxIdValidation($rules);
                $this->addUnzrRuleIfRequired($rules);
                $this->validateAddressees();
            }

            if (!empty($this->documents) && in_array('documents', $fields, true)) {
                $this->addExpirationDateRuleIfRequired($rules);
                $this->addNumberDocumentsValidation($rules);
            }

            if (!empty($this->documentsRelationship) && in_array('documentsRelationship', $fields, true)) {
                $this->addNumberDocumentsRelationshipValidation($rules);
            }
        }

        return $this->validate($rules);
    }

    /**
     * Validate data before sending API request.
     *
     * @return void
     * @throws ValidationException
     */
    public function validateBeforeSendApi(): void
    {
        $errors = new MessageBag();

        // Validate documents for minor patients
        $minorPatientValidation = $this->validateDocumentsForMinorPatient();
        if ($minorPatientValidation['error']) {
            $errors->add('minor_patient', $minorPatientValidation['message']);
        }

        // Validate necessity of confidant person
        $confidantPersonValidation = $this->validateNecessityOfConfidantPerson();
        if ($confidantPersonValidation['error']) {
            $errors->add('confidant_person', $confidantPersonValidation['message']);
        }

        // Validate person's documents
        $documentValidation = $this->validatePersonDocuments();
        if ($documentValidation['error']) {
            $errors->add('documents', $documentValidation['message']);
        }

        if ($errors->isNotEmpty()) {
            $validator = Validator::make([], []); // Empty validator
            $validator->errors()->merge($errors);

            throw new ValidationException($validator);
        }
    }

    /**
     * Do expirationDate required if a specific document type was selected.
     *
     * @param  array  $rules
     * @return void
     */
    private function addExpirationDateRuleIfRequired(array &$rules): void
    {
        $requiredTypes = config('ehealth.expiration_date_exists');

        foreach ($this->documents as $document) {
            if (empty($document['expirationDate']) && in_array($document['type'], $requiredTypes, true)) {
                $rules['documents.expirationDate'][] = 'required';
            }
        }
    }

    /**
     * Add validation for document numbers based on different document types.
     *
     * @param  array  $rules
     * @return void
     */
    private function addNumberDocumentsValidation(array &$rules): void
    {
        foreach ($this->documents as $key => $document) {
            $rules["documents.$key.number"][] = match ($document['type']) {
                'PASSPORT', 'REFUGEE_CERTIFICATE' => new TwoLettersSixDigits(),
                'NATIONAL_ID' => 'digits:9',
                'BIRTH_CERTIFICATE', 'TEMPORARY_PASSPORT', 'CHILD_BIRTH_CERTIFICATE', 'MARRIAGE_CERTIFICATE', 'DIVORCE_CERTIFICATE' => new AlphaNumericWithSymbols(),
                'TEMPORARY_CERTIFICATE' => new TwoLettersFourToSixDigitsOrComplex(),
                'BIRTH_CERTIFICATE_FOREIGN', 'PERMANENT_RESIDENCE_PERMIT' => 'string',
                default => null
            };
        }
    }

    /**
     * Add validation for document numbers based on different document types.
     *
     * @param  array  $rules
     * @return void
     */
    private function addNumberDocumentsRelationshipValidation(array &$rules): void
    {
        foreach ($this->documentsRelationship as $key => $document) {
            if ($document['type'] === 'BIRTH_CERTIFICATE') {
                $rules["documentsRelationship.$key.number"][] = new AlphaNumericWithSymbols();
            }
        }
    }

    /**
     * Do UNZR required if a document type is NATIONAL_ID.
     *
     * @param  array  $rules
     * @return void
     */
    private function addUnzrRuleIfRequired(array &$rules): void
    {
        foreach ($this->documents as $document) {
            if (isset($document['type']) && $document['type'] === 'NATIONAL_ID') {
                $rules['patient.unzr'][] = 'required';
                break;
            }
        }
    }

    /**
     * Do tax_id required if no_tax_id = false and persons age > NO_SELF_AUTH_AGE.
     *
     * @param  array  $rules
     * @return void
     */
    private function addNoTaxIdValidation(array &$rules): void
    {
        if (!empty($this->patient['taxId']) && $this->patient['birthDate'] > self::NO_SELF_AUTH_AGE) {
            $rules['patient.taxId'][] = 'required';
        }
    }

    /**
     * Validate necessity of confidant person.
     *
     * @return array
     */
    private function validateNecessityOfConfidantPerson(): array
    {
        $personAge = Carbon::parse($this->patient['birthDate'])->age;

        // If age less than 18 then check that confidant_person is submitted
        if ($personAge < self::NO_SELF_REGISTRATION_AGE && empty($this->documentsRelationship['personId'])) {
            return [
                'error' => true,
                'message' => __('validation.custom.patient.confidantPersonRequiredForChildren')
            ];
        }

        // If age between 14 and 18 then
        if ($personAge > self::NO_SELF_REGISTRATION_AGE && $personAge < self::PERSON_FULL_LEGAL_CAPACITY_AGE) {
            $legalCapacityDocumentTypes = [
                'MARRIAGE_CERTIFICATE', 'DIVORCE_CERTIFICATE', 'STATE_REGISTER_EXTRACT',
                'EMPLOYMENT_CONTRACT', 'LEGAL_CAPACITY_DOCUMENT'
            ];

            $hasLegalCapacityDocument = false;

            foreach ($this->documents as $document) {
                if (in_array($document['type'], $legalCapacityDocumentTypes, true)) {
                    $hasLegalCapacityDocument = true;
                    break;
                }
            }

            // if none of persons documents has type from PERSON_LEGAL_CAPACITY_DOCUMENT_TYPES config parameter - check that confidant_person is submitted
            if (!$hasLegalCapacityDocument && empty($this->documentsRelationship['personId'])) {
                return [
                    'error' => true,
                    'message' => __('validation.custom.patient.confidantPersonRequiredForMinor')
                ];
            }

            // Else if at least one of submitted person document types exist in PERSON_LEGAL_CAPACITY_DOCUMENT_TYPES config parameter - check that confidant_person is not submitted
            if ($hasLegalCapacityDocument && !empty($this->documentsRelationship['personId'])) {
                return [
                    'error' => true,
                    'message' => __('validation.custom.patient.confidantPersonMustBeCapable')
                ];
            }
        }

        return [
            'error' => false,
            'message' => ''
        ];
    }

    /**
     * Check that document types BIRTH_CERTIFICATE or BIRTH_CERTIFICATE_FOREIGN are submitted if person age < NO_SELF_AUTH_AGE.
     *
     * @return array
     */
    private function validateDocumentsForMinorPatient(): array
    {
        $personAge = Carbon::parse($this->patient['birthDate'])->age;

        if ($personAge < self::NO_SELF_AUTH_AGE) {
            $requiredDocumentTypes = ['BIRTH_CERTIFICATE', 'BIRTH_CERTIFICATE_FOREIGN'];
            $hasRequiredDocument = false;

            if (isset($this->documents)) {
                foreach ($this->documents as $document) {
                    if (in_array($document['type'], $requiredDocumentTypes, true)) {
                        $hasRequiredDocument = true;
                        break;
                    }
                }
            }

            if (!$hasRequiredDocument) {
                return [
                    'error' => true,
                    'message' => __('validation.custom.patient.birthDocumentsRequired')
                ];
            }
        }

        return [
            'error' => false,
            'message' => ''
        ];
    }

    /**
     * Validate person documents.
     *
     * @return array
     */
    private function validatePersonDocuments(): array
    {
        $personAge = Carbon::parse($this->patient['birthDate'])->age;
        $personLegalCapacityDocumentTypes = [
            'MARRIAGE_CERTIFICATE', 'DIVORCE_CERTIFICATE', 'STATE_REGISTER_EXTRACT',
            'EMPLOYMENT_CONTRACT', 'LEGAL_CAPACITY_DOCUMENT'
        ];

        // if age not between 14 and 18
        if ($personAge < self::NO_SELF_REGISTRATION_AGE || $personAge > self::PERSON_FULL_LEGAL_CAPACITY_AGE) {
            if (isset($this->documents)) {
                foreach ($this->documents as $document) {
                    if (in_array($document['type'], $personLegalCapacityDocumentTypes, true)) {
                        // return the first found document type
                        return [
                            'error' => true,
                            'message' => __("{$document['type']} не може бути подана для цієї особи.")
                        ];
                    }
                }
            }
        }

        $personRegistrationDocumentTypes = [
            'PASSPORT', 'NATIONAL_ID', 'BIRTH_CERTIFICATE', 'BIRTH_CERTIFICATE_FOREIGN', 'PERMANENT_RESIDENCE_PERMIT',
            'REFUGEE_CERTIFICATE', 'TEMPORARY_CERTIFICATE', 'TEMPORARY_PASSPORT'
        ];
        $hasLegalCapacityDocument = false;
        $hasRegistrationDocument = false;

        // If there is at least one document with LEGAL_CAPACITY, check for at least one REGISTRATION
        if (isset($this->documents)) {
            foreach ($this->documents as $document) {
                if (in_array($document['type'], $personLegalCapacityDocumentTypes, true)) {
                    $hasLegalCapacityDocument = true;
                }

                if (in_array($document['type'], $personRegistrationDocumentTypes, true)) {
                    $hasRegistrationDocument = true;
                }
            }

            if ($hasLegalCapacityDocument && !$hasRegistrationDocument) {
                return [
                    'error' => true,
                    'message' => __('validation.custom.patient.personalDocumentsRequired')
                ];
            }
        }

        return [
            'error' => false,
            'message' => ''
        ];
    }

    /**
     * Validate addressees.
     *
     * @return void
     * @throws ValidationException
     */
    private function validateAddressees(): void
    {
        $errors = $this->component->addressValidation();

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
