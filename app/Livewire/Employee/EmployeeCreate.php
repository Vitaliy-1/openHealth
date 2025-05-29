<?php

namespace App\Livewire\Employee;

use App\Core\Arr;
use App\Enums\Status;
use App\Models\Employee\EmployeeRequest;
use App\Models\Person\Person;
use App\Models\Relations\Party;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmployeeCreate extends EmployeeComponent
{
    public function save(): void
    {
        $validated = Arr::toSnakeCase($this->form->toArray());

        DB::beginTransaction();

        try {
            $party = $this->createParty($validated['party']);
            $this->createPhones($party, $validated['party']['phones'] ?? []);
            $person = $this->createPerson($validated['party']);
            $user = $this->createUser($person);

            $this->createEmployeeRequest([
                'party_id' => $party->id,
                'employee_type' => $validated['party']['employee_type'],
                'position' => $validated['party']['position'],
                'start_date' => $validated['party']['start_date'] ?? now()->toDateString(),
                'user_id' => $user->id,
                'status' => Status::NEW
            ]);

            // Блоки з вкладеними зв’язками
            $this->createDocuments($party, $validated['documents'] ?? []);
            $this->createEducations($party, $validated['educations'] ?? []);
            $this->createQualifications($party, $validated['qualifications'] ?? []);
            $this->createSpecialities($party, $validated['specialities'] ?? [], $validated['qualifications'] ?? []);
            $this->createScienceDegrees($party, $validated['science_degrees'] ?? []);

            DB::commit();

            session()->flash('success', __('forms.saved_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saving employee: " . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', __('forms.something_went_wrong') . ' ' . $e->getMessage());
        }
    }

    protected function createParty(array $data): Party
    {
        return Party::create([
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'email' => $data['email'],
            'gender' => $data['gender'],
            'birth_date' => $data['birth_date'],
            'tax_id' => $data['tax_id'],
            'about_myself' => $data['about_myself'] ?? null,
            'working_experience' => $data['working_experience'] ?? null,
        ]);
    }

    protected function createPhones(Party $party, array $phones): void
    {
        foreach ($phones as $phone) {
            $party->phones()->create([
                'number' => $phone['number'] ?? null,
                'type' => $phone['type'] ?? null,
            ]);
        }
    }

    protected function createEmployeeRequest(array $data): void
    {
        $legalEntityId = $this->legalEntityContext->id();

        if (is_null($legalEntityId)) {
            throw new \Exception('Legal Entity ID not found in context.');
        }

        EmployeeRequest::create([
            'party_id' => $data['party_id'],
            'employee_type' => $data['employee_type'],
            'position' => $data['position'],
            'start_date' => $data['start_date'],
            'status' => $data['status'],
            'legal_entity_id' => $legalEntityId,
            'user_id' => $data['user_id'],
            'inserted_at' => now()
        ]);
    }

    protected function createPerson(array $data): Person
    {
        return Person::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'second_name' => $data['middle_name'] ?? null,
            'birth_date' => $data['birth_date'],
            'gender' => $data['gender'],
            'email' => $data['email'],
            'tax_id' => $data['tax_id'],
        ]);
    }

    protected function createUser(Person $person): User
    {
        $legalEntityId = $this->legalEntityContext->id();

        if (is_null($legalEntityId)) {
            throw new \Exception('Legal Entity ID not found in context when creating user.');
        }

        DB::statement("SELECT setval('users_id_seq', (SELECT MAX(id) FROM users))");

        return User::create([
            'email' => $person->email,
            'password' => Hash::make(Str::random(12)),
            'legal_entity_id' => $legalEntityId,
            'person_id' => $person->id,
        ]);
    }

    protected function createDocuments(Party $party, array $documents): void
    {
        foreach ($documents as $doc) {
            $party->documents()->create([
                'type' => $doc['type'] ?? null,
                'number' => $doc['number'] ?? null,
                'issued_by' => $doc['issued_by'] ?? null,
                'issued_at' => $doc['issued_at'] ?? null,
            ]);
        }
    }

    protected function createEducations(Party $party, array $educations): void
    {
        foreach ($educations as $edu) {
            $party->educations()->create([
                'country' => $edu['country'] ?? null,
                'city' => $edu['city'] ?? null,
                'institution_name' => $edu['institution_name'] ?? null,
                'speciality' => $edu['speciality'] ?? null,
                'degree' => $edu['degree'] ?? null,
                'issued_date' => $edu['issued_date'] ?? null,
                'diploma_number' => $edu['diploma_number'] ?? null,
            ]);
        }
    }

    protected function createQualifications(Party $party, array $qualifications): void
    {
        foreach ($qualifications as $qual) {
            $party->qualifications()->create([
                'type' => $qual['type'] ?? null,
                'institution_name' => $qual['institution_name'] ?? null,
                'speciality' => $qual['speciality'] ?? null,
                'certificate_number' => $qual['certificate_number'] ?? null,
                'issued_date' => $qual['issued_date'] ?? null,
            ]);
        }
    }

    protected function createSpecialities(Party $party, array $specialities, array $qualifications = []): void
    {
        $qualificationType = $qualifications[0]['type'] ?? null;

        foreach ($specialities as $spec) {
            $party->specialities()->create([
                'speciality' => $spec['speciality'] ?? null,
                'level' => $spec['level'] ?? null,
                'attestation_name' => $spec['attestation_name'] ?? null,
                'attestation_date' => $spec['attestation_date'] ?? null,
                'certificate_number' => $spec['certificate_number'] ?? null,
                'speciality_officio' => $spec['speciality_officio'] ?? false,
                'qualification_type' => $qualificationType,
            ]);
        }
    }

    protected function createScienceDegrees(Party $party, array $scienceDegrees): void
    {
        foreach ($scienceDegrees as $degree) {
            $party->scienceDegrees()->create([
                'degree' => $degree['degree'] ?? null,
                'country' => $degree['country'] ?? null,
                'city' => $degree['city'] ?? null,
                'issued_date' => $degree['issued_date'] ?? null,
                'institution_name' => $degree['institution_name'] ?? null,
                'speciality' => $degree['speciality'] ?? null,
                'diploma_number' => $degree['diploma_number'] ?? null,
            ]);
        }
    }

    public function render()
    {
        $pageTitle = __('forms.add_employee');
        return view('livewire.employee.employee-create', compact('pageTitle'));
    }
}
