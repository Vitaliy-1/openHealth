<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Arr;
use Exception;
use App\Models\User;
use App\Models\LegalEntity;
use App\Models\Relations\Party;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Employee\BaseEmployee;
use App\Enums\Employee\RequestStatus;
use App\Enums\Employee\RevisionStatus;
use App\Models\Employee\EmployeeRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use App\Classes\eHealth\Api\Employee as ApiEmployee;
use Throwable;

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
        UserRepository               $userRepository,
        PartyRepository              $partyRepository,
        PhoneRepository              $phoneRepository,
        DocumentRepository           $documentRepository,
        EducationRepository          $educationRepository,
        ScienceDegreeRepository      $scienceDegreeRepository,
        QualificationRepository      $qualificationRepository,
        SpecialityRepository         $specialityRepository,
        RevisionRepository           $revisionRepository,
        private readonly ApiEmployee $employeeApi,
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
     * Creates or updates an employee-related model based on UUID.
     * This method is designed to be called ONLY when $employeeModel is NOT null.
     *
     * @param array                    $data          Data for the model.
     * @param Employee|EmployeeRequest $employeeModel The model class to update/create.
     * @param LegalEntity              $legalEntity   The legal entity to associate with.
     *
     * @return BaseEmployee The created or updated model.
     * @throws InvalidArgumentException If $employeeModel is null (should not happen with correct usage).
     */
    public function createOrUpdate(array $data, Employee|EmployeeRequest $employeeModel, LegalEntity $legalEntity): BaseEmployee
    {

        $employee = $employeeModel->updateOrCreate(
            [
                'uuid' => $data['uuid'] ?? '',
            ],
            $data
        );
        $employee->legalEntity()->associate($legalEntity);

        return $employee;
    }

    /**
     * Saves or updates employee-related data, including EmployeeRequest, Party, and associated details.
     *
     * @param array                         $response
     * @param LegalEntity                   $legalEntity
     * @param Employee|EmployeeRequest|null $employeeModel The model class to create/update (can be null for a new request).
     * @param string|null                   $employeeUUID  UUID of an existing Employee, if this is an EmployeeRequest that updates.
     *
     * @return BaseEmployee
     * @throws Exception
     */
    public function store(
        array $response,
        LegalEntity $legalEntity,
        Employee|EmployeeRequest|null $employeeModel,
        ?string $employeeUUID = null
    ): BaseEmployee {
        try {
            $partyData = $response['party'] ?? [];
            unset($response['party']);

            if (!empty($partyData['phones'])) {
                $phonesData = $partyData['phones'];
                unset($partyData['phones']);
            }
            if (!empty($partyData['documents'])) {
                $documentsData = $partyData['documents'];
                unset($partyData['documents']);
            }
            if (!empty($response['doctor'])) {
                $doctorData = $response['doctor'];
                unset($response['doctor']);
            }

            unset($response['updated_at']);

            $user = null;

            if (!empty($partyData['email'])) {
                $this->userRepository->createIfNotExist($partyData, $response['employee_type']);
            }

            $employee = $this->createOrUpdate($response, $employeeModel, $legalEntity);
            $isEmployeeRequest = $employee instanceof EmployeeRequest;
            $employeeInstance = Employee::where('uuid', $employeeUUID)?->first();
            $alreadyExistParty = $employeeInstance?->party;
            $party = $alreadyExistParty ?? $this->partyRepository->createOrUpdate($partyData);

            if ($isEmployeeRequest) {
                optional($employeeInstance, fn ($instance) => $employee->employee()->associate($instance));
            }

            /**
             * If $alreadyExistParty == null it only means that EmployeeRequest expects to create through creation of the LegalEntity
             * Because if $employee is EmployeeRequest the data below mustn't be changed until a valid user approves these changes.
             * And therefore, if $employee is Employee, the data should be updated or created.
             */
            if (!$isEmployeeRequest || !$alreadyExistParty) {
                // Add documents for Party
                $this->documentRepository->syncDocuments($party, $documentsData ?? []);

                // Add phones for Party
                $this->phoneRepository->syncPhones($party, $phonesData ?? []);

                // Add educations
                $this->educationRepository->syncEducations($employee, $doctorData['educations'] ?? []);

                // Add science degrees
                $this->scienceDegreeRepository->syncScienceDegrees($employee, $doctorData['science_degree'] ?? []);

                // Add qualifications
                $this->qualificationRepository->syncQualifications($employee, $doctorData['qualifications'] ?? []);

                // Add specialities
                $this->specialityRepository->syncSpecialities($employee, $doctorData['specialities'] ?? []);
            }

            $party->employees()->save($employee);

            // Assign party to the user if $user is a new one
            if (!$alreadyExistParty && $user) {
                $user->party()->save($party);
            }

            if ($isEmployeeRequest) {
                $responseData = [
                    'response' => $response,
                    'party' => $partyData,
                    'documents' => $documentsData,
                    'phones' => $phonesData,
                    'doctor' => $doctorData ?? []
                ];

                $this->revisionRepository->saveRevision($employee, [
                    'data' => $responseData,
                    'status' => RevisionStatus::PENDING,
                ]);
            }

            return $employee;

        } catch (Exception $err) {
            Log::error('Create Employee Error: ' . $err->getMessage(), ['exception' => $err]);
            throw new Exception(__('Create Employee Error') . ' : ' . $err->getMessage());
       }
    }

    /**
     * Creates a new EmployeeRequest draft from prepared data.
     * This is a universal method that only handles database persistence.
     *
     * @param array $employeeRequestData The prepared data for the request itself.
     * @param Party $party The associated Party model.
     * @param LegalEntity $legalEntity The associated LegalEntity model.
     * @param User|null $user The associated User model, if found.
     * @return EmployeeRequest
     */
    public function createEmployeeRequestDraft(array $employeeRequestData, Party $party, LegalEntity $legalEntity, ?User $user): EmployeeRequest
    {
        $employeeRequest = new EmployeeRequest();
        $employeeRequest->fill($employeeRequestData);
        $employeeRequest->status = 'NEW';
        $employeeRequest->legalEntity()->associate($legalEntity);
        $employeeRequest->party()->associate($party);

        if ($user) {
            $employeeRequest->user()->associate($user);
        }

        $employeeRequest->save();

        return $employeeRequest;
    }

    /**
     * Finds all pending employee requests for a given user and legal entity.
     * This encapsulates the database query, keeping the service layer clean.
     *
     * @param User $user
     * @param LegalEntity $legalEntity
     * @return Collection
     */
    public function findPendingRequestsForUser(User $user, LegalEntity $legalEntity): Collection
    {
        $user->loadMissing('party');

        return EmployeeRequest::query()
            ->with('revision')
            ->where('legal_entity_id', $legalEntity->id)
            ->whereIn('status', RequestStatus::getStatusesForSync())
            ->whereNotNull('uuid')
            ->where(function ($query) use ($user) {

                $query->where('user_id', $user->id);
            })
            ->get();
    }

    /**
     * @param Employee|int|string $employee the model or identifier (ID or UUID) of the employee to update
     * @param array $party
     * @param array $documents
     * @param array $phones
     * @param array|null $educations
     * @param array|null $specialties
     * @param array|null $qualifications
     * @param array|null $scienceDegrees
     * @return Employee Updated employee
     * @throws Throwable
     */
    public function updateDetails(
        Employee|int|string $employee,
        array $party,
        array $documents,
        array $phones,
        ?array $educations = null,
        ?array $specialities = null,
        ?array $qualifications = null,
        ?array $scienceDegrees = null,

    ): Employee
    {
        $model = $this->getEmployeeByIdentifier($employee);

        if (is_null($model)) {
            throw new InvalidArgumentException('Employee model or valid Employee identifier must be provided');
        }
        DB::transaction(function () use ($model, $party, $documents, $phones, $educations, $specialities, $qualifications, $scienceDegrees) {
            $this->updatePartyByUuid($model, $party);

            $model->party->documents()->delete();
            $model->party->documents()->createMany($documents);

            $model->party->phones()->delete();
            $model->party->phones()->createMany($phones);

            if (!is_null($educations)) {
                $model->educations()->delete();
                $model->educations()->createMany($educations);
            }

            if (!is_null($specialities)) {
                $model->specialities()->delete();
                $model->specialities()->createMany($specialities);
            }

            if (!is_null($qualifications)) {
                $model->qualifications()->delete();
                $model->qualifications()->createMany($qualifications);
            }

            if (!is_null($scienceDegrees)) {
                $model->scienceDegrees()->delete();
                $model->scienceDegrees()->createMany($scienceDegrees);
            }
        });

        return $model;
    }

    /**
     * @param Employee|string|int $employee Employee Model, ID or UUID of the employee
     * @return ?Employee
     */
    public function getEmployeeByIdentifier(Employee|string|int $employee): ?Employee
    {
        if (is_a($employee, Employee::class)) {
            return $employee;
        }

        if (is_int($employee)) {
            return Employee::with('party')->find($employee);
        }

        if (Str::isUuid($employee)) {
            return Employee::with('party')->where('uuid', $employee)->first();
        }

        return null;
    }

    /**
     * The logic behind the party update or create is as follows:
     * 1. Check party by UUID. Possible scenario: the party already exists in the system
     * 2. If user already has a party, update it.
     * 3. If user does not have a party, but there is a party with the same UUID, update it and establish the relation.
     * 4. If neither of the above, create a new party and establish the relation.
     */
    protected function updatePartyByUuid(Employee $model, array $party): void
    {
        $partyUuid = Arr::get($party, 'uuid');
        $partyByUuid = Party::where('uuid', $partyUuid)->first();

        // If the model doesn't have a party and party doesn't exist, create new one. It's a brand-new person
        if (!$partyByUuid && !$model->party) {
            $newParty = new Party($party);
            $newParty->save();
            $model->party()->associate($newParty)->save();

            // If the model doesn't have a related party but the party already exists, update it and relate - the scenario of a new employee with already created person/party
        } else if ($partyByUuid && !$model->party) {
            $model->party()->associate($partyByUuid)->save();

            // The model already has a related party, update it and change the UUID - the case when eHealth creates another party, probably merge scenario
        } else if (!$partyByUuid && $model->party) {
            $model->party()->update($party);
            // Both the model and the party exist, check if they are the same
        } else if ($partyByUuid && $model->party) {

            // uuid is the same, just update
            if ($partyByUuid->uuid == $model->party->uuid) {
                $model->party()->update($party);
            } else {

                // Different uuid, need to merge the results, prioritizing the eHealth data
                $result = array_merge(
                    $model->party()->toArray(),
                    $partyByUuid->toArray()
                );

                $model->party()->update($result);
            }
        }
    }
}
