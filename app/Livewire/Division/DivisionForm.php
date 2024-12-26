<?php

namespace App\Livewire\Division;

use App\Livewire\Division\Forms\DivisionFormRequest;
use App\Livewire\Division\Api\DivisionRequestApi;
use App\Traits\WorkTimeUtilities;
use App\Models\Division;
use Livewire\Component;

class DivisionForm extends Component
{
    use WorkTimeUtilities;

    public DivisionFormRequest $formService;

    public ?object $legalEntity;

    public string $mode = 'create';

    public ?array $dictionaries;

    public array $working_hours = [];

    protected ?array $division_allowed_phone_type_keys = ['MOBILE','LAND_LINE'];

    protected ?array $division_allowed_type_keys = ['CLINIC', 'AMBULANT_CLINIC', 'FAP'];

    // TODO: remove listeners and all data/methods depend on them
    protected $listeners = ['addressDataFetched'];

    public function mount($id = '')
    {
        if (!empty($id)) {
            $this->getDivision($id);
            $this->mode = 'edit';
        }

        $this->working_hours = $this->weekdays;

        $this->initWorkingHours();

        $this->getLegalEntity();

        $this->dictionaries = [
            'PHONE_TYPE' => $this->filterDictionary('PHONE_TYPE', $this->division_allowed_phone_type_keys),
            'SETTLEMENT_TYPE' => dictionary()->getDictionary('SETTLEMENT_TYPE', true)['values'],
            'DIVISION_TYPE' => $this->filterDictionary('DIVISION_TYPE', $this->division_allowed_type_keys)
        ];
    }

    /**
     * Get original dictionary and return key:values pair which key is matched with one of the key stored into #keys array.
     * If $removeKeys = true this will remove all keys matched with $keys array.
     *
     * @param string $dictionaryName
     * @param array $keys
     * @param bool $removeKeys
     *
     * @return array
     */
    public function filterDictionary(string $dictionaryName, array $keys, bool $removeKeys = false): array
    {
        $filteredDictionary = array_filter(dictionary()->getDictionary($dictionaryName, true)['values'], function($key) use ($keys, $removeKeys) {
            if ($removeKeys) {
                return !in_array($key, $keys);
            } else {
                return in_array($key, $keys);
            }
        }, ARRAY_FILTER_USE_KEY);

        return $filteredDictionary;
    }

    /**
     * Working Hours may be not initiated (for creation case) or may be incomplete (for update case).
     * Here, this method will bring address array to properly state
     *
     * @return void
     */
    protected function initWorkingHours(): void
    {
        $arr = $this->formService->getDivisionParam('working_hours');

        // getDivisionParam returned '' (empty string) if the param hasn't been found
        $arr = empty($arr) ? [] : $arr;

        foreach ($this->working_hours as $day => $name) {
            if (!isset($arr[$day]) || ($arr[$day][0]['0'] === '00:00' && $arr[$day][0]['1'] === '00:00')) {
                $arr[$day] = [[]];
            }
        }

        $this->formService->setDivisionParam('working_hours', $arr);
    }

    public function getLegalEntity()
    {
        $this->legalEntity = auth()->user()->legalEntity;
    }

    public function getDivision($id)
    {
        $this->formService->setDivision(Division::find($id)->toArray());
        $this->formService->setDivisionParam('phones', $this->formService->getDivisionParam('phones')[0]);
        $this->formService->setDivisionParam('addresses', $this->formService->getDivisionParam('addresses')[0]);

        if ($this->formService->isDivisionParamExistAndNull('working_hours')) {
            $this->initWorkingHours();
        }
    }

    public function fetchDataFromAddressesComponent():void
    {
        $this->dispatch('fetchAddressData');
    }

    public function addressDataFetched($addressData): void
    {
        $this->formService->setDivisionParam('addresses', $addressData);
    }

    public function validateDivision(): bool
    {
        // $this->resetErrorBag(); // TODO: remove after testing

        $error = $this->formService->doValidation();

        if ($error) {
            $this->dispatch('flashMessage', ['message' => $error, 'type' => 'error']);

            return false;
        } else {
            return true;
        }
    }

    public function create()
    {
        $this->mode = 'create';
    }

    public function store()
    {
        $this->fetchDataFromAddressesComponent();
        $this->dispatch('address-data-fetched');

        if ($this->validateDivision()) {
            $this->updateOrCreate(new Division());
        }
    }

    // TODO: remove this method after resolve the #110 ISSUE
    public function checkAndProceedToNextStep(): void
    {
        // Dumb method for compatibility purpose
    }

    public function update():void
    {
        $this->fetchDataFromAddressesComponent();
        $this->dispatch('address-data-fetched');

        if ($this->validateDivision()) {
            $division = Division::find($this->formService->getDivisionParam('id'));

            $this->updateOrCreate($division);
        }
    }

    public function updateOrCreate(Division $division)
    {
        $response = $this->mode === 'edit'
            ? $this->updateDivision()
            : $this->createDivision();

        if ($response) {
            $this->saveDivision($division, $response);

            return redirect()->route('division.index');
        }

        $this->dispatch('flashMessage', ['message' => __('Інформацію не оновлено'), 'type' => 'error']);
    }

    private function updateDivision(): array
    {
        $uuid = $this->formService->getDivisionParam('uuid');
        $division = removeEmptyKeys($this->formService->getDivision());

        return DivisionRequestApi::updateDivisionRequest($uuid, $division);
    }

    private function createDivision(): array
    {
        $division = removeEmptyKeys($this->formService->getDivision());

        return DivisionRequestApi::createDivisionRequest($division);
    }

    private function saveDivision(Division $division, array $response): void
    {
        $division->fill($response);
        $division->setAttribute('uuid', $response['id']);
        $division->setAttribute('legal_entity_uuid', $response['legal_entity_id']);
        $division->setAttribute('external_id', $response['external_id']);
        $division->setAttribute('status', $response['status']);

        $this->legalEntity->division()->save($division);
    }

    /**
     * Proceed data when day is off and hasn't the schedule at all
     *
     * @param mixed $day
     * @param mixed $allDayWork
     *
     * @return void
     */
    public function notWorking($day, $allDayWork)
    {
        $working_hours = $this->formService->getDivisionParam('working_hours');

        if ($allDayWork) {
            $working_hours[$day] = [];
        } else {
            if (count($working_hours[$day]) === 0) {
                $working_hours[$day][] = [];
            }
        }

        $this->formService->setDivisionParam('working_hours', $working_hours);
    }

    /**
     * Add shift(s) to the current day's schedule
     *
     * @param string $day
     *
     * @return void
     */
    public function addAvailableShift(string $day): void
    {
        $working_hours = $this->formService->getDivisionParam('working_hours');

        $working_hours[$day][] = [];

        $this->formService->setDivisionParam('working_hours', $working_hours);
    }

    /**
     * Remove the selected shift from the day's schedule
     *
     * @param string $day   // key value aka 'mon', 'tue' etc.
     * @param int $shift    // shift's numeric position in array
     *
     * @return void
     */
    public function deleteShift(string $day, int $shift)
    {
        $working_hours = $this->formService->getDivisionParam('working_hours');

        unset($working_hours[$day][$shift]);

        $working_hours[$day] = array_values($working_hours[$day]);

        $this->formService->setDivisionParam('working_hours', $working_hours);
    }

    /**
     * This method called when no shift should be present in the day's schedule.
     * But one time range must left anyway!
     *
     * @param mixed $day
     * @param mixed $isShift    // true if shift schedule is activated
     * @return void
     */
    public function noShift($day, $isShift)
    {
        $working_hours = $this->formService->getDivisionParam('working_hours');

        if ($isShift) {
            $shiftCount = count($working_hours[$day]);

            // There can be only one!
            if ($shiftCount > 1) {
                for ($i = 1; $i < $shiftCount; $i++) {
                    $this->deleteShift($day, $i);
                }
            }
        }
    }

    public function render()
    {
        $currentDivision = [];
        $_division = $this->formService->getDivision();

        if (!empty($_division)) {
            $currentDivision['name'] = !empty($_division['name'])
                ? $_division['name']
                : '';
            $currentDivision['type'] = !empty($_division['type'])
                ? dictionary()->getDictionary('DIVISION_TYPE', true)['values'][$_division['type']]
                : '';
        }

        return view('livewire.division.division-form-create', compact('currentDivision'));
    }
}
