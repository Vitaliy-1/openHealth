<?php

namespace App\Repositories;

use Log;
use Exception;
use App\Core\Arr;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Division;
use App\Models\Revision;
use Illuminate\Support\Str;
use App\Models\LegalEntity;
use App\Models\Relations\Party;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\Employee\BaseEmployee;
use Illuminate\Http\RedirectResponse;
use App\Classes\eHealth\Api\EmployeeApi;
use App\Models\Employee\EmployeeRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ResponseValidator;

class EmployeeRepository
{
    protected ?UserRepository          $userRepository;
    protected ?PartyRepository         $partyRepository;
    protected ?PhoneRepository         $phoneRepository;
    protected ?DocumentRepository      $documentRepository;
    protected ?EducationRepository     $educationRepository;
    protected ?ScienceDegreeRepository $scienceDegreeRepository;
    protected ?QualificationRepository $qualificationRepository;
    protected ?SpecialityRepository    $specialityRepository;
    protected ?RevisionRepository      $revisionRepository;

    public function __construct(
        UserRepository          $userRepository,
        PartyRepository         $partyRepository,
        PhoneRepository         $phoneRepository,
        DocumentRepository      $documentRepository,
        EducationRepository     $educationRepository,
        ScienceDegreeRepository $scienceDegreeRepository,
        QualificationRepository $qualificationRepository,
        SpecialityRepository    $specialityRepository,
        RevisionRepository      $revisionRepository
    )
    {
        $this->userRepository          = $userRepository;
        $this->partyRepository         = $partyRepository;
        $this->phoneRepository         = $phoneRepository;
        $this->documentRepository      = $documentRepository;
        $this->educationRepository     = $educationRepository;
        $this->scienceDegreeRepository = $scienceDegreeRepository;
        $this->qualificationRepository = $qualificationRepository;
        $this->specialityRepository    = $specialityRepository;
        $this->revisionRepository      = $revisionRepository;
    }

    /**
     * Saves or updates employee-related data, including EmployeeRequest, Party, and associated details.
     *
     * @param array $formData
     * @param LegalEntity $legalEntity
     * @param BaseEmployee|EmployeeRequest|null $employeeModel
     * @param string|null $employeeUUID
     * @return void
     * @throws Exception
     */
    public function saveEmployeeData(
        array                        $formData,
        LegalEntity                  $legalEntity,
        BaseEmployee|EmployeeRequest $employeeModel = null,
        ?string                      $employeeUUID = null
    ): void
    {
        DB::beginTransaction();
        try {
            $employeeRequestData = [
                'uuid'              => Arr::get($formData, 'uuid', null),
                'legal_entity_uuid' => Arr::get($formData, 'legal_entity_uuid', $legalEntity->uuid),
                'legal_entity_id'   => Arr::get($formData, 'legal_entity_id', $legalEntity->id),
                'user_id'           => Arr::get($formData, 'user_id', auth()->id()),
                'position'          => Arr::get($formData, 'position'),
                'status'            => Arr::get($formData, 'status', 'NEW'),
                'employee_type'     => Arr::get($formData, 'employee_type'),
                'start_date'        => Arr::get($formData, 'start_date'),
                'end_date'          => Arr::get($formData, 'end_date'),
                'inserted_at'       => now()->toIso8601String(),
                'division_uuid'     => Arr::get($formData, 'division_uuid', null),
                'division_id'       => Arr::get($formData, 'division_uuid') ? Division::where(
                    'uuid',
                    Arr::get(
                        $formData,
                        'division_uuid'
                    )
                )->first()?->id : null,
            ];

            $partyData = Arr::get($formData, 'party', []);
            $partyData = Arr::toSnakeCase($partyData);

            $phonesData = $partyData['phones'] ?? [];
            unset($partyData['phones']);

            $documentsData = $partyData['documents'] ?? [];
            unset($partyData['documents']);

            $doctorData = Arr::get($formData, 'doctor', []);

            $employee = $this->createOrUpdate(
                $employeeRequestData,
                $employeeModel ?? new EmployeeRequest(),
                $legalEntity
            );

            $isEmployeRequest = $employee instanceof EmployeeRequest;

            $party = null;
            $email = $partyData['email'] ?? null;
            $primaryPhone = null;

            foreach ($phonesData as $phone) {
                if (($phone['type'] ?? '') === 'MOBILE') {
                    $primaryPhone = $phone['number'];
                    break;
                }
            }

            if ($email || $primaryPhone) {
                $query = Party::query();
                if ($email) {
                    $query->where('email', $email);
                }
                if ($primaryPhone) {
                    $query->orWhereHas('phones', function($q) use ($primaryPhone) {
                        $q->where('number', $primaryPhone);
                    });
                }
                $party = $query->first();
            }

            if ($party) {
                $this->partyRepository->update($party, $partyData);
            } else {
                $party = $this->partyRepository->create($partyData);
            }

            // Add documents for Party
            $this->documentRepository->syncDocuments($party, $documentsData);

            // Add phones for Party
            $this->phoneRepository->syncPhones($party, $phonesData);

            // Add educations
            $this->educationRepository->addEducations($employee, $doctorData['educations'] ?? []);

            // Add science degrees
            $this->scienceDegreeRepository->addScienceDegrees($employee, $doctorData['science_degree'] ?? []);

            // Add qualifications
            $this->qualificationRepository->addQualifications($employee, $doctorData['qualifications'] ?? []);

            // Add specialities
            $this->specialityRepository->addSpecialities($employee, $doctorData['specialities'] ?? []);

            // Attach employee to Party
            $party->employees()->save($employee);

            // Create revision entry
            if ($isEmployeRequest) {
                $revisionData = [
                    'employeeRequestData' => $employeeRequestData,
                    'partyData'           => $partyData,
                    'documentsData'       => $documentsData,
                    'phonesData'          => $phonesData,
                    'doctorData'          => $doctorData ?? []
                ];

                $this->revisionRepository->saveRevision($employee, [
                    'data'   => json_encode($revisionData, JSON_THROW_ON_ERROR),
                    'status' => Revision::STATUS_PENDING,
                ]);
            }

            DB::commit();

        } catch (Exception $err) {
            DB::rollBack();
            throw new Exception(__('Create Employee Error') . ' : ' . $err->getMessage());
        }
    }

    protected function createOrUpdate(array $data, Employee|EmployeeRequest|null $model, LegalEntity $legalEntity): Employee|EmployeeRequest
    {
        $modelData = [
            'uuid' => $data['uuid'] ?? null,
            'legal_entity_uuid' => $data['legal_entity_uuid'],
            'position' => $data['position'],
            'status' => $data['status'],
            'employee_type' => $data['employee_type'],
            'inserted_at' => $data['inserted_at'],
            'user_id' => $data['user_id'],
            'legal_entity_id' => $data['legal_entity_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'division_uuid' => $data['division_uuid'] ?? null,
            'division_id' => $data['division_id'] ?? null,
        ];

        $uuid = $modelData['uuid'];
        $employee = null;

        if ($uuid) {
            $employee = $model::where('uuid', $uuid)->first();
            if ($employee) {
                $employee->update($modelData);
            }
        }

        if (!$employee) {
            $employee = $model::create($modelData);
        }

        $employee->legalEntity()->associate($legalEntity);
        $employee->save();

        return $employee;
    }

    /**
     * Save employee data to the database
     *
     * @param User $user
     * @param Party|null $party
     * @param array $employeeData Data received from request to eHealth (GetEmployeesList|GetEmployeeDetails)
     * @param string $authUserUUID
     *
     * @return bool
     */
    protected function updateEmployeeDataAtFirstLogin(User|null $user, Party|null $party, array $employeeData, string $authUserUUID, string $legalEntityUUID): void
    {
        $employeeResponse = schemaService()->setDataSchema($employeeData, app(EmployeeApi::class))
            ->responseSchemaNormalize()
            ->replaceIdsKeysToUuid(['id', 'legalEntityId', 'divisionId', 'partyId'])
            ->snakeCaseKeys(true)
            ->getNormalizedData();

        $legalEntity = !empty($user) ? $user->legalEntity : LegalEntity::where('uuid', $legalEntityUUID)->first();

        $employeeResponse['division_id'] = isset($employeeResponse['division_uuid'])
            ? Division::where('uuid', $employeeResponse['division_uuid'])->first()?->id
            : null;

        // Update Party uuid because it is hasn't actual value in the employeeRequest
        if (!empty($party) && $party->uuid !== $employeeResponse['party']['uuid']) {
            $party->uuid = $employeeResponse['party']['uuid'];

            $party->save();
        }

        if ($user && empty($employeeData['userId'])) {
            $employeeResponse['user_id'] = $user->id;

            $user->uuid = $authUserUUID;

            $user->save();
        }

        $this->saveEmployeeData($employeeResponse, $legalEntity, new Employee());
    }

    /**
     * Authenticate new OWNER and save data to the database
     * Also check if the other employees is already exists in the system and save its data too
     *
     * @param EmployeeRequest $employeeRequest Only EmployeeRequest type because up to now we should have only the EmployeeRequest for the OWNER
     * @param User $user
     * @param string $authUserUUID
     *
     * @return bool
     */
    public function authenticateNewOwner(EmployeeRequest $employeeRequest, User $ownerUser, string $authUserUUID): bool|RedirectResponse
    {
        try {
            DB::transaction(function() use($employeeRequest, $ownerUser, $authUserUUID) {

                $legalEntityUUID = $ownerUser->legalEntity->uuid;

                // List of the users (employees) belongs to the same legal entity
                $employeeList = EmployeeApi::getEmployeesList($legalEntityUUID);

                $employeeData = [];

                $employeePosition = $employeeRequest->position;

                /*
                 * Variable to store OWNER's Party ID.
                 * Need to determine all employees belongs to OWNER.
                 */
                $ownerPartyUUID = null;

                // $employeList already contains 'OWNER' as first element
                foreach ($employeeList as $employee) {
                    $employeeData = $employee;

                    // Used only for OWNER's employee
                    $user = null;

                    if (($employee['position'] === $employeePosition  && $employee['employee_type'] === 'OWNER') || $employeeData['party']['id'] === $ownerPartyUUID) {

                        $user = $ownerUser;

                        $employeeResponse = EmployeeApi::getEmployeeData($employee['id']);

                        $employeeValidator = $this->validateEmployeeData($employeeResponse);

                        /** @var \Illuminate\Contracts\Validation\Validator $employeeValidator */
                        if($employeeValidator->fails()) {
                            Log::error(__('auth.login.error.validation.employee_data', [], 'en'), ['errors' => $employeeValidator->errors()]);

                            throw new Exception($employeeValidator->errors());
                        }

                        $employeeData = $employeeValidator->validated();

                        // This need because Party UUID for newly created EmployeeRequest may be NULL
                        $ownerPartyUUID = $employeeData['party']['id'];

                        $employeeData['party']['email'] = $user->email;

                        if ($employeeData['employee_type'] !== 'OWNER') {
                            $user->assignRole($employeeData['employee_type']);
                        }
                    }

                    $employeeData['legal_entity_id'] = $employeeData['legal_entity']['id'];
                    $employeeData['inserted_at'] = Carbon::now()->format('Y-m-d');
                    $employeeData['updated_at'] = Carbon::now()->format('Y-m-d');

                    $party = $user
                        ? $employeeRequest->party
                        : Party::where('uuid', $employeeData['party']['id'])->first();

                    if ($employeeData['status'] === 'DISMISSED') {
                        $party = null;
                    }

                    if (is_array($employeeData['division']) &&  isset($employeeData['division']['id'])) {
                        $employeeData['division_id'] = $employeeData['division']['id'];
                    }

                    $this->updateEmployeeDataAtFirstLogin($user, $party, $employeeData, $authUserUUID, $legalEntityUUID);
                }

                // Update status of EmployeeRequest and the time of update
                $this->updateEmployeeRequestStatus($employeeRequest, 'APPROVED', $employeeRequest->updatedAt);

                if($employeeRequest->revision) {
                    $employeeRequest->revision->setApplied();
                }
            });
        } catch (Exception $err) {
            Log::error('New Owner: ' . __('auth.login.error.data_saving', [], 'en'), ['error' => $err->getMessage()]);

            return false;
        }

        return true;
    }

    /**
     * Authenticate new employee and save data to the database
     *
     * @param Employee $employee Only Employee type because up to now we should have all the data for employees
     * @param User $user
     * @param string $authUserUUID
     *
     * @return bool
     * TODO: test after creating an employee will works
     */
    public function authenticateNewEmployees(string $legalEntityUUID, User $user, string $authUserUUID): bool
    {
        // Get all employees except owner
        $employees = Employee::employeeInstance($user->id, $legalEntityUUID, ['OWNER'])->get();

        if (!$employees->count()) {
            return true;
        }

        try {
            DB::transaction(function() use($employees, $user, $legalEntityUUID, $authUserUUID) {
                foreach ($employees as $employee) {
                    if ($employee->party->email) {
                        continue;
                    }

                    $employeeResponse = EmployeeApi::getEmployeeData($employee->uuid);

                    $employeeValidator = $this->validateEmployeeData($employeeResponse);

                    /** @var \Illuminate\Contracts\Validation\Validator $employeeValidator */
                    if($employeeValidator->fails()) {
                        Log::error(__('auth.login.error.validation.employee_data', [], 'en'), ['errors' => $employeeValidator->errors()]);

                        throw new Exception($employeeValidator->errors());
                    }

                    $employeeData = $employeeValidator->validated();

                    $employeeData['party']['email'] = $user->email;

                    if (isset($employeeData['division']['id'])) {
                        $employeeData['division_id'] = $employeeData['division']['id'];
                    }

                    $employeeData['legal_entity_id'] = $employeeData['legal_entity']['id'];
                    $employeeData['inserted_at'] = Carbon::now()->format('Y-m-d');
                    $employeeData['updated_at'] = Carbon::now()->format('Y-m-d');

                    $this->updateEmployeeDataAtFirstLogin($user,  $employee->party, $employeeData, $authUserUUID, $legalEntityUUID);
                }
            });
        } catch (Exception $err) {
            Log::error('New Owner: ' . __('auth.login.error.data_saving', [], 'en'), ['error' => $err->getMessage()]);

            return false;
        }

        return true;
    }

    /**
     * Authenticate new employee and save data to the database
     *
     * @param Employee $employee Only Employee type because up to now we should have all the data for employees
     * @param User $user
     * @param string $authUserUUID
     *
     * @return bool
     * TODO: test after creating an employee will works
     */
    public function checkForEmployeeUpdate(string $legalEntityUUID, User $user, string $authUserUUID): bool
    {
        $employeeRoles = $user->getRoleNames()->toArray();

        $employeeRequests = EmployeeRequest::employeeInstance($user->id, $legalEntityUUID, $employeeRoles, true)->where('status', 'NEW')->get();

        if (!$employeeRequests->count()) {
            return true;
        }

        try {
            DB::transaction(function() use($employeeRequests, $user, $legalEntityUUID, $authUserUUID) {
                $updatedAt = '1970-01-01T00:00:00.000000Z';

                foreach ($employeeRequests as $employeeRequest) {

                    $employeeRequestResponse = EmployeeApi::_getRequestById($employeeRequest->uuid);

                    $employeeRequestValidator = $this->validateEmployeeRequestData($employeeRequestResponse['data']);

                    /** @var \Illuminate\Contracts\Validation\Validator $employeeRequestValidator */
                    if ($employeeRequestValidator->fails()) {
                        Log::error(__('auth.login.error.validation.employee_request_data', [], 'en'), ['errors' => $employeeRequestValidator->errors()]);

                        throw new Exception($employeeRequestValidator->errors());
                    }

                    $employeeRequestData = $employeeRequestValidator->validated();

                    // Just skip request if nothing changes
                    if ($employeeRequestData['status'] === 'NEW') {
                        continue;
                    }

                    $this->updateEmployeeRequestStatus($employeeRequest, $employeeRequestData['status'], $employeeRequestData['updated_at']);

                    $currentRequestDate = Carbon::parse($employeeRequestData['updated_at']);
                    $proceddedRequestDate = Carbon::parse($updatedAt);

                    // Skip all EmployeeRequests that has wrong status (ex. EXPIRED) or older than last proceeded one
                    if ($employeeRequestData['status'] !== 'APPROVED' || $currentRequestDate->lt($proceddedRequestDate)) {
                        $employeeRequest->revision?->setOutdated();

                        continue;
                    }

                    $updatedAt = $employeeRequestData['updated_at'];

                    $employeeData = $this->getRevisionEmployeeData($employeeRequest);

                    $party = $employeeRequest->employee->party;

                    $this->updateEmployeeDataAtFirstLogin($user, $party, $employeeData, $authUserUUID, $legalEntityUUID);

                    $employeeRequest->revision->setApplied();
                }
            });
        } catch (Exception $err) {
            Log::error('New Owner: ' . __('auth.login.error.data_saving', [], 'en'), ['error' => $err->getMessage()]);

            return false;
        }

        return true;
    }

    /**
     * Update EmployeeRequest status and updated_at attributes
     * It mandatory for next employee updates to avoid repeat finished ones
     *
     * @param \App\Models\Employee\EmployeeRequest $employeeRequest
     * @param string $status
     * @param string $updatedAt
     *
     * @return void
     */
    protected function updateEmployeeRequestStatus(EmployeeRequest $employeeRequest, string $status, string $updatedAt): void
    {
        $employeeRequest->status = $status;
        $employeeRequest->appliedAt = $updatedAt;

        $employeeRequest->save();
    }

    /**
     * Prepare EmployeeData for employee update based on data stored in 'revisions' table
     *
     * @param \App\Models\Employee\EmployeeRequest $employeeRequest
     *
     * @return array
     */
    protected function getRevisionEmployeeData(EmployeeRequest $employeeRequest): array
    {
        $revisionData = json_decode($employeeRequest->revision->data, true);

        $employee = $employeeRequest->employee;

        $employeeData = collect($employee->toArray())
            ->mapWithKeys(fn ($value, $key) => [Str::snake($key) => $value])
            ->toArray();

        unset($employeeData['user']);

        $employeeData['id'] = $employeeData['uuid'];
        $employeeData['legal_entity_id'] = $employeeData['legal_entity_uuid'];
        $employeeData['party']['id'] = $employeeData['party']['uuid'];
        $employeeData['party'] = array_merge($employeeData['party'], $revisionData['party']);
        $employeeData['party']['documents'] =  $revisionData['documents'];
        $employeeData['party']['phones'] = $revisionData['phones'];

        if (isset($employeeData['division']['id'])) {
            $employeeData['division_id'] = $employeeData['division']['id'];
        }

        $employeeData['updated_at'] = Carbon::now()->format('Y-m-d');

        return $employeeData;
    }

    /**
     * Check employee details $response schema for errors.
     *
     * @return array Returned only specified fields
     */
    protected function validateEmployeeData(array $data): ResponseValidator
    {
        return Validator::make($data, [
            'division' => 'nullable|array',
            'division.id' => 'required_with:division|string',
            'division.name' => 'required_with:division|string',
            'division.legal_entity_id' => 'nullable|string',
            'employee_type' => 'required|string',
            'end_date' => 'nullable|string',
            'id' => 'required|string',
            'is_active' => 'required|bool',
            'legal_entity' => 'required|array',
            'legal_entity.id' => 'required|string',
            'party' => 'required|array',
            'party.id' => 'required|string',
            'party.first_name' => 'required|string',
            'party.last_name' => 'required|string',
            'party.second_name' => 'nullable|string',
            'party.no_tax_id' => 'nullable|bool',
            'party.gender' => 'nullable|string',
            'party.verification_status' => 'required|string',
            'party.tax_id' => 'nullable|string',
            'party.birth_date' => 'nullable|string',
            'party.documents' => 'nullable|array',
            'party.phones' => 'nullable|array',
            'party.phones.*.type' => 'required_with:party.phones|string',
            'party.phones.*.number' => 'required_with:party.phones|string',
            'party.working_experience' => 'nullable',
            'party.about_myself' => 'nullable',
            'start_date' => 'required|string',
            'status' => 'required|string',
            'position' => 'required|string',
            'doctor' => 'nullable|array'
        ]);
    }

    /**
     * Check employee response details $response schema for errors.
     *
     * @return array Returned only specified fields
     */
    protected function validateEmployeeRequestData(array $data): ResponseValidator
    {
        return Validator::make($data, [
            'division' => 'nullable|array',
            'division.id' => 'required_with:division|string',
            'division.name' => 'required_with:division|string',
            'division.legal_entity_id' => 'nullable|string',
            'employee_type' => 'required|string',
            'id' => 'required|string',
            'legal_entity_id' => 'required|string',
            'inserted_at' => 'required|string',
            'party' => 'required|array',
            'party.birth_date' => 'nullable|string',
            'party.email' => 'nullable|string',
            'party.first_name' => 'required|string',
            'party.last_name' => 'required|string',
            'party.second_name' => 'nullable|string',
            'party.no_tax_id' => 'nullable|bool',
            'party.gender' => 'nullable|string',
            'party.tax_id' => 'nullable|string',
            'party.documents' => 'nullable|array',
            'party.phones' => 'nullable|array',
            'party.phones.*.type' => 'required_with:party.phones|string',
            'party.phones.*.number' => 'required_with:party.phones|string',
            'status' => 'required|string',
            'position' => 'required|string',
            'start_date' => 'required|string',
            'end_date' => 'nullable|string',
        ]);
    }
}
