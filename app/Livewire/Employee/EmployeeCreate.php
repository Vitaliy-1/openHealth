<?php

namespace App\Livewire\Employee;

use App\Models\Employee\EmployeeRequest;
use App\Models\LegalEntity;
use App\Models\Relations\Document;
use App\Models\Relations\Education;
use App\Models\Relations\Party;
use App\Models\Relations\Qualification;
use App\Models\Relations\ScienceDegree;
use App\Models\Relations\Speciality;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmployeeCreate extends EmployeeComponent
{
    /**
     * @throws ValidationException
     * @throws \Throwable
     */
    public function save(): void
    {
        $validated = $this->form->validate(); // validate() вже повертає snake_case масив

        DB::transaction(function () use ($validated) {
            // 1. Створення Party
            $party = Party::create($validated['party']);

            // 2. Створення EmployeeRequest
            EmployeeRequest::create([
                'party_id'       => $party->id,
                'position'       => $validated['party']['position'],
                'employee_type'  => $validated['party']['employee_type'],
                'start_date'     => $validated['party']['start_date'] ?? now()->toDateString(),
                'status'         => 'NEW',
                'inserted_at'    => now(),
            ]);

            // 3. Створення User
            User::create([
                'email'            => $party->email,
                'password'         => Hash::make(Str::random(12)),
                'legal_entity_id'  => app(LegalEntity::class)?->id,
                'person_id'        => $party->id,
            ]);

            // 4. Документи
            foreach ($this->form->documents ?? [] as $docData) {
                $document = new Document($docData); // Ensure keys are snake_case
                $document->documentable()->associate($party);
                $document->save();
            }

            // 5. Освіта
            foreach ($this->form->educations ?? [] as $educationData) {
                $education = new Education($educationData);
                $education->educationable()->associate($party);
                $education->save();
            }

            // 6. Спеціальності
            foreach ($this->form->specialities ?? [] as $specialityData) {
                $speciality = new Speciality($specialityData);
                $speciality->specialityable()->associate($party);
                $speciality->save();
            }

            // 7. Науковий ступінь
            if (!empty($validated['science_degree'])) {
                $scienceDegree = new ScienceDegree($validated['science_degree']);
                $scienceDegree->science_degreeable()->associate($party);
                $scienceDegree->save();
            }

            // 8. Кваліфікації
            foreach ($this->form->qualifications ?? [] as $qualificationData) {
                $qualification = new Qualification($qualificationData);
                $qualification->qualificationable()->associate($party);
                $qualification->save();
            }
        });

        session()->flash('success', __('forms.saved_successfully'));
    }

    public function render()
    {
        return view('livewire.employee.employee-create');
    }
}
