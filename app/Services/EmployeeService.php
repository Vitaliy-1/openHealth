<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LegalEntity;
use App\Repositories\DocumentRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\PartyRepository;
use App\Repositories\PhoneRepository;
use App\Repositories\UserRepository;

class EmployeeService
{
    protected ?object $legalEntity;
    protected ?object $userRepository;
    protected ?object $employeeRepository;
    protected ?object $partyRepository;
    protected ?object $phoneRepository;
    protected ?object $documentRepository;

    public function __construct(
        LegalEntity        $legalEntity,
        UserRepository     $userRepository,
        EmployeeRepository $employeeRepository,
        PartyRepository    $partyRepository,
        PhoneRepository    $phoneRepository,
        DocumentRepository $documentRepository,
    )
    {
        $this->legalEntity = $legalEntity;
        $this->userRepository = $userRepository;
        $this->employeeRepository = $employeeRepository;
        $this->partyRepository = $partyRepository;
        $this->phoneRepository = $phoneRepository;
        $this->documentRepository = $documentRepository;
    }

    /**
     * @param $request
     * @param LegalEntity $legalEntity
     * @return object|null
     */
    public function saveEmployeeData($request, LegalEntity $legalEntity): ?Employee //TODO: Global LegalEntity model
    {
        //Create or Update User
        if (isset($request['party']['email'])) {
            $user = $this->userRepository->createIfNotExist($request['party']['email'], $request['employee_type']);
            $user->legalEntity()->associate($this->legalEntity);
            $user->save();
        }

        //Create or Update Employee
        $employee = $this->employeeRepository->createOrUpdate($request);
        $employee->legalEntity()->associate($legalEntity);

        //Create or Update Party
        $party = $this->partyRepository->createOrUpdate($request['party']);

        //Create or Update Phone Party
            $this->documentRepository->addPartyDocuments($party, $request['party']['documents'] ?? []);

        //Create or Update Phone Party
        $this->phoneRepository->addPartyPhones($party, $request['party']['phones'] ?? []);

        // Binding the employee to the batch
        $party->employees()->save($employee);

        return $employee;
    }
}
