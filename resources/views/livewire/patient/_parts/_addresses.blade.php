
{{--<div>--}}
{{--    <livewire:components.addresses-search :addresses="$patient_request->addresses ?? []" :class="'mb-4 flex wrap flex-col flex-wrap gap-6 xl:flex-row'" />--}}
{{--</div>--}}



<div>
    <div class="py-4">
        <h3 class="font-medium text-2xl	text-black dark:text-white">
            {{ __('forms.patient_address') }}
        </h3>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="addresses.area" class="default-label">
                    {{ __('forms.region') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.addresses.area"
                               type="text"
                               id="addresses.area"/>
            </x-slot>
            @error('patient_request.addresses.area')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="addresses.settlement" class="default-label">
                    {{ __('forms.city') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.addresses.settlement"
                               type="text"
                               id="addresses.settlement"/>
            </x-slot>
            @error('patient_request.addresses.settlement')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="street_type" class="default-label">
                    {{ __('forms.street_type') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.select
                    class="default-input" wire:model="patient_request.addresses.street_type" type="text"
                    id="gender">
                    <x-slot name="option">
                        <option> {{ __('forms.select') }} {{ __('forms.street_type') }}</option>
                        @foreach($this->dictionaries['STREET_TYPE'] as $k => $street_type)
                            <option value="{{ $k }}">{{ $street_type }}</option>
                        @endforeach
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('patient_request.addresses.street_type')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="addresses.street" class="default-label">
                    {{ __('forms.street_name') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.addresses.street"
                               type="text"
                               id="addresses.street"/>
            </x-slot>
            @error('patient_request.addresses.street')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="addresses.building" class="default-label">
                    {{ __('forms.building') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.addresses.building"
                               type="text"
                               id="addresses.building"/>
            </x-slot>
            @error('patient_request.addresses.building')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="addresses.apartment" class="default-label">
                    {{ __('forms.apartment') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.addresses.apartment"
                               type="text"
                               id="addresses.apartment"/>
            </x-slot>
            @error('patient_request.addresses.apartment')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="addresses.zip" class="default-label">
                    {{ __('forms.zip_code') }}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.addresses.zip"
                               type="text"
                               id="addresses.zip"
                />
            </x-slot>
            @error('patient_request.addresses.zip')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col xl:flex-row justify-between items-center">
        <div class="xl:w-1/4 text-left"></div>
        <div class="xl:w-1/4 text-right">
            <x-button wire:click="store('addresses')" type="submit" class="btn-primary d-flex max-w-[150px]">
                {{ __('forms.save') }}
            </x-button>
        </div>
    </div>
</div>
