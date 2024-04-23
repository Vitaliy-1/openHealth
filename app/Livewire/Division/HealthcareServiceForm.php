<?php

namespace App\Livewire\Division;

use App\Classes\eHealth\Api\HealthcareServiceApi;
use App\Helpers\JsonHelper;
use App\Livewire\Division\Api\HealthcareServiceRequestApi;
use App\Models\Division;
use App\Models\HealthcareService;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

class HealthcareServiceForm extends Component
{

    #[Validate([
        'healthcare_service.category' => 'required|min:6|max:255',
        'healthcare_service.providing_condition' => 'required',
        'healthcare_service.speciality_type' => 'required',
        'healthcare_service.type' => 'required_if:healthcare_service.category,PHARMACY_DRUGS',
    ])]

    public ?array $healthcare_service = [] ;
    public  ?object  $healthcare_services;

    public Division $division;

    public  string $mode = 'create';
    public ?array $tableHeaders = [];

    public ?array $dictionaries =[];
    public bool $showModal = false;
    public ?array $available_time = [];
    public ?array $speciality_type_key = ["THERAPIST", "PEDIATRICIAN", "FAMILY_DOCTOR",'RECEPTIONIST'];

    public ?array $speciality_type;
    /**
     * @var true
     */
    public bool $license_show;

    public function mount(Division $division)
    {
        $this->division = $division;
        $this->getHealthcareServices() ;
        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', [
            'HEALTHCARE_SERVICE_CATEGORIES',
            'SPECIALITY_TYPE',
            'HEALTHCARE_SERVICE_SPECIALITY_TYPE_FIELD_REQUIRED_FOR_CATEGORIES',
            'HEALTHCARE_SERVICE_PHARMACY_DRUGS_TYPES',
            'PROVIDING_CONDITION',
        ]);
        $this->speciality_type = $this->dictionaries['SPECIALITY_TYPE'];
        $this->specialityType();
        $this->tableHeadersHealthcare();
    }

    public function getHealthcareServices():void
    {
        $this->healthcare_services = $this->division
            ->healthcareService()
            ->get();
    }

    public function openModal():void
    {
        $this->showModal = true;
    }

    public function closeModal():void
    {
        $this->showModal = false;
        $this->healthcare_service = [];
        $this->getHealthcareServices();

    }

    public  function create():void{
        $this->healthcare_service = [];

        $this->mode = 'create';
        $this->openModal();
    }

    public function store():void
    {
        $this->resetErrorBag();
        $this->validate();
        $this->updateOrCreate(new HealthcareService());
        $this->closeModal();
    }

    public function edit(HealthcareService $healthcareServiceApi):void
    {
        $this->mode = 'edit';
        $this->healthcare_service = $healthcareServiceApi->toArray();
        $this->openModal();
    }

    public function update(HealthcareService $healthcareService) :void
    {
        $healthcareService = $healthcareService::find($this->healthcare_service['id']);
        $this->updateOrCreate($healthcareService);
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
        return HealthcareServiceRequestApi::updateHealthcareServiceRequest($this->healthcare_service['uuid'],$this->healthcare_service);
    }

    private function createHealthcareService(): array
    {
        return HealthcareServiceRequestApi::createHealthcareServiceRequest($this->healthcare_service);
    }

    private function saveHealthcareService(HealthcareService $healthcareService, array $response): void
    {
        $healthcareService->fill($this->healthcare_service);
        $healthcareService->setAttribute('uuid', $response['id']);
        $this->division->healthcareService()->save($healthcareService);
    }

    public function activate(HealthcareService $healthcareService): void
    {
        HealthcareServiceRequestApi::activateHealthcareServiceRequest($healthcareService['uuid']);
        $healthcareService->setAttribute('status', 'ACTIVE');
        $healthcareService->save();
        $this->getHealthcareServices();
    }

    public function deactivate(HealthcareService $healthcareService): void
    {
        HealthcareServiceRequestApi::deactivateHealthcareServiceRequest($healthcareService['uuid']);
        $healthcareService->setAttribute('status', 'DEACTIVATED');
        $healthcareService->save();
        $this->getHealthcareServices();
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

    public function  changeCategory():void
    {
        if (isset($this->healthcare_service['category']) && $this->healthcare_service['category'] == 'PHARMACY_DRUGS') {
            $this->speciality_type_key = ["PHARMACIST", "PROVISOR", "CLINICAL_PROVISOR"];
            $this->specialityType();
            $this->license_show = true;
        }
    }

    public function specialityType():void
    {
        $this->dictionaries['SPECIALITY_TYPE'] = array_intersect_key( $this->speciality_type, array_flip($this->speciality_type_key));
    }

    public function addAvailableTime($k = 0):void
    {
         $this->healthcare_service['available_time'][] = [
            'days_of_week' => get_day_key($k),
            'all_day' => '',
            'available_start_time' =>'',
            'available_end_time' =>'',
        ];
    }
    public function addNotAvailableTime():void
    {
        $this->healthcare_service['not_available'][] = [
            'description' => '',
            'during' => [
                'start' => Carbon::now(),
                'end' => '',
            ],
        ];

    }
    public function removeNotAvailable($k):void
    {
        unset($this->healthcare_service['not_available'][$k]);
    }
    public function removeAvailableTime($k):void
    {
        unset($this->healthcare_service['available_time'][$k]);
    }
    public function render(): \Illuminate\View\View
    {
        return view('livewire.division.healthcare-service-form');
    }
}
