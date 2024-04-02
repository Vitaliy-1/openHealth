<?php

namespace App\Livewire\Contract;

use App\Classes\eHealth\Api\LegalEntitiesApi;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use App\Models\Division;
use App\Models\LegalEntity;
use App\Traits\FormTrait;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ContractForm extends Component
{
    use FormTrait;



   public ?array $dictionaries_field = [
        'CONTRACT_TYPE',
    ];

    public ?LegalEntity $legalEntity;

    public  Collection $divisions;

    public ?array $legalEntityApi = [];
    public array $legalEntity_search = [];

    public function mount() {
        $this->getDictionary();
        $this->getLegalEntity();
    }

    public function getLegalEntity(){
        $this->legalEntity = auth()->user()->legalEntity;
        $this->getDivisions();

    }

    public function render()
    {
        return view('livewire.contract.contract-form');
    }

    public function getDivisions(){
        $this->divisions = $this->legalEntity->division;
    }


    public function contractType()
    {
        return $this->legalEntity->contract_type;
    }


    public function getLegalEntityApi()
    {
        $this->validate(
            ['legalEntity_search.text' => 'required|min:8|max:10'],
        );
        $this->legalEntityApi = LegalEntitiesRequestApi::getLegalEntities($this->legalEntity_search['text'] );


    }
}
