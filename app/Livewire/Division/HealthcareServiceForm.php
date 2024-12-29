<?php

namespace App\Livewire\Division;

use App\Livewire\Division\Api\HealthcareServiceRequestApi;
use App\Livewire\Division\Forms\HealthCareFormRequest;
use App\Models\Division;
use App\Models\HealthcareService;
use App\Traits\FormTrait;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class HealthcareServiceForm extends Component
{
    use WithPagination,
        FormTrait;

    public HealthCareFormRequest $formService;

    public Division $division;

    public  string $mode = 'create';

    public ?array $tableHeaders = [];

    /**
     * Values of possible allowed categories
     *
     * @var ?array $healthcare_categories_keys
     */
    public ?array $healthcare_categories_keys = ['MSP'];

    /**
     * Current selected category
     *
     * @var string|null $category
     */
    public ?string $category = '';

    public ?array $speciality_type_msp_keys = [
        'PHARMACIST', '"PHARMACEUTICS_ORGANIZATION', 'CLINICAL_PROVISOR',
        'ANALYTICAL_AND_CONTROL_PHARMACY', 'PHARMACEUTICS_ORGANIZATION'
    ];

    public ?array $speciality_type_inpatient_keys = [
        'GENERAL_SURGERY', 'ANAESTHETICS', 'NARCOLOGY', 'THORACIC_SURGERY', 'VASCULAR_SURGERY',
        'NEUROSURGERY', 'SURGICAL_ONCOLOGY', 'RADIATION_THERAPY', 'COMBUSTIOLOGY', 'INTENSIVE_THERAPY',
        'PEDIATRIC_SURGERY', 'TRANSPLANTOLOGY', 'ORAL_AND_MAXILLOFACIAL_SURGERY', 'PLASTIC_SURGERY',
        'SURGICAL_OPHTHALMOLOGY', 'GYNECOLOGIC_ONCOLOGY', 'CARDIOVASCULAR_SURGERY', 'PATHOLOGIC_ANATOMY'
    ];

    public ?array $speciality_type;

    /**
     * @var true
     */
    public bool $license_show;

    public bool $divisionStatus = false;


    public function mount(Division $division)
    {
        $this->dictionaries = [
            'show' => [],
            'modal' => []
        ];

        $this->division = $division;

        $this->divisionStatus = $this->division->status === 'ACTIVE';

        $this->category = $this->healthcare_categories_keys[0];

        $this->prepareDictionaries();

        $this->initHealthcareService();

        $this->tableHeadersHealthcare();
    }

    public function initHealthcareService()
    {
        // HEALTHCARE_SERVICE_CATEGORY firmly pinned up to the 'MSP' for now
        $this->formService->setHealthcareServiceParam('category', $this->category);

        if ($this->category === 'MSP') {
            $this->formService->setHealthcareServiceParam('providing_condition', 'OUTPATIENT');
        }

        $this->changeCategory($this->category);
    }

    public function prepareDictionaries()
    {
        // HEALTHCARE_SERVICE_CATEGORIES
        $HSC = $this->filterDictionary('HEALTHCARE_SERVICE_CATEGORIES', $this->healthcare_categories_keys);
        // SPECIALITY_TYPE
        $ST = $this->filterDictionary('SPECIALITY_TYPE', $this->speciality_type_msp_keys, true);
        // PROVIDING_CONDITION
        $PC = dictionary()->getDictionary('PROVIDING_CONDITION', true)['values'];

        // Using for HealthcareServices main page (in table)
        $this->dictionaries['show'] = [
            'HEALTHCARE_SERVICE_CATEGORIES' => $HSC,
            'SPECIALITY_TYPE' => $ST,
            'PROVIDING_CONDITION' => $PC
        ];

        // Use within modal dialog window
        $this->dictionaries['modal'] = $this->dictionaries['show'];
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

    #[On('refreshPage')]
    public function refreshPage()
    {
        $this->dispatch('$refresh');
    }

    public function closeModal():void
    {
        $this->showModal = false;

        $this->formService->healthcareServiceClean($this->category);

        $this->dispatch('refreshPage');
    }

    public function create():void
    {
        $this->formService->healthcareServiceClean();

        $this->mode = 'create';

        $this->initHealthcareService();

        $this->resetErrorBag();

        $this->openModal();
    }

    public function store():void
    {
        $this->resetErrorBag();

        $error = $this->formService->doValidation($this->mode);

        if ($error) {
            $this->dispatch('flashMessage', ['message' => $error, 'type' => 'error']);
        } else {
            $this->updateOrCreate(new HealthcareService());
        }

        $this->closeModal();
    }

    public function edit(HealthcareService $healthcareServiceApi):void
    {
        $this->mode = 'edit';

        $this->formService->setHelathcareService($healthcareServiceApi->toArray());

        $this->openModal();
    }

    public function update(HealthcareService $healthcareService) :void
    {
        $id = $this->formService->getHealthcareServiceParam('id');
        $healthcareService = $healthcareService::find($id);

        $error = $this->formService->doValidation($this->mode);

        if ($error) {
            $this->dispatch('flashMessage', ['message' => $error, 'type' => 'error']);
        } else {
            $this->updateOrCreate($healthcareService);
        }

        $this->closeModal();
    }

    public function updateOrCreate(HealthcareService $healthcareService): void
    {
        $response = $this->mode === 'edit'
            ? $this->updateHealthcareService()
            : $this->createHealthcareService();

        if ($response) {
            $this->saveHealthcareService($healthcareService, $response);
        }
    }

    private function updateHealthcareService(): array
    {
        $uuid = $this->formService->getHealthcareServiceParam('uuid');

        return HealthcareServiceRequestApi::updateHealthcareServiceRequest($uuid,$this->formService->getHealthcareService());
    }

    private function createHealthcareService(): array
    {
        return HealthcareServiceRequestApi::createHealthcareServiceRequest($this->division->uuid,$this->formService->getHealthcareService());
    }

    private function saveHealthcareService(HealthcareService $healthcareService, array $response): void
    {
        $healthcareService->setAttribute('uuid', $response['id']);

        $healthcareService->fill($response);

        $this->division->healthcareService()->save($healthcareService);
    }

    public function activate(HealthcareService $healthcareService): void
    {
        HealthcareServiceRequestApi::activateHealthcareServiceRequest($healthcareService['uuid']);

        $healthcareService->setAttribute('status', 'ACTIVE');
        $healthcareService->save();

        $this->dispatch('refreshPage');
    }

    public function deactivate(HealthcareService $healthcareService): void
    {
        HealthcareServiceRequestApi::deactivateHealthcareServiceRequest($healthcareService['uuid']);

        $healthcareService->setAttribute('status', 'DEACTIVATED');
        $healthcareService->save();

        $this->dispatch('refreshPage');
    }

    public function syncHealthcareServices(): void
    {
        $syncHealthcareServices = HealthcareServiceRequestApi::syncHealthcareServiceRequest($this->division->uuid);

        $this->dispatch('flashMessage', ['message' => __('Інформацію успішно оновлено'), 'type' => 'success']);

        $this->synсHealthcareServicesSave($syncHealthcareServices);

        $this->dispatch('refreshPage');
    }

    public function synсHealthcareServicesSave($responses): void
    {
        foreach ($responses as $response){
            $healthcareService = HealthcareService::firstOrNew(['uuid' => $response['id']]);
            $healthcareService->setAttribute('uuid', $response['id']);
            $healthcareService->fill($response);
            $this->division->healthcareService()->save($healthcareService);
        }
    }

    public function tableHeadersHealthcare(): void
    {
        $this->tableHeaders  = [
            __('ID E-health '),
            __('Категорія'),
            __('Умови надання'),
            __('Тип спеціальності'),
            __('Статус'),
            __('Дія'),
        ];
    }

    public function changeCategory($type): void
    {
        $this->category = $type;

        $this->dictionaries['modal']['PROVIDING_CONDITION'] = $type === 'MSP'
            ? $this->filterDictionary('PROVIDING_CONDITION', ['OUTPATIENT'])
            : dictionary()->getDictionary('PROVIDING_CONDITION', true)['values'];


        // if ($category === 'PHARMACY_DRUGS') {
        //     $this->speciality_type_msp_keys = ["PHARMACIST", "PROVISOR", "CLINICAL_PROVISOR"];
        //     $this->specialityType();
        //     $this->license_show = true;
        // }
    }

    public function specialityType(): void
    {
        $this->dictionaries['modal']['SPECIALITY_TYPE'] = array_intersect_key( $this->speciality_type, array_flip($this->speciality_type_msp_keys));
    }

    public function changeProvidingCondition($type): void
    {
        $currentProvidingCondition = $this->formService->getHealthcareServiceParam('providing_condition') ?? '';

        if ($currentProvidingCondition  == 'INPATIENT') {
            $this->dictionaries['modal']['SPECIALITY_TYPE'] = $this->filterDictionary('SPECIALITY_TYPE', $this->speciality_type_inpatient_keys);
        } else {
            $this->dictionaries['modal']['SPECIALITY_TYPE'] = $this->filterDictionary('SPECIALITY_TYPE', $this->speciality_type_msp_keys, true);
        }
    }

    #[Computed]
    public function availableTime(): array
    {
        return empty($this->formService->getHealthcareServiceParam('available_time'))
            ? []
            : $this->formService->getHealthcareServiceParam('available_time');
    }

    public function addAvailableTime($k = 0): void
    {
        $this->formService->addAvailableTime($k);
    }

    public function removeAvailableTime($k): void
    {
        $this->formService->removeAvailableTime($k);
    }

    #[Computed]
    public function notAvailable(): array
    {
        return empty($this->formService->getHealthcareServiceParam('not_available'))
            ? []
            : $this->formService->getHealthcareServiceParam('not_available');
    }

    public function addNotAvailableTime(): void
    {
        $this->formService->addNotAvailableTime();
    }

    public function removeNotAvailable($k): void
    {
        $this->formService->removeNotAvailable($k);
    }


    public function render(): View
    {
        $perPage = config('pagination.per_page');
        $healthcareServices = $this->division->healthcareService()->orderBy('uuid')->paginate($perPage);
        $currentDivision['name'] = $this->division->name;
        $currentDivision['type'] = dictionary()->getDictionary('DIVISION_TYPE', true)['values'][$this->division->type];

        return view('livewire.division.healthcare-service-form', compact(['healthcareServices', 'currentDivision']));
    }
}
