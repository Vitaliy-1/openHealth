<?php

namespace App\Livewire\Contract;

use App\Classes\eHealth\Api\LegalEntitiesApi;
use App\Livewire\Contract\Forms\ContractFormRequest;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use App\Models\Division;
use App\Models\LegalEntity;
use App\Traits\FormTrait;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class ContractForm extends Component
{
    use FormTrait,WithFileUploads;


    public ?array $dictionaries_field = [
        'CONTRACT_TYPE',
    ];

    public ?LegalEntity $legalEntity;

    public ?Collection $divisions;
    public ?Collection $healthcareServices;

    public ContractFormRequest $contract_request;

    public array $legalEntityApi = [];
    public array $external_contractors = [];
    public string $external_contractor_key = '';
    public string $legalEntity_search = '';

    public function mount()
    {
        $this->getDictionary();
        $this->getLegalEntity();
    }

    public function getLegalEntity()
    {
        $this->legalEntity = auth()->user()->legalEntity;
        $this->getDivisions();
    }

    public function render()
    {
        return view('livewire.contract.contract-form');
    }

    public function getDivisions()
    {
        $this->divisions = $this->legalEntity->division;
    }


    public function contractType()
    {
        return $this->legalEntity->contract_type;
    }


    public function getLegalEntityApi()
    {
        if (strlen($this->legalEntity_search) >= 7) {
            $this->legalEntityApi = LegalEntitiesRequestApi::getLegalEntities($this->legalEntity_search);
        }

    }


    public function addExternalContractors(): void
    {
        $this->validateExternalContractors();
        if ($this->external_contractor_key !== '') {
            $this->external_contractors[$this->external_contractor_key] = $this->contract_request->external_contractors;
        }
        else {
            $this->external_contractors[] = $this->contract_request->external_contractors;
        }
        $this->closeModal();
    }
    private function validateExternalContractors(): void
    {
        $this->contract_request->rulesForModelValidate('external_contractors');
    }

    private function resetExternalContractorKeyAndRequest(): void
    {
        $this->external_contractor_key = '';
        $this->contract_request->external_contractors = [];
    }


    public function closeModal(): void
    {
        $this->resetExternalContractorKeyAndRequest();
        $this->legalEntity_search = '';
        $this->showModal = false;

    }

    public function editExternalContractors($key): void
    {
        $this->external_contractor_key = $key;
        $this->contract_request->external_contractors = $this->external_contractors[$this->external_contractor_key];
        $this->legalEntity_search  = $this->contract_request->external_contractors['legal_entity']['name'];
        $this->openModal();
    }


    public function deleteExternalContractors($key)
    {
        unset($this->external_contractors[$key]);
    }


    public function getHealthcareServices($id)
    {
        $division = Division::find($id);
        $this->contract_request->external_contractors['divisions']['name'] = $division->name;
        $this->contract_request->external_contractors['divisions']['uuid'] = $division->uuid;
        $this->healthcareServices = $division
            ->healthcareService()
            ->get();
    }


    public function sendApiRequest(){
        $this->contract_request->rulesForModelValidate();
    }
}
