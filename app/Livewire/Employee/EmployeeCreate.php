<?php

namespace App\Livewire\Employee;

use App\Models\Employee\EmployeeRequest;
use App\Models\Relations\Document;
use App\Models\Relations\Education;
use App\Models\Relations\Party;
use App\Models\Relations\Qualification;
use App\Models\Relations\ScienceDegree;
use App\Models\Relations\Speciality;
use App\Models\User;
use App\Services\LegalEntityContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmployeeCreate extends EmployeeComponent
{
    /**
     * @throws ValidationException
     */
    public function save()
    {
        $validated = $this->form->validatedSnakeCase();

        DB::transaction(function () use ($validated) {
            // 1. Створення Party
            $party = Party::create($validated['party']);

            // 2. Створення EmployeeRequest
            $employeeRequest = EmployeeRequest::create([
                'party_id' => $party->id,
                'position' => $validated['party']['position'],
                'employee_type' => $validated['party']['employee_type'],
                'start_date' => $validated['party']['start_date'] ?? now()->toDateString(),
                'status' => 'NEW',
                'inserted_at' => now(),
            ]);

            // 3. Створення User
            $user = User::create([
                'email' => $party->email,
                'password' => Hash::make(Str::random(12)),
                'legal_entity_id' => app(LegalEntityContext::class)->id(),
                'person_id' => $party->id, // ТУТ ВАЖЛИВО: якщо User має person_id що = party id
            ]);

            // 4. Збереження документів
            foreach ($this->form->documents ?? [] as $docData) {
                $document = new Document($docData);
                $document->documentable()->associate($party); // MorphMany до Party
                $document->save();
            }

            // 5. Збереження освіти
            foreach ($this->form->educations ?? [] as $educationData) {
                $education = new Education($educationData);
                $education->educationable()->associate($party);
                $education->save();
            }

            // 6. Збереження спеціальностей
            foreach ($this->form->specialities ?? [] as $specialityData) {
                $speciality = new Speciality($specialityData);
                $speciality->specialityable()->associate($party);
                $speciality->save();
            }

            // 7. Збереження наукового ступеня
            if (!empty($validated['science_degree'])) {
                $scienceDegree = new ScienceDegree($validated['science_degree']);
                $scienceDegree->science_degreeable()->associate($party);
                $scienceDegree->save();
            }

            // 8. Збереження кваліфікацій
            foreach ($this->form->qualifications ?? [] as $qualificationData) {
                $qualification = new Qualification($qualificationData);
                $qualification->qualificationable()->associate($party);
                $qualification->save();
            }
        });

        dd('ok');
        session()->flash('success', __('forms.saved_successfully'));
        return redirect()->route('employee.index');
    }

    public function render()
    {
        $pageTitle =  __('Додати співробітника');
        $currentEmployee = [];
        // або
        $currentEmployee = $this->newEmployee ?? [];
        return view('livewire.employee.employee-create', compact(['pageTitle', 'currentEmployee']));
    }
}
