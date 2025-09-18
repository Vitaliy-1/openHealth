<?php

declare(strict_types=1);

namespace App\Livewire\Employee\Traits;

use App\Classes\eHealth\Api\EmployeeRequest as EHealthEmployeeRequest;
use App\Classes\eHealth\Payloads\EHealthEmployeePayload;
use App\Core\Arr;
use App\Enums\Employee\RequestStatus;
use App\Enums\Employee\RevisionStatus;
use App\Exceptions\EHealth\EHealthResponseException;
use App\Exceptions\EHealth\EHealthValidationException;
use App\Models\Employee\BaseEmployee;
use App\Models\Employee\EmployeeRequest;
use App\Models\Relations\Party;
use App\Models\Revision;
use App\Models\User;
use App\Repositories\Repository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use RuntimeException;

trait ManagesEmployeeForm
{
    use WithFileUploads;

    protected ?BaseEmployee $employeeRequest, $employee = null;

    abstract protected function getEmployeeRequestForSave(): ?EmployeeRequest;

    private function processAndSave(): void
    {
        // Livewire automatically handles validation on state-changing methods.
        // If validation fails, a ValidationException is thrown.
        DB::transaction(fn() => $this->saveOrUpdateDraft());
    }

    public function save(): void
    {
        try {
            $this->processAndSave();
            $this->dispatch('flashMessage', ['message' => __('forms.employee_request_saved_successfully'), 'type' => 'success']);
        } catch (ValidationException $e) {
            $this->handleValidationException($e);
        } catch (Exception $e) {
            $this->handleGeneralException($e);
        }
    }

    public function prepareForSigning(): void
    {
        try {
            $this->processAndSave();
            $this->dispatch('flashMessage', ['message' => __('forms.employee_request_saved_successfully'), 'type' => 'success']);
            $this->dispatch('open-signature-modal');
        } catch (ValidationException $e) {
            $this->handleValidationException($e);
        } catch (Exception $e) {
            $this->handleGeneralException($e);
        }
    }

    public function sign()
    {
        Log::info('Attempting to sign.');

        try {
            // STEP 1: Always save the latest form data before signing.
            $this->processAndSave();

            // STEP 2: Validate and get the draft request.
            $requestToSign = $this->validateAndGetDraft();

            // STEP 3: Sign the data.
            $signedContent = $this->signDataWithCipher($requestToSign);

            // STEP 4: Send the data to the eHealth API.
            $eHealthResponse = new EHealthEmployeeRequest()->create($signedContent);
            $this->updateLocalRecords($requestToSign, $eHealthResponse);

            // STEP 5: Redirect on success.
            session()?->flash('success', __('employees.sign_success'));
            $this->resetSignatureFields();
            Log::info('Successfully signed and will redirect.');

            return redirect()->route('employee.index', ['legalEntity' => legalEntity()->id]);

        } catch (Exception $e) {
            $this->handleGeneralException($e);
        }
    }

    /**
     * Resets only the fields related to the digital signature form inputs.
     */
    public function resetSignatureFields(): void
    {
        $this->form->reset('keyContainerUpload', 'password', 'knedp');
    }

    /**
     * Updates an existing draft request and its revision.
     * It will only update the associated Party if the Party is not yet "locked"
     * (i.e., not registered with E-Health and not linked to a user).
     */
    protected function updateExistingDraft(array $preparedDataForDb): void
    {
        // Step 1: Update the associated Party, but only if it's still editable.
        $party = $this->employeeRequest->party;
        if ($party && is_null($party->user_id) && is_null($party->uuid)) {
            $partyData = $this->extractPartyData($preparedDataForDb);
            $party->update($partyData);
        }

        // Step 2: Update the EmployeeRequest model itself.
        $requestAttributes = Arr::only($preparedDataForDb, ['position', 'employee_type', 'start_date', 'end_date', 'division_id']);
        $this->employeeRequest->fill($requestAttributes)->save();

        // Step 3: Update the revision to reflect the latest state.
        $nestedDataForRevision = $this->prepareDataForRevision($preparedDataForDb);
        if ($this->employeeRequest->revision) {
            $this->employeeRequest->revision->update(['data' => $nestedDataForRevision]);
        } else {
            $this->saveRevisionForRequest($this->employeeRequest, $nestedDataForRevision);
        }
    }

    /**
     * Handles a detailed validation error from the eHealth API.
     */
    protected function handleEHealthValidationError(EHealthValidationException $e): void
    {
        $fullMessage = $e->getTranslatedMessage();
        $this->dispatch('flashMessage', ['message' => $fullMessage, 'type' => 'error', 'persistent' => true]);

        Log::error(
            'EHealth Validation Error: ' . $fullMessage,
            [
                'details' => $e->getDetails(),
                'trace'   => $e->getTraceAsString(),
            ]
        );
    }

    /**
     * Creates a new draft request.
     * The business logic for finding/creating Party and User is now here.
     */
    protected function createNewDraft(array $preparedDataForDb): void
    {
        // Step 1: Business logic to find or create the Party.
        $partyData = $this->extractPartyData($preparedDataForDb);
        $party = $this->findOrCreateParty($partyData);

        // Step 2: Business logic to find the associated user.
        $user = null;
        if (!empty($party->email)) {
            $user = User::where('email', $party->email)->first();
        }

        // Step 3: Prepare data specifically for the EmployeeRequest model.
        $employeeRequestData = Arr::only($preparedDataForDb, [
            'position', 'start_date', 'end_date', 'employee_type', 'division_id'
        ]);

        // Step 4: Call the clean repository method to persist the new draft.
        $newRequest = Repository::employee()->createEmployeeRequestDraft(
            $employeeRequestData,
            $party,
            legalEntity(),
            $user
        );

        // Step 5: Handle the revision logic.
        $nestedDataForRevision = $this->prepareDataForRevision($preparedDataForDb);
        $this->saveRevisionForRequest($newRequest, $nestedDataForRevision);
        $this->employeeRequest = $newRequest;
        if (property_exists($this, 'employeeRequestId')) {
            $this->employeeRequestId = $newRequest->id;
        }
    }

    /**
     * Simplified logic moved from the repository.
     * Finds an existing party or creates a new one. It does NOT update.
     */
    private function findOrCreateParty(array $partyData): Party
    {
        $party = null;
        if (!empty($partyData['email'])) {
            $party = Party::where('email', $partyData['email'])->first();
        }
        if ($party === null && !empty($partyData['tax_id'])) {
            $party = Party::where('tax_id', $partyData['tax_id'])->first();
        }

        return $party ?? Party::create($partyData);
    }

    /**
     * Extracts party-related fields from the main data array.
     */
    private function extractPartyData(array $preparedData): array
    {
        return Arr::only($preparedData, [
            'last_name', 'first_name', 'second_name', 'gender', 'birth_date',
            'tax_id', 'no_tax_id', 'email', 'working_experience', 'about_myself',
        ]);
    }

    /**
     * A centralized exception handler for generic, non-validation errors.
     */
    private function handleException(Exception $e): void
    {
        Log::error('Process failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        $this->dispatch('flashMessage', ['message' => $e->getMessage(), 'type' => 'error', 'persistent' => true]);
    }

    /**
     * A new centralized exception handler for various specific exceptions.
     */
    private function handleGeneralException(Exception $e): void
    {
        match (true) {
            $e instanceof ValidationException => $this->handleValidationException($e),
            $e instanceof EHealthValidationException => $this->handleEHealthValidationError($e),
            $e instanceof EHealthResponseException => $this->handleEHealthResponseException($e),
            $e instanceof ConnectionException => $this->handleConnectionException($e),
            default => $this->handleException($e),
        };
        $this->dispatch('close-signature-modal');
    }

    private function handleEHealthResponseException(EHealthResponseException $e): void
    {
        $this->dispatch('flashMessage', ['message' => $e->getMessage(), 'type' => 'error', 'persistent' => true]);
        Log::error(
            'EHealth response error: ' . $e->getMessage(),
            [
                'details' => $e->getDetails(),
                'trace'   => $e->getTraceAsString(),
            ]
        );
    }

    private function handleConnectionException(ConnectionException $e): void
    {
        $this->dispatch('flashMessage', ['message' => __('forms.ehealth_connection_error'), 'type' => 'error', 'persistent' => true]);
        Log::error('EHealth connection error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    }


    /**
     * Prepares the nested data structure required for a Revision from flat form data.
     */
    private function prepareDataForRevision(array $flatData): array
    {
        $employeeChunk = Arr::only($flatData, ['position', 'employee_type', 'start_date', 'end_date', 'division_id']);
        $partyChunk = Arr::only($flatData, ['last_name', 'first_name', 'second_name', 'gender', 'birth_date', 'tax_id', 'no_tax_id', 'email', 'working_experience', 'about_myself']);
        $documentsChunk = $flatData['documents'] ?? [];
        $phonesChunk = $flatData['phones'] ?? [];
        $doctorChunk = $flatData['doctor'] ?? [];
        return [
            'employee_request_data' => $employeeChunk,
            'party' => $partyChunk,
            'documents' => $documentsChunk,
            'phones' => $phonesChunk,
            'doctor' => $doctorChunk,
        ];
    }

    /**
     * Encapsulates the logic for creating and saving a new revision for a request.
     */
    private function saveRevisionForRequest(BaseEmployee $request, array $nestedData): void
    {
        $revision = new Revision();
        $revision->data = $nestedData;
        $revision->status = RevisionStatus::PENDING;
        $request->revision()->save($revision);
    }

    /**
     * The single source of truth for creating or updating a draft.
     * This method contains the core logic for validation and persistence.
     *
     * @return EmployeeRequest The saved or updated request instance.
     * @throws ValidationException
     */
    private function saveOrUpdateDraft(): EmployeeRequest
    {
        $this->form->validate($this->form->rulesForSave());

        $preparedDataForDb = $this->form->getPreparedData();
        $this->employeeRequest = $this->getEmployeeRequestForSave();

        if ($this->employeeRequest && is_null($this->employeeRequest->uuid)) {
            $this->updateExistingDraft($preparedDataForDb);
        } else {
            $this->createNewDraft($preparedDataForDb);
        }

        return $this->employeeRequest;
    }

    /**
     * Handles ValidationException by dispatching events for user feedback and scrolling.
     */
    private function handleValidationException(ValidationException $e): void
    {
        $validator = $e->validator;
        $allErrorKeys = collect($validator->errors()->keys())->unique();

        // A map of translatable field sections.
        $sections = [
            'form.documents' => __('forms.document'),
            'form.doctor.educations' => __('forms.education'),
            'form.doctor.specialities' => __('forms.specialities'),
            'form.doctor.qualifications' => __('forms.qualifications'),
            'form.doctor.scienceDegrees' => __('forms.science_degree'),
        ];

        // A map of translatable specific fields (with wildcards for nested arrays).
        $fieldTranslations = [
            'form.party.firstName' => __('forms.first_name'),
            'form.party.lastName' => __('forms.last_name'),
            'form.party.secondName' => __('forms.second_name'),
            'form.party.gender' => __('forms.gender'),
            'form.party.birthDate' => __('forms.birth_date'),
            'form.party.taxId' => __('forms.tax_id'),
            'form.party.noTaxId' => __('forms.no_tax_id'),
            'form.party.email' => __('forms.email'),
            'form.party.workingExperience' => __('forms.working_experience'),
            'form.party.aboutMyself' => __('forms.about_myself'),
            'form.position' => __('forms.position'),
            'form.employeeType' => __('forms.role'),
            'form.startDate' => __('forms.start_date_work'),
            'form.endDate' => __('forms.end_date_work'),
            'form.party.phones.*.number' => __('forms.phone_number'),
            'form.party.phones.*.type' => __('forms.phone_type'),
            'form.documents.*.type' => __('forms.document_type'),
            'form.documents.*.number' => __('forms.document_number'),
            'form.documents.*.issuedBy' => __('forms.issued_by'),
            'form.documents.*.issuedAt' => __('forms.issued_at'),
            'form.doctor.educations.*.city' => __('forms.city'),
            'form.doctor.educations.*.institutionName' => __('forms.institution_name'),
            'form.doctor.educations.*.speciality' => __('forms.speciality'),
            'form.doctor.educations.*.degree' => __('forms.degree'),
            'form.doctor.educations.*.issuedDate' => __('forms.issued_date'),
            'form.doctor.educations.*.diplomaNumber' => __('forms.diploma_number'),
            'form.doctor.specialities.*.attestationName' => __('forms.attestationName'),
            'form.doctor.specialities.*.level' => __('forms.select_level'),
            'form.doctor.qualifications.*.institutionName' => __('forms.institutionName'),
            'form.doctor.qualifications.*.speciality' => __('forms.speciality'),
            'form.doctor.scienceDegrees.*.city' => __('forms.city'),
            'form.doctor.scienceDegrees.*.institutionName' => __('forms.institutionName'),
            'form.doctor.scienceDegrees.*.speciality' => __('forms.speciality'),
            'form.doctor.scienceDegrees.*.issuedDate' => __('forms.issuedDate'),
        ];

        $fieldsToDisplay = $allErrorKeys
            ->map(function ($key) use ($fieldTranslations, $sections, $allErrorKeys) {
                // Check if this is a top-level section key (e.g., 'form.documents')
                if (array_key_exists($key, $sections)) {
                    // Check if there are any more specific errors within this section.
                    $hasSpecificErrors = $allErrorKeys->contains(fn($errorKey) =>
                    str_starts_with($errorKey, $key . '.')
                    );

                    // If the section is a top-level error and has no specific sub-errors, it means the whole section is empty/missing.
                    if (!$hasSpecificErrors) {
                        return __('forms.section_not_filled', ['section' => $sections[$key]]);
                    }
                }

                // Check for an exact field translation match.
                if (isset($fieldTranslations[$key])) {
                    return $fieldTranslations[$key];
                }

                // Match nested keys with wildcards using regex (most reliable method).
                foreach ($fieldTranslations as $pattern => $translation) {
                    $patternRegex = '/^' . str_replace('\*', '\d+', preg_quote($pattern, '/')) . '$/';
                    if (preg_match($patternRegex, $key)) {
                        return $translation;
                    }
                }

                // Fallback to the key itself if no translation is found.
                return $key;
            })
            ->filter()
            ->unique()
            ->implode(', ');

        // Check if the flash message is empty and add a default message.
        if (empty($fieldsToDisplay)) {
            $flashMessage = __('forms.validation_error_unknown');
        } else {
            $flashMessage = __('forms.validation_fix_fields', ['fields' => $fieldsToDisplay]);
        }

        $this->dispatch('flashMessage', ['message' => $flashMessage, 'type' => 'error', 'persistent' => true]);

        if (!empty($validator->errors()->keys())) {
            $this->dispatch('validation-failed-scroll', firstErrorKey: $validator->errors()->keys()[0]);
        }
    }

    /**
     * Gets the draft and validates it, including KEP-specific validation.
     *
     * @throws ValidationException
     * @throws RuntimeException
     */
    private function validateAndGetDraft(): EmployeeRequest
    {
        $requestToSign = $this->getEmployeeRequestForSave();

        // Throw an exception if the draft is not found or has already been signed.
        if (is_null($requestToSign) || !is_null($requestToSign->uuid)) {
            throw new RuntimeException(__('forms.draft_not_found_or_already_signed'), 400);
        }

        // Validate KEP-specific fields.
        $this->form->validate($this->form->rulesForKepOnly());

        return $requestToSign;
    }

    /**
     * Signs the data using SignatureService.
     *
     * @throws RuntimeException
     */
    private function signDataWithCipher(EmployeeRequest $requestToSign): string
    {
        $requestToSign->load('revision');
        $nestedDataForRevision = $requestToSign->revision->data;
        $payloadToSign = EHealthEmployeePayload::prepare($nestedDataForRevision);
        return signatureService()->signData(
            $payloadToSign,
            $this->form->password,
            $this->form->knedp,
            $this->form->keyContainerUpload,
            Auth::user()->party->tax_id
        );
    }

    /**
     * Updates local records with the response from the eHealth API.
     */
    private function updateLocalRecords(EmployeeRequest $request, array $eHealthResponse): void
    {
        $uuid = $eHealthResponse['id'];

        $request->update(
            [
                'uuid' => $uuid,
                'legal_entity_uuid' => legalEntity()->uuid,
                'inserted_at' => Carbon::now(),
                'status' => RequestStatus::SIGNED,
            ]
        );

        $request->revision->update(
            [
                'ehealth_response' => $eHealthResponse['ehealth_response'],
                'status'           => RevisionStatus::SENT,
            ]
        );
    }

    /**
     * Checks if a ValidationException contains KEP-related errors.
     */
    private function isKepValidationError(ValidationException $e): bool
    {
        $errors = $e->validator->errors()->keys();
        return collect($errors)->contains(fn($key) =>
            str_contains($key, 'form.password') ||
            str_contains($key, 'form.keyContainerUpload') ||
            str_contains($key, 'form.knedp')
        );
    }
}
