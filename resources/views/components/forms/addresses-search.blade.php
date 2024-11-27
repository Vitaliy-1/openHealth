<div class="{{ $class }}">
    <x-forms.form-group class="w-1/4">
        <x-slot name="label">
            <x-forms.label
                class="default-label"
                for="area"
                name="label"
            >
                {{-- TODO: Change to forms.area, Check for correctness description --}}
                {{ __('forms.region') }} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input"
                wire:model.live="address.area"
                id="area"
            >
                <x-slot name="option">
                    <option value="">{{ __('forms.select') }}</option>
                    @nonempty($regions)
                        @foreach ($regions as $region_item)
                            <option value="{{ $region_item['name'] }}"
                                {{ isset($address['area']) && stripos($region_item['name'], $address['area']) ? 'selected' : '' }}>
                                {{ $region_item['name'] }}
                            </option>
                        @endforeach
                    @endnonempty
                </x-slot>
            </x-forms.select>
        </x-slot>

        @error('address.area')
        <x-slot name="error">
            <x-forms.error>
                {{ $message }}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

    <!-- DISTRICT -->
    <x-forms.form-group class="w-1/4 relative" x-data="{ showTo: false}">
        <x-slot name="label">
            <x-forms.label
                class="default-label"
                for="region"
                name="label"
            >
                {{-- TODO: Change to forms.region, Check for correctness description --}}
              {{ __('forms.area') }} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <div
                x-data="{
                    srch: @entangle('districtsSearching'),
                    init() {
                        $watch('srch', (value) => {
                            console.log('In Watch');
                            this.showTo = true;
                        })
                    }
                }"
                x-on:mouseleave="timeout = setTimeout(() => { showTo = false }, 800)"
            >
                <x-forms.input
                    x-bind:disabled="{{ empty($address['area']) || $address['area'] === 'М.КИЇВ' ? 'true' : 'false' }}"
                    wire:model.live.debounce.400ms="address.region"
                    class="default-input"
                    autocomplete="off"
                    x-ref="regionField"
                    type="text"
                    id="region"
                />

                <template x-if="showTo">
                    <div
                        x-cloak
                        x-on:click.away="showTo = false"
                        x-transition
                        class="absolute z-10 bg-white border border-gray-300 w-full max-h-48 overflow-y-auto"
                    >
                        <ul>
                            @forelse ($districts as $district)
                                <li
                                    x-on:click="
                                        $refs.regionField.value = '{{ addslashes($district['name']) }}';
                                        $wire.call('selectDistrict', '{{ $district['name'] }}');
                                        showTo = false;
                                    "
                                    {{-- wire:click="selectDistrict('{{ $district['name'] }}'); showTo = false" --}}
                                    class="cursor-pointer px-4 py-2 hover:bg-gray-100"
                                >
                                    {{ $district['name'] }}
                                </li>
                            @empty
                                <li class="cursor-default px-4 py-2">
                                    {{ __('Нічого не знайдено') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </template>
            </div>
        </x-slot>

        @error('address.region')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
        @enderror
    </x-forms.form-group>

    <!-- TYPE -->
    <x-forms.form-group class="w-1/4">
        <x-slot name="label">
            <x-forms.label
                class="default-label"
                for="settlement_type"
                name="label"
            >
                {{ __('forms.settlementType') }} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input"
                x-bind:disabled="{{ empty($address['region']) ? 'true' : 'false' }}"
                wire:model.live="address.settlement_type"
                id="settlement_type"
            >
                <x-slot name="option">
                    <option value="">{{ __('forms.select') }}</option>
                    @isset($dictionaries['SETTLEMENT_TYPE'])
                        @foreach($dictionaries['SETTLEMENT_TYPE'] as $k => $type)
                            <option class="normal-case"
                                {{ isset($address['settlement_type']) && $address['settlement_type'] == $k ? 'selected': ''}} value="{{ $k }}">{{ $type }}
                            </option>
                        @endforeach
                    @endif
                </x-slot>
            </x-forms.select>
        </x-slot>

        @error('address.settlement_type')
        <x-slot name="error">
            <x-forms.error>
                {{ $message }}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

    <!-- SETTLEMENT -->
    <x-forms.form-group class="w-1/4 relative" x-data="{ showTo: false }">
        <x-slot name="label">
            <x-forms.label
                class="default-label"
                for="settlement"
                name="label"
            >
                {{__('forms.settlement')}} *
            </x-forms.label>
        </x-slot>

        <x-slot name="input">
            <div
                x-data="{
                    srch: @entangle('settlementsSearching'),
                    init() {
                        $watch('srch', (value) => {
                            console.log('In Watch');
                            this.showTo = true;
                        })
                    },
                    setInputValue(value) { $refs.settlementField.value = value; }
                }"
                x-on:mouseleave="timeout = setTimeout(() => { showTo = false }, 800)"
            >
                <x-forms.input
                    x-bind:disabled="{{ empty($address['settlement_type']) || $address['area'] === 'М.КИЇВ' ? 'true' : 'false' }}"
                    wire:model.live.debounce.400ms="address.settlement"
                    class="default-input"
                    x-ref="settlementField"
                    autocomplete="off"
                    type="text"
                    id="settlement"
                />

                <template x-if="showTo">
                    <div
                        x-cloak
                        x-on:click.away="showTo = false"
                        x-transition
                        class="z-10 max-h-96 overflow-auto w-full absolute bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700"
                    >
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton">
                            @forelse ($settlements as $settlement)
                                <li
                                    x-on:click="
                                        $refs.settlementField.value = '{{ addslashes($settlement['name']) }}';
                                        $wire.call('selectSettlements', '{{ $settlement['name'] }}', '{{ $settlement['id'] }}');
                                        showTo = false;
                                    "
                                    class="cursor-pointer px-4 py-2 hover:bg-gray-100"
                                >
                                    {{ $settlement['name'] }}
                                </li>
                            @empty
                                <li class="cursor-default px-4 py-2">
                                    {{ __('Нічого не знайдено') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </template>
            </div>
        </x-slot>


        @error('address.settlement')
        <x-slot name="error">
            <x-forms.error>
                {{ $message }}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

    <!-- STREET_TYPE -->
    <x-forms.form-group class="w-1/4">
        <x-slot name="label">
            <x-forms.label
                class="default-label"
                for="street_type"
                name="label"
            >
                {{__('forms.streetType')}} *
            </x-forms.label>
        </x-slot>

        <x-slot name="input">
            <x-forms.select
                class="default-input"
                x-bind:disabled="{{ empty($address['settlement']) ? 'true' : 'false' }}"
                wire:model.live="address.street_type"
                id="street_type"

            >
                <x-slot name="option">
                    <option value="">{{__('forms.select')}}</option>
                    @if($dictionaries['STREET_TYPE'])
                        @foreach($dictionaries['STREET_TYPE'] as $k => $type)
                            <option class="normal-case"
                                {{ isset($address['street_type']) && $address['street_type'] == $k ? 'selected': ''}} value="{{ $k }}">{{ $type }}</option>
                        @endforeach
                    @endif
                </x-slot>
            </x-forms.select>
        </x-slot>

        @error('address.street_type')
        <x-slot name="error">
            <x-forms.error>
                {{ $message }}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

    <!-- STREET -->
    <x-forms.form-group class="w-1/4 relative" x-data="{ showTo: false }">
        <x-slot name="label">
            <x-forms.label
                class="default-label"
                for="street"
                name="label"
            >
                {{ __('forms.street') }} *
            </x-forms.label>
        </x-slot>

        <x-slot name="input">
            <div
                x-data="{
                    srch: @entangle('streetsSearching'),
                    init() {
                        $watch('srch', (value) => {
                            console.log('In Watch');
                            this.showTo = true;
                        })
                    }
                }"
                x-on:mouseleave="timeout = setTimeout(() => { showTo = false }, 800)"
            >
                <x-forms.input
                    x-bind:disabled="{{ empty($address['settlement_type']) ? 'true' : 'false' }}"
                    wire:model.live.debounce.400ms="address.street"
                    class="default-input"
                    autocomplete="off"
                    type="text"
                    x-ref="streetField"
                    id="street"
                />

                <template x-if="showTo">
                    <div
                        x-cloak
                        x-on:click.away="showTo = false"
                        x-transition
                        class="z-10 max-h-96 overflow-auto w-full absolute bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700"
                    >
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton">
                            @forelse ($streets as $street)
                                <li
                                    x-on:click="
                                        $refs.streetField.value = '{{ addslashes($street['name']) }}';
                                        $wire.call('selectStreets', '{{ $street['name'] }}');
                                        showTo = false;
                                    "
                                    class="cursor-pointer px-4 py-2 hover:bg-gray-100"
                                >
                                    {{ $street['name'] }}
                                </li>
                            @empty
                                <li class="cursor-default px-4 py-2">
                                    {{ __('Нічого не знайдено') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </template>
            </div>
        </x-slot>

        @error('address.street')
        <x-slot name="error">
            <x-forms.error>
                {{ $message }}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

    <!-- BUILDING -->
    <x-forms.form-group class="w-1/4">
        <x-slot name="label">
            <x-forms.label
                class="default-label"
                for="building"
                name="label"
            >
                {{ __('forms.building') }}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input
                class="default-input"
                wire:model="address.building"
                x-bind:disabled="{{ empty($address['settlement']) ? 'true' : 'false' }}"
                type="text"
                id="building"
            />
        </x-slot>

        @error('address.building')
        <x-slot name="error">
            <x-forms.error>
                {{ $message }}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

    <!-- APARTMENT -->
    <x-forms.form-group class="w-1/4">
        <x-slot name="label">
            <x-forms.label
                class="default-label"
                for="apartment"
                name="label"
            >
                {{ __('forms.apartment') }}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input
                class="default-input"
                wire:model="address.apartment"
                x-bind:disabled="{{ empty($address['settlement']) ? 'true' : 'false' }}"
                type="text"
                id="apartment"
            />
        </x-slot>

        @error('address.apartment')
        <x-slot name="error">
            <x-forms.error>
                {{ $message }}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

    <!-- ZIP -->
    <x-forms.form-group class="w-1/4">
        <x-slot name="label">
            <x-forms.label
                class="default-label"
                for="zip"
                name="label"
            >
                {{__('forms.zipCode')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input
                x-mask="99999"
                class="default-input"
                wire:model="address.zip"
                x-bind:disabled="{{ empty($address['settlement']) ? 'true' : 'false' }}"
                type="text"
                id="zip"
            />
        </x-slot>

        @error('address.zip')
        <x-slot name="error">
            <x-forms.error>
                {{ $message }}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</div>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        Livewire.on('address-data-fetched', function () {
            Livewire.emit('checkAndProceedToNextStep');
        });
    });
</script> --}}
