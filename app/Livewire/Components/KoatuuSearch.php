<?php

namespace App\Livewire\Components;

use App\Helpers\JsonHelper;
use App\Models\Koatuu\KoatuuLevel1;
use Livewire\Attributes\Validate;
use Livewire\Component;

class KoatuuSearch extends Component
{
    #[Validate('required|min:3')]
    public string $area = '';

    public string $region = '';

    public string $settlement = '';

    public string $settlement_type = '';

    public string $street = '';

    public string $building = '';

    public string $apartment = '';

    public string $zip = '';
    public ?object $koatuu_level1;

    public ?object $koatuu_level2;

    public ?object $koatuu_level3;

    public ?array $dictionaries;

    protected $listeners = ['setAddressesFields'];


    public function mount($addresses)

    {
        if (!empty($addresses)){
            $this->updateField($addresses);
        }

        $this->koatuu_level1 = KoatuuLevel1::all();

        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', [
            'SETTLEMENT_TYPE',
        ]);
    }

    public function setAddressesFields($addresses)
    {
        foreach ($addresses as $key => $address) {
            if (!empty($address)) {
                $this->$key = $address;
            }
        }
    }



    public function updated($field)
    {
        $this->dispatch('updateFieldAddresses', [
            'field' => $field,
            'value' => $this->$field,
        ]);

    }

    public function searchKoatuuLevel2()
    {

        if (empty($this->area) && strlen($this->region) <= 3) {
            return;
        }

        $this->koatuu_level2 = $this->koatuu_level1
            ->where('name', $this->area)
            ->first()
            ->koatuu_level2()
            ->where('name', 'ilike', '%' . $this->region . '%')
            ->take(5)->get();

    }

    public function validateKoatauu()
    {
        $this->validate();
    }

    public function searchKoatuuLevel3()
    {

        if (empty($this->region) && strlen($this->settlement) <= 3) {
            return;
        }

        $this->koatuu_level3 = $this->koatuu_level2
            ->where('name', $this->region)->first()
            ->koatuu_level3()
            ->where('name', 'ilike', '%' . $this->settlement . '%')
            ->take(5)->get();
    }


    public function render()
    {
        return view('livewire.components.koatuu-search');
    }
}
