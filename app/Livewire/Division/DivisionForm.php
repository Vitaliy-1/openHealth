<?php

declare(strict_types=1);

namespace App\Livewire\Division;

use Livewire\Component;
use App\Models\Division;
use App\Traits\AddressSearch;
use App\Traits\WorkTimeUtilities;
use App\Livewire\Division\Forms\DivisionFormRequest;

// TODO: divide this class onto three ones: Divisions as parent class and Division Create & DivisionUpdate extends Division
class DivisionForm extends Component
{
    use WorkTimeUtilities,
        AddressSearch;

    public DivisionFormRequest $formService;

    public string $mode = 'create';

    public array $dictionaries;

    protected array $divisionAllowedPhoneTypeKeys = ['MOBILE','LAND_LINE'];
    protected array $divisionAllowedTypeKeys = ['CLINIC', 'AMBULANT_CLINIC', 'FAP'];

    public function mount($id = '')
    {
        if (!empty($id)) {
            $this->getDivision($id);
            $this->mode = 'edit';
        }

        $this->formService->initWorkingHours($this->weekdays);

        $this->dictionaries = [
            'SETTLEMENT_TYPE' => dictionary()->getDictionary('SETTLEMENT_TYPE'),
            'PHONE_TYPE' => dictionary()->getDictionary('PHONE_TYPE', false)
                ->allowedKeys($this->divisionAllowedPhoneTypeKeys)
                ->toArrayRecursive(),
            'DIVISION_TYPE' => dictionary()->getDictionary('DIVISION_TYPE', false)
                ->allowedKeys($this->divisionAllowedTypeKeys)
                ->toArrayRecursive()
        ];
    }

    public function getDivision($id)
    {
        $division = Division::find($id);

        $this->formService->setDivision($division->toArray());

        $this->formService->setDivisionParam('addresses', $division->address->toArray());

        $this->address = $this->formService->getDivisionParam('addresses');

        if ($this->formService->isDivisionParamExistAndNull('working_hours')) {
            $this->formService->initWorkingHours($this->weekdays);
        }
    }

    public function validateDivision(): bool
    {
        $error = $this->formService->doValidation();

        if ($error) {
            $this->dispatch('flashMessage', ['message' => $error, 'type' => 'error']);

            return false;
        } else {
            return true;
        }
    }

    public function store()
    {
        if ($this->validateDivision()) {
            $this->updateOrCreate(new Division());
        }
    }

    public function update(): void
    {
        if ($this->validateDivision()) {
            $division = Division::find($this->formService->getDivisionParam('id'));

            $this->updateOrCreate($division);
        }
    }

    public function updateOrCreate(Division $division)
    {
        $response = $this->mode === 'edit'
            ? $this->formService->updateDivision()
            : $this->formService->createDivision();

        if ($response) {
            $this->formService->saveDivision($division, $response);

            return redirect()->route('division.index');
        }

        $this->dispatch('flashMessage', ['message' => __('Інформацію не оновлено'), 'type' => 'error']);
    }

    /**
     * Proxy method!
     * Proceed data when day is off and hasn't the schedule at all
     *
     * @param  mixed  $day
     * @param  mixed  $allDayWork
     *
     * @return void
     */
    public function notWorking($day, $allDayWork)
    {
        $this->formService->notWorking($day, $allDayWork);
    }

    /**
     * Proxy method!
     * Add shift(s) to the current day's schedule
     *
     * @param  string  $day
     *
     * @return void
     */
    public function addAvailableShift(string $day): void
    {
        $this->formService->addAvailableShift($day);
    }

    /**
     * Proxy method!
     * Remove the selected shift from the day's schedule
     *
     * @param  string  $day  key value aka 'mon', 'tue' etc.
     * @param  int  $shift  shift's numeric position in array
     *
     * @return void
     */
    public function deleteShift(string $day, int $shift)
    {
        $this->formService->deleteShift($day, $shift);
    }

    /**
     * Proxy method!
     * Called when no shift should be present in the day's schedule.
     * But one time range must left anyway!
     *
     * @param  mixed  $day
     * @param  mixed  $isShift  true if shift schedule is activated
     * @return void
     */
    public function noShift($day, $isShift)
    {
        $this->formService->noShift($day, $isShift);
    }

    /**
     * Render with pagination
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $currentDivision = [];
        $division = $this->formService->getDivision();

        if (!empty($division)) {
            $currentDivision['name'] = !empty($division['name'])
                ? $division['name']
                : '';
            $currentDivision['type'] = !empty($division['type'])
                ? dictionary()->getDictionary('DIVISION_TYPE', false)->getValue($division['type'])
                : '';
        }

        return view('livewire.division.division-form-create', compact('currentDivision'));
    }
}
