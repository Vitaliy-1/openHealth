<?php

namespace App\Livewire\Patient\Forms;

use App\Rules\AlphaNumericWithSymbols;
use App\Rules\Cyrillic;
use App\Rules\TwoLettersFourToSixDigitsOrComplex;
use App\Rules\TwoLettersSixDigits;
use App\Rules\EightDigitsHyphenFiveDigits;
use Carbon\Carbon;
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
        'patient.gender' => ['required', 'string'],
        'patient.unzr' => ['nullable', new EightDigitsHyphenFiveDigits()],
        'patient.taxId' => ['nullable', 'numeric', 'digits:10'],
        'patient.secret' => ['required', 'string', 'min:6'],
        'patient.email' => ['nullable', 'email', 'string'],
        'patient.preferredWayCommunication' => ['nullable', 'string'],

        'patient.phones.type' => ['nullable', 'string'],
        'patient.phones.number' => ['nullable', 'string', 'min:13', 'max:13'],

        'patient.emergencyContact.firstName' => ['required', 'min:3', new Cyrillic()],
        'patient.emergencyContact.lastName' => ['required', 'min:3', new Cyrillic()],
        'patient.emergencyContact.secondName' => ['nullable', 'min:3', new Cyrillic()],
        'patient.emergencyContact.phones.type' => ['required', 'string'],
        'patient.emergencyContact.phones.number' => ['required', 'string', 'min:13', 'max:13'],

        'patient.authenticationMethods.type' => ['required', 'string'],
        'patient.authenticationMethods.phoneNumber' => ['nullable', 'string', 'min:13', 'max:13'],
        'patient.authenticationMethods.value' => ['nullable', 'string'],
        'patient.authenticationMethods.alias' => ['nullable', 'string']
    ])]
    public array $patient = [];

    #[Validate([
        'documents.type' => ['required', 'string'],
        'documents.number' => ['required', 'string', 'max:255'],
        'documents.issuedBy' => ['required', 'string'],
        'documents.issuedAt' => ['required', 'date', 'before:today', 'after:patient.birthDate'],
        'documents.expirationDate' => ['nullable', 'date', 'after:today']
    ])]
    public array $documents = [];

    public array $addresses = [];

    #[Validate([
        'documentsRelationship.type' => ['required', 'string'],
        'documentsRelationship.number' => ['required', 'string', 'max:255'],
        'documentsRelationship.issuedBy' => ['required', 'string'],
        'documentsRelationship.issuedAt' => ['required', 'date', 'before:today', 'after:patient.birthDate'],
        'documentsRelationship.activeTo' => ['nullable', 'date', 'after:today']
    ])]
    public array $documentsRelationship = [];

    #[Validate([
        'confirmationCode' => ['required', 'numeric', 'digits:4']
    ])]
    public string $confirmationCode;

    #[Validate([
        'uploadedDocuments.unzr' => ['nullable', 'file', 'mimes:jpeg', 'max:10000'],
        'uploadedDocuments.documentsRelationship' => ['nullable', 'file', 'mimes:jpeg', 'max:10000']
    ])]
    public array $uploadedDocuments;

    /**
     * Validate data for chosen model.
     *
     * @param  string  $model
     * @return array
     * @throws ValidationException
     */
    public function rulesForModelValidate(string $model): array
    {
        $rules = $this->rulesForModel($model)->toArray();

        if ($model === 'documents' && !empty($this->documents)) {
            $this->addExpirationDateRuleIfRequired($rules);
            $this->addNumberDocumentsValidation($rules);
        }

        if ($model === 'documentsRelationship' && !empty($this->documentsRelationship)) {
            $this->addNumberDocumentsRelationshipValidation($rules);
        }

        if ($model === 'patient') {
            $this->addNoTaxIdValidation($rules);
            $this->addUnzrRuleIfRequired($rules);
        }

        if ($model === 'uploadedDocuments') {
            $this->addMimeTypeValidation();
        }

        return $this->validate($rules);
    }

    /**
     * Validate data before sending API request.
     *
     * @return array
     */
    public function validateBeforeSendApi(): array
    {
        $validationErrors = [];

        // Validate documents for minor patients
        $minorPatientValidation = $this->validateDocumentsForMinorPatient();
        if ($minorPatientValidation['error']) {
            $validationErrors[] = $minorPatientValidation['message'];
        }

        // Validate necessity of confidant person
        $confidantPersonValidation = $this->validateNecessityOfConfidantPerson();
        if ($confidantPersonValidation['error']) {
            $validationErrors[] = $confidantPersonValidation['message'];
        }

        // Validate person's documents
        $documentValidation = $this->validatePersonDocuments();
        if ($documentValidation['error']) {
            $validationErrors[] = $documentValidation['message'];
        }

        // Return validation errors if any
        if (!empty($validationErrors)) {
            return [
                'error' => true,
                'messages' => $validationErrors
            ];
        }

        // No errors found
        return [
            'error' => false,
            'messages' => []
        ];
    }

    /**
     * Do expirationDate required if a specific document type was selected.
     *
     * @param  array  $rules
     * @return void
     */
    private function addExpirationDateRuleIfRequired(array &$rules): void
    {
        $requiredTypes = [
            'NATIONAL_ID', 'PERMANENT_RESIDENCE_PERMIT', 'REFUGEE_CERTIFICATE', 'TEMPORARY_CERTIFICATE',
            'TEMPORARY_PASSPORT'
        ];

        if (empty($this->documents['expirationDate']) && in_array($this->documents['type'], $requiredTypes, true)) {
            $rules['documents.expirationDate'][] = 'required';
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
        $rules['documents.number'][] = match ($this->documents['type']) {
            'PASSPORT', 'REFUGEE_CERTIFICATE' => new TwoLettersSixDigits(),
            'NATIONAL_ID' => 'digits:9',
            'BIRTH_CERTIFICATE', 'TEMPORARY_PASSPORT', 'CHILD_BIRTH_CERTIFICATE', 'MARRIAGE_CERTIFICATE', 'DIVORCE_CERTIFICATE' => new AlphaNumericWithSymbols(),
            'TEMPORARY_CERTIFICATE' => new TwoLettersFourToSixDigitsOrComplex(),
            'BIRTH_CERTIFICATE_FOREIGN', 'PERMANENT_RESIDENCE_PERMIT' => 'string'
        };
    }

    /**
     * Add validation for document numbers based on different document types.
     *
     * @param  array  $rules
     * @return void
     */
    private function addNumberDocumentsRelationshipValidation(array &$rules): void
    {
        if ($this->documentsRelationship['type'] === 'BIRTH_CERTIFICATE') {
            $rules['documentsRelationship.number'][] = new AlphaNumericWithSymbols();
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
        if ($personAge < self::NO_SELF_REGISTRATION_AGE && empty($this->documentsRelationship['confidantPersonId'])) {
            return [
                'error' => true,
                'message' => __('Confidant person is mandatory for children.')
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
            if (!$hasLegalCapacityDocument && empty($this->documentsRelationship['confidantPersonId'])) {
                return [
                    'error' => true,
                    'message' => __('Confidant person is mandatory for minor patients.')
                ];
            }

            // Else if at least one of submitted person document types exist in PERSON_LEGAL_CAPACITY_DOCUMENT_TYPES config parameter - check that confidant_person is not submitted
            if ($hasLegalCapacityDocument && !empty($this->documentsRelationship['confidantPersonId'])) {
                return [
                    'error' => true,
                    'message' => __('Confidant can not be submitted for person who has document that proves legal capacity.')
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
                    'message' => __('Documents should contain one of: BIRTH_CERTIFICATE, BIRTH_CERTIFICATE_FOREIGN.'),
                ];
            }
        }

        return [
            'error' => false,
            'message' => '',
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
                            'message' => __("{$document['type']} can not be submitted for this person")
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
                    'message' => __('Document that proves personal data must be submitted.')
                ];
            }
        }

        return [
            'error' => false,
            'message' => ''
        ];
    }

    /**
     * Check if the mime type for uploaded files is only JPG.
     *
     * @throws ValidationException
     */
    private function addMimeTypeValidation(): void
    {
        if (isset($this->uploadedDocuments['unzr']) && $this->uploadedDocuments['unzr']->getMimeType() !== 'image/jpeg') {
            throw ValidationException::withMessages([
                'patientRequest.uploadedDocuments.unzr' => 'The file must be a JPG image.',
            ]);
        }

        if (isset($this->uploadedDocuments['documentsRelationship']) && $this->uploadedDocuments['documentsRelationship']->getMimeType() !== 'image/jpeg') {
            throw ValidationException::withMessages([
                'patientRequest.uploadedDocuments.documentsRelationship' => 'The file must be a JPG image.'
            ]);
        }
    }
}
