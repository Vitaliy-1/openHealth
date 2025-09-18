<?php

namespace App\Livewire\Employee\Forms;

use App\Core\Arr;
use App\Models\Employee\BaseEmployee;
use App\Rules\Cyrillic;
use App\Rules\DocumentNumber;
use App\Rules\UniquePassportRule;
use Livewire\Form;
use App\Rules\Name;
use App\Rules\TaxId;
use App\Rules\BirthDate;
use App\Rules\PhoneNumber;
use App\Rules\PhoneDuplicates;
use App\Models\Relations\Party;
use Illuminate\Validation\Rule;
use App\Models\Employee\Employee;
use App\Rules\UniqueEmailInLegalEntity;
use App\Models\Employee\EmployeeRequest;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EmployeeForm extends Form
{
    public string $position = '';
    public string $employeeType = '';
    public string $startDate = '';
    public ?string $endDate = null;
    public ?int $existingPartyId = null;
    public ?string $divisionId = null;

    public ?string $knedp = null;
    public ?TemporaryUploadedFile $keyContainerUpload = null;
    public ?string $password = null;

    public array $documents = [];
    public array $party = [
        'lastName' => '',
        'firstName' => '',
        'secondName' => '',
        'gender' => '',
        'birthDate' => '',
        'phones' => [['type' => '', 'number' => '']],
        'taxId' => '',
        'noTaxId' => false,
        'email' => '',
        'workingExperience' => null,
        'aboutMyself' => '',
    ];

    public array $doctor = [
        'specialities' => [],
        'scienceDegrees' => [],
        'qualifications' => [],
        'educations' => [],
    ];

    public function rulesForSave(): array
    {
        return array_merge(
            $this->rootFieldsRules(),
            $this->partyRules(),
            $this->documentsRules(),
            $this->doctorRules()
        );
    }

    public function rulesForKepOnly(): array
    {
        return [
            'knedp' => ['required', 'string'],
            'password' => ['required', 'string'],
            'keyContainerUpload' => ['required', 'file', 'extensions:dat,pfx,pk8,zs2,jks,p7s'],
        ];
    }

    protected function rootFieldsRules(): array
    {
        return [
            'position' => ['required', 'string', Rule::in(array_keys($this->component->dictionaries['POSITION'] ?? []))],
            'employeeType' => ['required', 'string', Rule::in(array_keys($this->component->dictionaries['EMPLOYEE_TYPE'] ?? []))],
            'startDate' => ['required', 'date_format:Y-m-d'],
            'endDate' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:startDate'],
            'divisionId' => ['nullable', 'string'],
        ];
    }

    protected function partyRules(): array
    {
        return [
            'party.lastName' => ['required', new Name()],
            'party.firstName' => ['required', new Name()],
            'party.secondName' => ['nullable', 'present', new Name()],
            'party.gender' => ['required', 'string', Rule::in(array_keys($this->component->dictionaries['GENDER'] ?? []))],
            'party.birthDate' => ['required', 'date_format:Y-m-d', new BirthDate()],
            'party.phones' => ['required', 'array', 'min:1', new PhoneDuplicates()],
            'party.phones.*.number' => ['required', new PhoneNumber()],
            'party.phones.*.type' => ['required', 'string', Rule::in(array_keys($this->component->dictionaries['PHONE_TYPE'] ?? []))],
            'party.taxId' => ['required', 'string', new TaxId()],
            'party.noTaxId' => ['boolean'],
            'party.email' => ['nullable', 'present', 'email', new UniqueEmailInLegalEntity($this->existingPartyId)],
            'party.workingExperience' => ['required', 'numeric', 'min:1'],
            'party.aboutMyself' => ['nullable', 'present', 'string'],
        ];
    }

    protected function documentsRules(): array
    {
        return [
            'documents' => ['required', 'array', 'min:1', new UniquePassportRule()],
            'documents.*.type' => ['required', 'string', Rule::in(array_keys($this->component->dictionaries['DOCUMENT_TYPE'] ?? []))],
            'documents.*.number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $documentType = $this->documents[$index]['type'] ?? null;
                    if ($documentType) {
                        $validator = validator(
                            [
                                'number' => $value,
                            ],
                            [
                                'number' => [new DocumentNumber($documentType)],
                            ]
                        );
                        if ($validator->fails()) {
                            foreach ($validator->errors()->all() as $error) {
                                $fail($error);
                            }
                        }
                    }
                }
            ],
            'documents.*.issuedBy' => ['nullable', 'present', 'string', 'min:1'],
            'documents.*.issuedAt' => ['required', 'date_format:Y-m-d'],
        ];
    }

    /**
     * Defines validation rules for doctor-related data.
     *
     * @return array
     */
    protected function doctorRules(): array
    {
        $doctorTypes = config('ehealth.doctors_type');
        $isDoctor = in_array($this->employeeType, $doctorTypes, true);

    $educationRules = ['nullable', 'array'];
    $specialitiesRules = ['nullable', 'array'];

    if ($isDoctor) {
        $educationRules[] = 'required';
        $educationRules[] = 'min:1';
        $specialitiesRules[] = 'required';
        $specialitiesRules[] = 'min:1';
    }

    $scienceDegreesRules = ['nullable', 'array'];
    $qualificationsRules = ['nullable', 'array'];

    return [
        'doctor.educations' => $educationRules,
        'doctor.educations.*.country' => ['required', 'string', 'max:255'],
        'doctor.educations.*.city' => ['required', 'string', 'max:255', new Cyrillic()],
        'doctor.educations.*.institutionName' => ['required', 'string', 'max:255', new Cyrillic()],
        'doctor.educations.*.issuedDate' => ['nullable', 'date'],
        'doctor.educations.*.diplomaNumber' => ['required', 'string', 'max:255'],
        'doctor.educations.*.degree' => ['required', 'string', 'max:255'],
        'doctor.educations.*.speciality' => ['required', 'string', 'max:255'],

        'doctor.specialities' => $specialitiesRules,
        'doctor.specialities.*.speciality' => ['required', 'string', 'max:255'],
        'doctor.specialities.*.specialityOfficio' => ['required', 'boolean'],
        'doctor.specialities.*.level' => ['required', 'string', 'max:255'],
        'doctor.specialities.*.qualificationType' => ['required', 'string'],
        'doctor.specialities.*.attestationName' => ['required', 'string', 'max:255', new Cyrillic()],
        'doctor.specialities.*.attestationDate' => ['required', 'date'],
        'doctor.specialities.*.validToDate' => ['nullable', 'date'],
        'doctor.specialities.*.certificateNumber' => ['required', 'string', 'max:255'],

        'doctor.scienceDegrees' => $scienceDegreesRules,
        'doctor.scienceDegrees.*.country' => ['required', 'string', 'max:255'],
        'doctor.scienceDegrees.*.city' => ['required', 'string', 'max:255', new Cyrillic()],
        'doctor.scienceDegrees.*.degree' => ['required', 'string', 'max:255'], // Словник
        'doctor.scienceDegrees.*.institutionName' => ['required', 'string', 'max:255', new Cyrillic()],
        'doctor.scienceDegrees.*.diplomaNumber' => ['required', 'string', 'max:255'],
        'doctor.scienceDegrees.*.speciality' => ['required', 'string', 'max:255'],
        'doctor.scienceDegrees.*.issuedDate' => ['nullable', 'date'],

        'doctor.qualifications' => $qualificationsRules,
        'doctor.qualifications.*.type' => ['required', 'string', 'max:255'], // Словник
        'doctor.qualifications.*.institutionName' => ['required', 'string', 'max:255', new Cyrillic()],
        'doctor.qualifications.*.speciality' => ['required', 'string', 'max:255'], // Словник
        'doctor.qualifications.*.issuedDate' => ['required', 'date'],
        'doctor.qualifications.*.certificateNumber' => ['required', 'string', 'max:255'],
        'doctor.qualifications.*.validTo' => ['nullable', 'date', 'after_or_equal:doctor.qualifications.*.issuedDate'],
        'doctor.qualifications.*.additionalInfo' => ['nullable', 'string', new Cyrillic()],
    ];
}

    /**
     * The single "smart" method to populate the form from any data source.
     */
    public function hydrate(BaseEmployee|Party|null $source = null): void
    {
        $this->reset();

        if ($source === null) {
            return;
        }

        match (get_class($source)) {
            Employee::class => $this->hydrateFromEmployee($source),
            EmployeeRequest::class => $this->hydrateFromEmployeeRequest($source),
            Party::class => $this->hydrateFromParty($source),
        };
    }

    /**
     * Resets only the fields related to a specific position/employment.
     * This is called in the 'Add Position' component.
     */
    public function resetPositionFields(): void
    {
        $this->position = '';
        $this->employeeType = '';
        $this->startDate = '';
        $this->endDate = null;
        $this->divisionId = null;
    }

    /**
     * This eliminates all code duplication.
     */
    private function populatePartyData(Party $party): void
    {
        $party->loadMissing(['phones', 'documents']);
        $this->existingPartyId = $party->id;

        $this->party['lastName'] = $party->last_name;
        $this->party['firstName'] = $party->first_name;
        $this->party['secondName'] = $party->second_name;
        $this->party['gender'] = $party->gender;
        $this->party['birthDate'] = $party->birth_date?->format('Y-m-d');
        $this->party['taxId'] = $party->tax_id;
        $this->party['noTaxId'] = (bool)$party->no_tax_id;
        $this->party['email'] = $party->email;
        $this->party['workingExperience'] = $party->working_experience;
        $this->party['aboutMyself'] = $party->about_myself;

        $phones = $party->phones;
        // Only overwrite phones if the form is empty
        if ($phones->isNotEmpty() && empty($this->party['phones'][0]['number'])) {
            $this->party['phones'] = $phones->map(fn($p) => ['type' => $p->type, 'number' => $p->number])->toArray();
        }

        $documents = $party->documents;
        // Only overwrite documents if the form is empty
        if ($documents->isNotEmpty() && empty($this->documents)) {
            $this->documents = $documents->map(function ($doc) {
                return [
                    'type' => $doc->type,
                    'number' => $doc->number,
                    'issuedBy' => $doc->issued_by,
                    'issuedAt' => $doc->issued_at?->format('Y-m-d'),
                ];
            })->toArray();
        }
    }

    private function hydrateFromEmployee(Employee $employee): void
    {
        $employee->loadMissing(['party.phones', 'party.documents', 'educations', 'specialities', 'qualifications', 'scienceDegrees']);
        if ($employee->party) {
            $this->populatePartyData($employee->party);
        }
        $this->position = $employee->position;
        $this->employeeType = $employee->employee_type;
        $this->startDate = $employee->start_date?->format('Y-m-d');
        $this->endDate = $employee->end_date?->format('Y-m-d');
        $this->divisionId = $employee->division_id;

        $this->doctor['educations'] = $employee->educations->map(fn($edu) => Arr::toCamelCase($edu->toArray()))->toArray();
        $this->doctor['specialities'] = $employee->specialities->map(fn($spec) => Arr::toCamelCase($spec->toArray()))->toArray();
        $this->doctor['qualifications'] = $employee->qualifications->map(fn($spec) => Arr::toCamelCase($spec->toArray()))->toArray();
        $this->doctor['scienceDegrees'] = $employee->scienceDegrees->map(fn($spec) => Arr::toCamelCase($spec->toArray()))->toArray();
    }

    /**
     * Hydrates the form from an EmployeeRequest model.
     */
    private function hydrateFromEmployeeRequest(EmployeeRequest $request): void
    {
        $request->loadMissing(['party', 'revision']);
        $revisionData = $request->revision->data ?? [];

        // 1. Populate from revision data first
        $this->position = $revisionData['employee_request_data']['position'] ?? '';
        $this->employeeType = $revisionData['employee_request_data']['employee_type'] ?? '';
        $this->startDate = $revisionData['employee_request_data']['start_date'] ?? '';
        $this->documents = Arr::toCamelCase($revisionData['documents'] ?? []);
        $this->doctor = Arr::toCamelCase($revisionData['doctor'] ?? []);
        $this->party = array_merge($this->party, Arr::toCamelCase($revisionData['party'] ?? []));
        $this->party['phones'] = !empty($revisionData['phones']) ? Arr::toCamelCase($revisionData['phones']) : [['type' => 'MOBILE', 'number' => '']];

        // 2. Overwrite with live data from the Party, but without overwriting documents
        if ($request->party) {
            $this->populatePartyData($request->party);
        }
    }

    /**
     * Hydrates the form from a Party model (for "Add Position").
     */
    private function hydrateFromParty(Party $party): void
    {
        $this->populatePartyData($party);

        $needsRevisionCheck = empty($this->documents);
        if ($needsRevisionCheck) {
            $latestRequest = $party->employeeRequests()->with('revision')->latest()->first();
            if ($latestRequest && $latestRequest->revision) {
                $revisionData = $latestRequest->revision->data;
                if (empty($this->documents) && !empty($revisionData['documents'])) {
                    $this->documents = Arr::toCamelCase($revisionData['documents']);
                }
                if (empty($this->party['phones'][0]['number']) && !empty($revisionData['phones'])) {
                    $this->party['phones'] = Arr::toCamelCase($revisionData['phones']);
                }
            }
        }
    }

    /**
     * Prepares and returns a FLAT array of all form data for the repository.
     * The logic for creating a nested structure for the revision is moved to the Trait.
     */
    public function getPreparedData(): array
    {
        $formData = $this->all();
        $partyData = $formData['party'] ?? [];
        unset($formData['party']);
        $formData = array_merge($formData, $partyData);

        unset(
            $formData['existingPartyId'],
            $formData['knedp'],
            $formData['keyContainerUpload'],
            $formData['password']
        );

        return Arr::toSnakeCase($formData);
    }

    /**
     * Resets the form to its default state.
     */
    /**
     * Resets the form to its default state.
     */
    public function reset(...$properties): void
    {
        parent::reset(...$properties);
    }
}
