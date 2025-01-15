<?php

namespace App\View\Components\Forms;

use Closure;
use App\Rules\Zip;
use Illuminate\View\Component;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;
use App\Classes\eHealth\Api\AdressesApi;

class AddressesSearch extends Component
{
    public array $address = [];

    public ?array $regions = [];

    public array $districts = [];

    public ?array $settlements = [];

    public ?array $streets = [];

    public string $class = '';

    public ?array $dictionaries;

    /**
     * Create a new component instance.
     */
    public function __construct($address, $districts, $settlements, $streets, $class)
    {
        $this->address = $address;

        $this->regions = AdressesApi::_regions();

        $this->districts = $districts;

        $this->settlements = $settlements;

        $this->streets = $streets;

        $this->class = $class;

        $this->dictionaries = dictionary()->getDictionaries(['SETTLEMENT_TYPE', 'STREET_TYPE'], true);
    }

    public static function getAddressRules(array $address): array
    {
        return [
            'address.area' => ['required', 'string'],
            'address.region' => [
                Rule::requiredIf(function () use($address) {
                    if (empty($address['area'])) {
                        return true;
                    }

                    return $address['area'] !== 'М.КИЇВ';
                }),
            ],
            'address.settlement' => ['required', 'string'],
            'address.settlement_type' => ['required', 'string'],
            'address.settlement_id' => ['required', 'string'],
            'address.street_type' => ['required', 'string'],
            'address.street' => ['required', 'string'],
            'address.building' => ['nullable', 'string'],
            'address.apartment' => ['nullable', 'string'],
            'address.zip' => ['nullable', 'string', new Zip()],
        ];
    }

    public static function getAddressMessages(): array
    {
        return [
            'address.area' => __("Поле 'Область' є обов’язковим"),
            'address.region' => __("Поле 'Район' є обов’язковим"),
            'address.settlement_type' => __("Поле 'Тип населеного пункту' є обов’язковим"),
            'address.settlement' => __("Поле 'Населений пункт' є обов’язковим"),
            'address.street_type' => __("Поле 'Тип вулиці' є обов’язковим"),
            'address.street' => __("Поле 'Вулиця' є обов’язковим"),
            'address.building' => __("Неправильний формат номеру будинка"),
            'address.apartment' => __("Неправильний формат номеру квартири"),
            'address.zip' => __("Неправильний формат поштового індекса"),
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.addresses-search');
    }
}
