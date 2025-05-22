<?php

namespace App\Repositories;

use Log;
use Exception;
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
    /**
     * @param  LegalEntity  $legalEntity
     * @param  UserRepository  $userRepository
     * @param  EmployeeRepository  $employeeRepository
     * @param  PartyRepository  $partyRepository
     * @param  PhoneRepository  $phoneRepository
     * @param  DocumentRepository  $documentRepository
     * @param  EducationRepository  $educationRepository
     * @param  ScienceDegreeRepository  $scienceDegreeRepository
     * @param  QualificationRepository  $qualificationRepository
     * @param  SpecialityRepository  $specialityRepository
     * @param  RevisionRepository $revisionRepository
     */

    protected ?UserRepository $userRepository;
    protected ?EmployeeRepository $employeeRepository;
    protected ?PartyRepository $partyRepository;
    protected ?PhoneRepository $phoneRepository;
    protected ?DocumentRepository $documentRepository;
    protected ?EducationRepository $educationRepository;
    protected ?ScienceDegreeRepository $scienceDegreeRepository;
    protected ?QualificationRepository $qualificationRepository;
    protected SpecialityRepository $specialityRepository;
    protected  RevisionRepository $revisionRepository;


    public function __construct(
        UserRepository $userRepository,
        PartyRepository $partyRepository,
        PhoneRepository $phoneRepository,
        DocumentRepository $documentRepository,
        EducationRepository $educationRepository,
        ScienceDegreeRepository $scienceDegreeRepository,
        QualificationRepository $qualificationRepository,
        SpecialityRepository $specialityRepository,
        RevisionRepository $revisionRepository
    ) {
        $this->userRepository = $userRepository;
        $this->partyRepository = $partyRepository;
        $this->phoneRepository = $phoneRepository;
        $this->documentRepository = $documentRepository;
        $this->educationRepository = $educationRepository;
        $this->scienceDegreeRepository = $scienceDegreeRepository;
        $this->qualificationRepository = $qualificationRepository;
        $this->specialityRepository = $specialityRepository;
        $this->revisionRepository = $revisionRepository;
    }

    /**
     * @param $data
     * @return Employee
     */
    public function createOrUpdate($data, Employee|EmployeeRequest $employeeModel, LegalEntity $legalEntity): BaseEmployee
    {
        $employee =  $employeeModel::updateOrCreate(
            [
                'uuid' => $data['uuid'] ?? '',
            ],
            $data
        );

        $employee->legalEntity()->associate($legalEntity);

        return  $employee;

    }

    public function saveEmployeeData($response, LegalEntity $legalEntity,  Employee|EmployeeRequest $employeeModel, ?string $employeeUUID = null): Employee|EmployeeRequest|null
    {
       try {
            $partyData = $response['party'];
            unset($response['party']);

            if (!empty($partyData['phones'])) {
                $phonesData = $partyData['phones'];
                unset($partyData['phones']);
            }

            if (!empty($partyData['documents'])) {
                $documentsData = $partyData['documents'];
                unset($partyData['documents']);
            }

            if(!empty($response['doctor'])) {
                $doctorData = $response['doctor'];
                unset($response['doctor']);
            }

            unset($response['updated_at']);

            // Create or update User depends on it's Email (if exist email therefore user can be created too)
            if (isset($partyData['email']) && !empty($partyData['email'])) {
                $this->userRepository->createIfNotExist($partyData, $response['employee_type'], $legalEntity);
            }

            // Create or update Employee or EmployeeRequest
            $employee = $this->createOrUpdate($response,$employeeModel,$legalEntity);

            $isEmployeRequest = $employee instanceof EmployeeRequest;

            $employeeInstance = Employee::where('uuid', $employeeUUID)?->first();

            $alreadyExistParty = $employeeInstance?->party;

            // Create or update Party
            $party = $alreadyExistParty ?? $this->partyRepository->createOrUpdate($partyData);

            if ($isEmployeRequest) {
                optional($employeeInstance, fn($instance) => $employee->employee()->associate($instance));
            }

            /**
             * If $alreadyExistParty == null it only means that EmployeeRequest expect to create through creation of the LegalEntity
             * Because if $employee is EmployeeRequest the data below mustn't be changed until valid user approved this changes.
             * And therefore, if $employee is Employee the data should be updated or created
             */
            if (!$isEmployeRequest || !$alreadyExistParty) {
                // Add documents for Party
                $this->documentRepository->syncDocuments($party, $documentsData ?? []);

                // Add phones for Party
                $this->phoneRepository->syncPhones($party, $phonesData ?? []);

                // Add educations
                $this->educationRepository->addEducations($employee, $doctorData['educations'] ?? []);

                // Add science degrees
                $this->scienceDegreeRepository->addScienceDegrees($employee, $doctorData['science_degree'] ?? []);

                // Add qualifications
                $this->qualificationRepository->addQualifications($employee, $doctorData['qualifications'] ?? []);

                // Add specialities
                $this->specialityRepository->addSpecialities($employee, $doctorData['specialities'] ?? []);
            }

            // Bind employee to Party
            $party->employees()->save($employee);

            // Here is should be created record in the revisions table depends one the $employeeModel and it's id
            if ($isEmployeRequest) {
                $responseData = [
                    'response' => $response,
                    'party' => $partyData,
                    'documents' => $documentsData,
                    'phones' => $phonesData,
                    'doctor' => $doctorData ?? []
                ];

                $this->revisionRepository->saveRevision($employee, [
                    'data' => json_encode($responseData),
                    'status' => Revision::STATUS_PENDING,
                ]);
            }

            return $employee;
       } catch (Exception $err) {
            throw new Exception(__('Create Employee Error') . ' : ' . $err->getMessage());
       }
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
            'updated_at' => 'required|string',
        ]);
    }
}
