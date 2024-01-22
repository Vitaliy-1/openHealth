<?php

namespace App\Livewire\Division;

use App\Classes\eHealth\Api\DivisionApi;
use App\Classes\eHealth\Request;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Division extends Component
{


    #[Validate([
        'name' => 'required',
        'type' => 'required',
        'addresses.type' => 'required',
        'addresses.area' => 'required',
        'addresses.region' => 'required',
        'addresses.settlement' => 'required',
        'addresses.street' => 'required',
        'addresses.building' => 'required',
        'addresses.settlement_type' => 'required',
        'phones.number' => 'required',
        'phones.type' => 'required',
        'email' => 'required',
        'working_hours' => 'required',
        'is_active' => 'required',
        'status' => 'required',
        'external_id' => 'required',
    ])]

    public ?array $division = [];

    public ?array $divisions = [];

    public $headers = [];


    public function mount( )
    {
       $this->getDivisions();

       $this->headers = $this->getHeaders();
    }

    public function getHeaders()
    {
        return ['Назва ','Тип', 'Статус', 'Дії'];
    }


    public function store(){
        $division = new Division();
        $division->fill($this->division);
        $division->save();
    }


    public function getDivisions(): array
    {
        if (auth()->user()->person->employee->legal_entity_id)
          return  $this->divisions = (new DivisionApi())->getDivisions(
                ['legal_entity_id' => auth()->user()->person->employee->legal_entity_id]
            );

        return (new DivisionApi())->getDivisions() ?? [];
    }


    public function render()
    {
        return view('livewire.division.division');
    }
}
