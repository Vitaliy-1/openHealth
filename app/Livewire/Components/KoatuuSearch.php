<?php

namespace App\Livewire\Components;

use App\Helpers\JsonHelper;
use App\Models\Koatuu\KoatuuLevel1;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Validate;
use Livewire\Component;

class KoatuuSearch extends Component
{
    #[Validate([
        'area' => 'required',
        'region' => 'required',
        'settlement' => 'required',
        'settlement_type' => 'required',
        'street_type' => 'required',
    ])]

    public string $area = '';

    public string $region = '';

    public string $settlement = '';

    public string $settlement_type = '';

    public string $street_type = '';

    public string $street = '';

    public string $building = '';

    public string $apartment = '';

    public string $zip = '';

    public string $class = '';

    public ?object $koatuu_level1;

    public ?object $koatuu_level2;

    public ?object $koatuu_level3;

    public ?array $dictionaries;

    protected $listeners = ['fetchAddressData' => 'provideAddressData','setAddressesFields'];

    public function mount($addresses,$class)
    {

        if (!empty($addresses)) {
            $this->updatedFields($addresses);
        }

        $this->class = $class;

        $this->koatuu_level1 = KoatuuLevel1::all();

        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', [
            'SETTLEMENT_TYPE',
            'STREET_TYPE',
        ]);

    }

    public function updatedFields($addresses)
    {
        foreach ($addresses as $key => $address) {
            if (!empty($address)) {
                $this->$key = $address;
            }
        }
    }
    public function setAddressesFields($addresses)
    {
        $this->updatedFields($addresses);
    }

    public function updated($field)
    {

        if ($field == 'area') {
            $this->region = '';
            $this->settlement = '';
            $this->settlement_type = '';
            $this->street_type = '';
            $this->street = '';
            $this->building = '';
            $this->apartment = '';
            $this->zip = '';
        }

        if ($field == 'region') {
            $this->settlement = '';
            $this->settlement_type = '';
            $this->street_type = '';
            $this->street = '';
            $this->building = '';
            $this->apartment = '';
            $this->zip = '';
        }

        if ($field == 'settlement') {
            $this->street_type = '';
            $this->street = '';
            $this->building = '';
            $this->apartment = '';
            $this->zip = '';
        }
    }

    public function provideAddressData()
    {
        $this->validate();

        $addresses['area'] = $this->area;
        $addresses['region'] = $this->region;
        $addresses['settlement'] = $this->settlement;
        $addresses['settlement_type'] = $this->settlement_type;
        $addresses['street_type'] = $this->street_type;
        $addresses['street'] = $this->street;
        $addresses['building'] = $this->building;
        $addresses['apartment'] = $this->apartment;
        $addresses['zip'] = $this->zip;

        $this->dispatch('addressDataFetched', $addresses);
    }

    public function searchKoatuuLevel2()
    {
        if (empty($this->area) && strlen($this->region) <= 3) {
            return false;
        }

        $this->koatuu_level2 = $this->koatuu_level1
            ->where('name', $this->area)
            ->first()
            ->koatuu_level2()
            ->where('name', 'ilike', '%' . $this->region . '%')
            ->take(5)->get();
        if ($this->koatuu_level2->isEmpty()) {
            $this->region = '';
        }

    }

    public function searchKoatuuLevel3()
    {

        if (empty($this->region) && strlen($this->settlement) <= 3) {
            return;
        }

        $this->koatuu_level3 = $this->koatuu_level2
            ->where('name', $this->region)
            ->first()
            ->koatuu_level3()
            ->where('name', 'ilike', '%' . $this->settlement . '%')
            ->take(5)->get();

        if ($this->koatuu_level3->isEmpty()) {
            $this->settlement = '';
        }
    }

    public function render()
    {
        return view('livewire.components.koatuu-search');
    }
}
