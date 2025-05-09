<?php

namespace App\Repositories;

use App\Classes\eHealth\Api\EmployeeApi;
use App\Models\Division;
use Log;
use Exception;
use App\Models\Employee\BaseEmployee;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRequest;
use App\Models\LegalEntity;
use App\Models\Relations\Party;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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


    public function __construct(
        UserRepository $userRepository,
        PartyRepository $partyRepository,
        PhoneRepository $phoneRepository,
        DocumentRepository $documentRepository,
        EducationRepository $educationRepository,
        ScienceDegreeRepository $scienceDegreeRepository,
        QualificationRepository $qualificationRepository,
        SpecialityRepository $specialityRepository
    ) {
        $this->userRepository = $userRepository;
        $this->partyRepository = $partyRepository;
        $this->phoneRepository = $phoneRepository;
        $this->documentRepository = $documentRepository;
        $this->educationRepository = $educationRepository;
        $this->scienceDegreeRepository = $scienceDegreeRepository;
        $this->qualificationRepository = $qualificationRepository;
        $this->specialityRepository = $specialityRepository;
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

    public function saveEmployeeData($request, LegalEntity $legalEntity,  Employee|EmployeeRequest $employeeModel): Employee|EmployeeRequest|null
    {
       try {
            // Create or update User
            if (isset($request['party']['email']) && !empty($request['party']['email'])) {
                $this->userRepository->createIfNotExist($request['party'], $request['employee_type'], $legalEntity);
            }
            // Create or update Employee
            $employee = $this->createOrUpdate($request,$employeeModel,$legalEntity);

            // Create or update Party
            $party = $this->partyRepository->createOrUpdate($request['party']);

            // Add documents for Party
            $this->documentRepository->addDocuments($party, $request['party']['documents'] ?? []);

            // Add phones for Party
            $this->phoneRepository->addPhones($party, $request['party']['phones'] ?? []);

            // Add educations
            $this->educationRepository->addEducations($employee, $request['doctor']['educations'] ?? []);

            // Add science degrees
            $this->scienceDegreeRepository->addScienceDegrees($employee, $request['doctor']['science_degree'] ?? []);

            // Add qualifications
            $this->qualificationRepository->addQualifications($employee, $request['doctor']['qualifications'] ?? []);

            // Add specialities
            $this->specialityRepository->addSpecialities($employee, $request['doctor']['specialities'] ?? []);

            // Bind employee to Party
            $party->employees()->save($employee);

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
    protected function updateEmployeeData(User|null $user, Party|null $party, array $employeeData, string $authUserUUID, string $legalEntityUUID): bool
    {
        $employeeResponse = schemaService()->setDataSchema($employeeData, app(EmployeeApi::class))
            ->responseSchemaNormalize()
            ->replaceIdsKeysToUuid(['id', 'legalEntityId', 'divisionId', 'partyId'])
            ->snakeCaseKeys(true)
            ->getNormalizedData();

        if ($user) {
            $employeeResponse['user_id'] = $user->id;

            $user->uuid = $authUserUUID;
        }

        $legalEntity = !empty($user) ? $user->legalEntity : LegalEntity::where('uuid', $legalEntityUUID)->first();

        $employeeResponse['division_id'] = isset($employeeResponse['division_uuid'])
            ? Division::where('uuid', $employeeResponse['division_uuid'])->first()->id
            : null;

        try {
            DB::transaction(function() use($employeeResponse, $user, $party, $legalEntity) {
                // Update Party uuid because it is hasn't actual value in the employeeRequest
                if (!empty($party) && $party->uuid !== $employeeResponse['party']['uuid']) {
                    $party->uuid = $employeeResponse['party']['uuid'];

                    $party->save();
                }

                if ($user) {
                    $user->save();
                }

                $this->saveEmployeeData($employeeResponse, $legalEntity, new Employee());
            });
        } catch (Exception $err) {
            Log::error(__('auth.login.error.data_saving'), ['error' => $err->getMessage()]);

            return false;
        }

        return true;
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
        $employeePosition = $employeeRequest->position;
        $legalEntityUUID = $ownerUser->legalEntity->uuid;
        /*
         * Variable to store OWNER's Party ID.
         * Need to determine all employees belongs to OWNER.
        */
        $ownerPartyUUID = null;

        // List of the users (employees) belongs to the same legal entity
        $employeeList = EmployeeApi::getEmployeesList($legalEntityUUID);

        $employeeData = [];

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
                    Log::error(__('auth.login.error.vlidation.employee_data', [], 'en'), ['errors' => $employeeValidator->errors()]);

                    return false;
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

            $party = $user ? $employeeRequest->party : Party::where('uuid', $employeeData['party']['id'])->first();

            if ($employeeData['status'] === 'DISMISSED') {
                $party = null;
            }

            if (!$this->updateEmployeeData($user, $party, $employeeData, $authUserUUID, $legalEntityUUID)) {
                return false;
            }
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
        $employees = Employee::employeeInstance($user->id, $legalEntityUUID, ['OWNER'])->get();

        if (!$employees->count()) {
            return true;
        }

        foreach ($employees as $employee) {
            if ($employee->party->email) {
                continue;
            }

            $employeeResponse = EmployeeApi::getEmployeeData($employee->uuid);

            $employeeValidator = $this->validateEmployeeData($employeeResponse);

            /** @var \Illuminate\Contracts\Validation\Validator $employeeValidator */
            if($employeeValidator->fails()) {
                Log::error(__('auth.login.error.vlidation.employee_data', [], 'en'), ['errors' => $employeeValidator->errors()]);

                return false;
            }

            $employeeData = $employeeValidator->validated();

            $employeeData['party']['email'] = $user->email;

            if (isset($employeeData['division']['id'])) {
                $employeeData['division_id'] = $employeeData['division']['id'];
            }

            $employeeData['legal_entity_id'] = $employeeData['legal_entity']['id'];
            $employeeData['inserted_at'] = Carbon::now()->format('Y-m-d');
            $employeeData['updated_at'] = Carbon::now()->format('Y-m-d');

            if (!$this->updateEmployeeData($user,  $employee->party, $employeeData, $authUserUUID, $legalEntityUUID)) {
                return false;
            }
        }

        return true;
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
}
