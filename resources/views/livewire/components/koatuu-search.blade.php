<div>
    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label class="default-label" for="area"
                               name="label">
                    {{__('forms.region')}} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.select
                    class="default-input"
                    wire:model.live="area"
                    id="area">
                    <x-slot name="option">
                        <option value="">{{__('forms.select')}}</option>
                        @if($koatuu_level1)
                            @foreach($koatuu_level1 as $lvl1)
                                <option
                                    {{ isset($addresseses['area']) && stripos($lvl1->name, $addresseses['area'])  ? 'selected' : '' }} value="{{ $lvl1->name }}">{{ $lvl1->name }}</option>
                            @endforeach
                        @endif
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('division.addresses.area')
            <x-slot name="error">
                <x-forms.error>
                    {{$message}}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
        <x-forms.form-group x-data="{ open: false }" class="xl:w-1/2 relative">
            <x-slot name="label">
                <x-forms.label class="default-label" for="region"
                               name="label">
                    {{__('forms.area')}} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <div x-on:mouseleave="timeout = setTimeout(() => { open = false }, 300)">
                    <x-forms.input
                        wire:model.live="region"
                        x-bind:disabled="{{empty($area)}}"
                        wire:keyup="searchKoatuuLevel2; open = true"
                        class="default-input"
                        autocomplete="off"
                        type="text" id="region"/>
                    <div x-show="open" x-ref="dropdown" wire:target="searchKoatuuLevel2">
                        <div
                            class="z-10 max-h-96 overflow-auto w-full	 absolute  bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownHoverButton">
                                @if($koatuu_level2)
                                    @foreach($koatuu_level2 as $lvl2)
                                        <li>
                                            <a x-on:click.prevent="
                                            $wire.set('region', '{{$lvl2->name}}');
                                            open = false;"
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
            @error('region')
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
                <x-forms.label class="default-label" for="type"
                               name="label">
                    {{__('forms.type')}} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.select
                    class="default-input"
                    x-bind:disabled="{{empty($region)}}"
                    wire:model.live="settlement_type"
                    id="type"
                >
                    <x-slot name="option">
                        <option value="">{{__('forms.select')}}</option>
                        @isset($dictionaries['SETTLEMENT_TYPE'])
                            @foreach($dictionaries['SETTLEMENT_TYPE'] as $k=>$type)
                                <option
                                    {{ isset($addresseses['type']) == $k ? 'selected': ''}} value="{{$k}}">{{$type}}</option>
                            @endforeach
                        @endif
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('type')
            <x-slot name="error">
                <x-forms.error>
                    {{$message}}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
        <x-forms.form-group x-data="{ open: false }" class="xl:w-1/2 relative">
            <x-slot name="label">
                <x-forms.label class="default-label" for="settlement"
                               name="label">
                    {{__('forms.settlement')}}
                    *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <div x-on:mouseleave="timeout = setTimeout(() => { open = false }, 300)">
                    <x-forms.input id="searchKoatuuLevel2"
                                   wire:keyup="searchKoatuuLevel3; open = true"
                                   class="default-input"
                                   autocomplete="off"
                                   x-bind:disabled="{{empty($settlement_type)}}"
                                   wire:model.live="settlement"
                                   type="text"
                                   id="settlement"/>
                    <div x-show="open" wire:target="searchKoatuuLevel3">
                        <div
                            class="z-10 max-h-96 overflow-auto w-full	 absolute  bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownHoverButton">
                                @if($koatuu_level3)
                                    @foreach($koatuu_level3 as $lvl3)
                                        <li>
                                            <a href="#" x-on:click.prevent="
                                              $wire.set('settlement', '{{$lvl3->name}}');
                                             open = false; "
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
            @error('settlement')
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
                <x-forms.label class="default-label" for="street"
                               name="label">
                    {{__('forms.street')}} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input
                    class="default-input"
                    x-bind:disabled="{{empty($settlement)}}"

                    wire:model.live="street" type="text"
                    id="street"/>
            </x-slot>
            @error('street')
            <x-slot name="error">
                <x-forms.error>
                    {{$message}}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label class="default-label" for="building"
                               name="label">
                    {{__('forms.building')}} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model.live="building"
                               x-bind:disabled="{{empty($settlement)}}"
                               type="text" id="building"/>
            </x-slot>
            @error('building')
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
                <x-forms.label class="default-label" for="apartment"
                               name="label">
                    {{__('forms.apartment')}}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input
                    class="default-input"
                    wire:model.live="apartment"
                    x-bind:disabled="{{empty($settlement)}}"
                    type="text" id="apartment"/>
            </x-slot>
        </x-forms.form-group>
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label class="default-label" for="zip"
                               name="label">
                    {{__('forms.zip_code')}}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input x-mask="99999"
                               class="default-input"
                               wire:model.live="zip"
                               x-bind:disabled="{{empty($settlement)}}"
                               type="text" id="zip"/>
            </x-slot>
        </x-forms.form-group>
    </div>
</div>
{{-- Do your work, then step back. --}}

