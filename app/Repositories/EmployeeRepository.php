<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\LegalEntity;
use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

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
    public function createOrUpdate($data): Employee
    {
        return Employee::updateOrCreate(
            [
                'uuid' => $data['id']
            ],
            $data
        );
    }

    public function saveEmployeeData($request, LegalEntity $legalEntity): ?Employee //TODO: Global LegalEntity model
    {
        DB::beginTransaction();

        try {
            // Create or update User
            if (isset($request['party']['email'])) {
                $user = $this->userRepository->createIfNotExist($request['party']['email'], $request['employee_type']);
                $user->legalEntity()->associate($legalEntity);
                $user->save();
            }

            // Create or update Employee
            $employee = $this->createOrUpdate($request);
            $employee->legalEntity()->associate($legalEntity);

            // Create or update Party
            $party = $this->partyRepository->createOrUpdate($request['party']);

            // Add documents for Party
            $this->documentRepository->addDocuments($party, $request['party']['documents'] ?? []);

            // Add phones for Party
            $this->phoneRepository->addPhones($party, $request['party']['phones'] ?? []);

            // Add educations
            $this->educationRepository->addEducations($employee, $request['doctor']['educations'] ?? []);

            // Add science degrees
            $this->scienceDegreeRepository->addScienceDegrees($employee, $request['doctor']['science_degrees'] ?? []);

            // Add qualifications
            $this->qualificationRepository->addQualifications($employee, $request['doctor']['qualifications'] ?? []);

            // Add specialities
            $this->specialityRepository->addSpecialities($employee, $request['doctor']['specialities'] ?? []);

            // Bind employee to Party
            $party->employees()->save($employee);

            // Commit the transaction
            DB::commit();

            return $employee;
        } catch (Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            return null;
        }
    }

}
