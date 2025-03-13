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

    public string $mode = 'create';

    public ?array $tableHeaders = [];

    /**
     * Values of possible allowed categories
     *
     * @var array $healthcareCategoriesKeys
     */
    protected array $healthcareCategoriesKeys = ['MSP'];

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

        $this->category = $this->healthcareCategoriesKeys[0];

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

    protected function prepareDictionaries(): void
    {
        $healthcareServiceCategories = dictionary()->getDictionary('HEALTHCARE_SERVICE_CATEGORIES', false)
            ->allowedKeys($this->healthcareCategoriesKeys)
            ->toArrayRecursive();
        $specialityType = dictionary()->getDictionary('SPECIALITY_TYPE', false)
            ->allowedKeys($this->speciality_type_msp_keys)
            ->toArrayRecursive();
        $providingCondition = dictionary()->getDictionary('PROVIDING_CONDITION');

        // Using for HealthcareServices main page (in table)
        $this->dictionaries['show'] = [
            'HEALTHCARE_SERVICE_CATEGORIES' => $healthcareServiceCategories,
            'SPECIALITY_TYPE' => $specialityType,
            'PROVIDING_CONDITION' => $providingCondition
        ];

        // Use within modal dialog window
        $this->dictionaries['modal'] = $this->dictionaries['show'];
    }

    #[On('refreshPage')]
    public function refreshPage()
    {
        $this->dispatch('$refresh');
    }

    public function closeModal(): void
    {
        $this->showModal = false;

        $this->formService->healthcareServiceClean($this->category);

        $this->dispatch('refreshPage');
    }

    public function create(): void
    {
        $this->formService->healthcareServiceClean();

        $this->mode = 'create';

        $this->initHealthcareService();

        $this->resetErrorBag();

        $this->openModal();
    }

    public function store(): void
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

    public function edit(HealthcareService $healthcareServiceApi): void
    {
        $this->mode = 'edit';

        $this->formService->setHelathcareService($healthcareServiceApi->toArray());

        $this->openModal();
    }

    public function update(HealthcareService $healthcareService): void
    {
        $id = $this->formService->getHealthcareServiceParam('id');
        $error = $this->formService->doValidation($this->mode);

        if ($error) {
            $this->dispatch('flashMessage', ['message' => $error, 'type' => 'error']);
        } else {
            $this->updateOrCreate($healthcareService::find($id));
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

        return HealthcareServiceRequestApi::updateHealthcareServiceRequest($uuid, $this->formService->getHealthcareService());
    }

    private function createHealthcareService(): array
    {
        return HealthcareServiceRequestApi::createHealthcareServiceRequest($this->division->uuid, $this->formService->getHealthcareService());
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

        $this->syncHealthcareServicesSave($syncHealthcareServices);

        $this->dispatch('refreshPage');
    }

    public function syncHealthcareServicesSave($responses): void
    {
        foreach ($responses as $response) {
            $healthcareService = HealthcareService::firstOrNew(['uuid' => $response['id']]);
            $healthcareService->setAttribute('uuid', $response['id']);
            $healthcareService->fill($response);
            $this->division->healthcareService()->save($healthcareService);
        }
    }

    public function tableHeadersHealthcare(): void
    {
        $this->tableHeaders = [
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
            ? dictionary()->getDictionary('PROVIDING_CONDITION', false)
                ->allowedKeys(['OUTPATIENT'])
                ->toArrayRecursive()
            : dictionary()->getDictionary('PROVIDING_CONDITION');

        // if ($category === 'PHARMACY_DRUGS') {
        //     $this->speciality_type_msp_keys = ["PHARMACIST", "PROVISOR", "CLINICAL_PROVISOR"];
        //     $this->specialityType();
        //     $this->license_show = true;
        // }
    }

    public function specialityType(): void
    {
        $this->dictionaries['modal']['SPECIALITY_TYPE'] = array_intersect_key(
            $this->speciality_type,
            array_flip($this->speciality_type_msp_keys)
        );
    }

    public function changeProvidingCondition($type): void
    {
        $currentProvidingCondition = $this->formService->getHealthcareServiceParam('providing_condition') ?? '';

        if ($currentProvidingCondition === 'INPATIENT') {
            $this->dictionaries['modal']['SPECIALITY_TYPE'] = dictionary()->getDictionary('SPECIALITY_TYPE', false)
                ->allowedKeys($this->speciality_type_inpatient_keys)
                ->toArrayRecursive();
        } else {
            $this->dictionaries['modal']['SPECIALITY_TYPE'] = dictionary()->getDictionary('SPECIALITY_TYPE', false)
                ->allowedKeys($this->speciality_type_msp_keys)
                ->toArrayRecursive();
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
        $currentDivision['type'] = dictionary()->getDictionary('DIVISION_TYPE', false)->getValue($this->division->type);

        return view('livewire.division.healthcare-service-form', compact(['healthcareServices', 'currentDivision']));
    }
}
