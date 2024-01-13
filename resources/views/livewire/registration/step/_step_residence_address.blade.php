<x-slot name="title">
    {{  __('4. Адресса ') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>

</x-slot>
<div  x-data="{ lvl: 0 , area: '{{ $legal_entities->residence_address['area']?? '' }}', region:'{{ $legal_entities->residence_address['region']?? '' }}',settlement_type: '{{ $legal_entities->residence_address['settlement_type']?? '' }}', settlement: '{{ $legal_entities->residence_address['settlement']?? '' }}' }">
<div  class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_area"
                           name="label">
                {{__('forms.region')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
                <x-forms.select @change=" area = $event.target.value"
                                class="default-input"
                                wire:model="legal_entities.residence_address.area" type="text"
                                id="residence_address_area">
                    <x-slot name="option">
                        <option value="">{{__('forms.select')}}</option>
                        @if($koatuu_level1)
                            @foreach($koatuu_level1 as $lvl1)
                                <option {{ isset($legal_entities->residence_address['area']) && stripos($lvl1->name, $legal_entities->residence_address['area'])  ? 'selected' : '' }} value="{{ $lvl1->name }}">{{ $lvl1->name }}</option>
                            @endforeach
                        @endif
                    </x-slot>
                </x-forms.select>
        </x-slot>
        @error('legal_entities.residence_address.area')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group  x-data="{ open: false }"  class="xl:w-1/2 relative">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_region"
                           name="label">
                {{__('forms.area')}} *
            </x-forms.label>
        </x-slot>
        <x-slot  name="input">
            <div  x-on:mouseleave="timeout = setTimeout(() => { open = false }, 300)">
            <x-forms.input
                            wire:model="legal_entities.residence_address.region"
                            x-bind:value="region"

                            x-bind:disabled="area == '' ;"
                            wire:keyup="searchKoatuuLevel2; open = true"
                           class="default-input"
                            autocomplete="off"
                           type="text" id="residence_address_region"/>
            <div x-show="open" x-ref="dropdown"  wire:target="searchKoatuuLevel2">
                <div  class="z-10 max-h-96 overflow-auto w-full	 absolute  bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton">
                        @if($koatuu_level2)
                        @foreach($koatuu_level2 as $lvl2)
                            <li>
                                <a wire:click.prevent="setField('residence_address','region', '{{$lvl2->id}}');
                                   open = false;
                                   region = '{{$lvl2->name}}';
                                   lvl = '2'"
                                   href="#"
                                   class="pointer
                                   block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    {{$lvl2->name}}
                                </a>
                            </li>
                        @endforeach
                        @else
                            <li>
                                <a
                                   href="#"
                                   class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Немае записів
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            </div>
        </x-slot>
        @error('legal_entities.residence_address.region')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_settlement_type"
                           name="label">
                {{__('forms.settlement_type')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                x-bind:disabled="area === ''"
                class="default-input"
                @change=" settlement_type = $event.target.value"
                wire:model="legal_entities.residence_address.settlement_type"
                id="residence_address_settlement_type"
            >
                <x-slot name="option">
                    <option value="">{{__('forms.select')}}</option>
                    @isset($dictionaries['SETTLEMENT_TYPE'])
                        @foreach($dictionaries['SETTLEMENT_TYPE'] as $k=>$type)
                            <option  {{ isset($legal_entities->residence_address['settlement_type']) == $k ? 'selected': ''}} value="{{$k}}">{{$type}}</option>
                        @endforeach
                    @endif
                </x-slot>
            </x-forms.select>
        </x-slot>
        @error('legal_entities.residence_address.settlement_type')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group x-data="{ open: false }" class="xl:w-1/2 relative">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_settlement"
                           name="label">
                {{__('forms.settlement')}}
                *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <div  x-on:mouseleave="timeout = setTimeout(() => { open = false }, 300)">
                <x-forms.input  id="searchKoatuuLevel2"
                                x-bind:disabled="settlement_type === ''"
                                wire:keyup="searchKoatuuLevel3; open = true"
                                class="default-input"
                                x-bind:value="settlement"
                                autocomplete="off"
                                wire:model="legal_entities.residence_address.settlement"
                                type="text" id="residence_address_settlement"/>
                <div x-show="open"  wire:target="searchKoatuuLevel3">
                    <div  class="z-10 max-h-96 overflow-auto w-full	 absolute  bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton">
                            @if($koatuu_level3)
                                @foreach($koatuu_level3 as $lvl3)
                                    <li>
                                        <a href="#" wire:click.prevent="
                                        setField('residence_address','settlement', '{{$lvl3->id}}');
                                        open = false;
                                        settlement = '{{$lvl3->name}}';"
                                         class="pointer block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                            {{$lvl3->name}}
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li>
                                    <a
                                        href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        Немае записів
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </x-slot>
        @error('legal_entities.residence_address.settlement')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_street"
                           name="label">
                {{__('forms.street')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input
                class="default-input"
                x-bind:disabled="settlement == ''"
                wire:model="legal_entities.residence_address.street" type="text"
                id="residence_address_street"/>
        </x-slot>
        @error('legal_entities.residence_address.street')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_building"
                           name="label">
                {{__('forms.building')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"  x-bind:disabled="settlement === ''"
                           wire:model="legal_entities.residence_address.building"
                           type="text" id="residence_address_building"/>
        </x-slot>
        @error('legal_entities.residence_address.building')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_apartment"
                           name="label">
                {{__('forms.apartment')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input                 x-bind:disabled="settlement == ''"
                                           class="default-input" wire:model="legal_entities.residence_address.apartment"
                           type="text" id="residence_address_settlement_apartment"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_zip"
                           name="label">
                {{__('forms.zip_code')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input x-mask="99999"
                           x-bind:disabled="settlement == ''"
                           class="default-input" wire:model="legal_entities.residence_address.zip"
                           type="text" id="residence_address_settlement_zip"/>
        </x-slot>
    </x-forms.form-group>
</div>
</div>
