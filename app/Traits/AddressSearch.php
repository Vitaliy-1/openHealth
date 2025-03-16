<?php

namespace App\Traits;

use App\Classes\eHealth\Api\AdressesApi;
use App\View\Components\Forms\AddressesSearch;
use Illuminate\Validation\ValidationException;

trait AddressSearch
{
    public ?array $address = [
        'country' => 'UA',
        'type' => 'RESIDENCE'
    ];

    public ?array $districts = [];

    public ?array $settlements = [];

    public ?array $streets = [];

    /**
     * Flag to indicate districts searching attempt
     *
     * @var bool
     */
    public bool $districtsSearching = false;

    /**
     * Flag to indicate settlements searching attempt
     *
     * @var bool
     */
    public bool $settlementsSearching = false;

    /**
     * Flag to indicate streets searching attempt
     *
     * @var bool
     */
    public bool $streetsSearching = false;

    public function addressValidation(): array
    {
        $errors = [];

        try {
            $this->validate(AddressesSearch::getAddressRules($this->address), AddressesSearch::getAddressMessages());
        } catch (ValidationException $err) {
            $errors = $err->validator->errors()->toArray();
        }

        return $errors;
    }

    public function updatedAddressArea($value)
    {
        if ($value === 'М.КИЇВ') {
            $this->address['region'] = '';
            $this->address['settlement'] = 'Київ';
            $this->address['settlementType'] = 'CITY';
            $this->address['settlementId'] = 'adaa4abf-f530-461c-bcbf-a0ac210d955b';
        }
    }

    public function setAddressesFields($addresses)
    {
        $this->updatedFields($addresses);
    }

    protected function updatedFields($addresses)
    {
        foreach ($addresses as $key => $address) {
            if (!empty($address)) {
                $this->address[$key] = $address;
            }
        }
    }

    // Reset fields for cases when different fields selection
    public function updated($field)
    {
        $fieldsToReset = match (substr($field, strrpos($field, '.') + 1)) {
            'area' => ['region', 'settlement', 'settlementId', 'settlementType', 'streetType', 'street', 'building', 'apartment', 'zip'],
            'region' => ['settlement', 'settlementId', 'settlementType', 'streetType', 'street', 'building', 'apartment', 'zip'],
            'settlement' => ['streetType', 'street', 'building', 'apartment', 'zip'],
            'street' => ['building', 'apartment', 'zip'],
            default => []
        };

        foreach ($fieldsToReset as $fieldToReset) {
            $this->address[$fieldToReset] = '';
        }
    }

    public function updatedAddressRegion($value)
    {
        $this->districts = [];
        $this->districtsSearching = !$this->districtsSearching;

        if (strlen($value) > 2) {
            $this->getDistricts();
        }
    }

    public function updatedAddressStreet($value)
    {
        $this->streets = [];
        $this->streetsSearching = !$this->streetsSearching;

        if (strlen($value) > 2) {
            $this->getStreets();
        }
    }

    public function updatedAddressSettlement($value)
    {
        $this->settlements = [];
        $this->settlementsSearching = !$this->settlementsSearching;

        if (strlen($value) > 2) {
            $this->getSettlements();
        }
    }

    public function selectDistrict($name)
    {
        $this->address['region'] = $name;
        $this->districts = [];
    }

    public function selectStreets($name)
    {
        $this->address['street'] = $name;
        $this->streets = [];
    }

    public function selectSettlements($name, $id)
    {
        $this->address['settlement'] = $name;
        $this->address['settlementId'] = $id;
        $this->settlements = [];
    }

    public function getAddressData(): array
    {
        return [
            'address' => $this->address,
            'rules' => $this->getAddressRules(),
            'messages' => $this->getAddressMessages()
        ];
    }

    public function getDistricts(): void
    {
        if (empty($this->address['area'])) {
            return;
        }

        $this->districts = AdressesApi::_districts($this->address['area'], $this->address['region']);
    }

    public function getSettlements(): void
    {
        if (empty($this->address['region'])) {
            return;
        }

        $this->settlements = AdressesApi::_settlements(
            $this->address['area'],
            $this->address['region'],
            $this->address['settlement']);
    }

    public function getStreets(): void
    {
        if (empty($this->address['settlementId'])) {
            return;
        }

        $this->streets = AdressesApi::_streets(
            $this->address['settlementId'],
            $this->address['streetType'],
            $this->address['street']
        );
    }
}
