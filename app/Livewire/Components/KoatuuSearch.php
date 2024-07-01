<?php

namespace App\Livewire\Components;

use App\Classes\eHealth\Api\AdressesApi;
use App\Helpers\JsonHelper;
use App\Models\Koatuu\KoatuuLevel1;
use App\Models\Koatuu\KoatuuLevel2;
use App\Models\Koatuu\KoatuuLevel3;
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

    protected $listeners = ['fetchAddressData' => 'provideAddressData','setAddressesFields','validateAddressData'];

    public function mount($addresses,$class)
    {
//        dd(AdressesApi::_regions());
        if (!empty($addresses)) {
            $this->updatedFields($addresses);
            if (!empty($addresses['region'])) {
                $this->koatuu_level2 = KoatuuLevel2::where('name', $addresses['region'])->get();
            }
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
        $fieldsToReset = [];

        switch ($field) {
            case 'area':
                $fieldsToReset = ['region', 'settlement', 'settlement_type', 'street_type', 'street', 'building', 'apartment', 'zip'];
                break;
            case 'region':
                $fieldsToReset = ['settlement', 'settlement_type', 'street_type', 'street', 'building', 'apartment', 'zip'];
                break;
            case 'settlement':
                $fieldsToReset = ['street_type', 'street', 'building', 'apartment', 'zip'];
                break;
            default:
                // Дополнительные условия обновления полей, если необходимо
                break;
        }

        foreach ($fieldsToReset as $fieldToReset) {
            $this->{$fieldToReset} = '';
        }

    }

    public function provideAddressData()
    {
        $addresses = [
            'area' => $this->area,
            'region' => $this->region,
            'settlement' => $this->settlement,
            'settlement_type' => $this->settlement_type,
            'street_type' => $this->street_type,
            'street' => $this->street,
            'building' => $this->building,
            'apartment' => $this->apartment,
            'zip' => $this->zip,
        ];

        $this->dispatch('addressDataFetched', $addresses);
    }


    public function validateAddressData()
    {
       $this->validate();
    }


    public function searchKoatuuLevel2()
    {
        if (empty($this->area) && strlen($this->region) >= 3) {
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

        if (empty($this->region) && strlen($this->settlement) >= 3) {
            return ;
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
