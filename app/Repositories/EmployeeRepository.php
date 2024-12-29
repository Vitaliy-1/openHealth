<?php

namespace App\Repositories;

use App\Models\Employee\BaseEmployee;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRequest;
use App\Models\LegalEntity;
use Exception;
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

    public function saveEmployeeData($request, LegalEntity $legalEntity ,  Employee|EmployeeRequest $employeeModel): Employee|EmployeeRequest //TODO: Global LegalEntity model
    {
//        DB::beginTransaction();
//        try {
            // Create or update User
            if (isset($request['party']['email']) && !empty($request['party']['email'])) {
                $this->userRepository->createIfNotExist($request['party'], $request['employeeType'], $legalEntity);
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
            $this->scienceDegreeRepository->addScienceDegrees($employee, $request['doctor']['scienceDegree'] ?? []);

            // Add qualifications
            $this->qualificationRepository->addQualifications($employee, $request['doctor']['qualifications'] ?? []);

            // Add specialities
            $this->specialityRepository->addSpecialities($employee, $request['doctor']['specialities'] ?? []);

            // Bind employee to Party
            $party->employees()->save($employee);

            // Commit the transaction
//            DB::commit();

            return $employee;
//        } catch (Exception $e) {
//            // Rollback the transaction on error
//            DB::rollBack();
//
//            return null;
//        }
    }

}
