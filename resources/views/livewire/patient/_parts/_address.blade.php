<div>
    <div class="py-4">
        <h3 class="font-medium text-2xl	text-black dark:text-white">
            {{ __('forms.patient_address') }}
        </h3>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="address.region" class="default-label">
                    {{ __('forms.region') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.address.region"
                               type="text"
                               id="address.region"/>
            </x-slot>
            @error('patient_request.address.region')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="address.city" class="default-label">
                    {{ __('forms.city') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.address.city"
                               type="text"
                               id="address.city"/>
            </x-slot>
            @error('patient_request.address.city')
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
                    class="default-input" wire:model="patient_request.address.street_type" type="text"
                    id="gender">
                    <x-slot name="option">
                        <option> {{ __('forms.select') }} {{ __('forms.street_type') }}</option>
                        @foreach($this->dictionaries['STREET_TYPE'] as $k => $street_type)
                            <option value="{{ $k }}">{{ $street_type }}</option>
                        @endforeach
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('patient_request.address.street_type')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="address.street_name" class="default-label">
                    {{ __('forms.street_name') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.address.street_name"
                               type="text"
                               id="address.street_name"/>
            </x-slot>
            @error('patient_request.address.street_name')
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
                <x-forms.label for="address.building" class="default-label">
                    {{ __('forms.building') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.address.building"
                               type="text"
                               id="address.building"/>
            </x-slot>
            @error('patient_request.address.building')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="address.apartment" class="default-label">
                    {{ __('forms.apartment') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.address.apartment"
                               type="text"
                               id="address.apartment"/>
            </x-slot>
            @error('patient_request.address.apartment')
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
                <x-forms.label for="address.zip_code" class="default-label">
                    {{ __('forms.zip_code') }}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.address.zip_code"
                               type="text"
                               id="address.zip_code"/>
            </x-slot>
            @error('patient_request.address.zip_code')
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
            <x-button wire:click="store('address')" type="submit" class="btn-primary d-flex max-w-[150px]">
                {{ __('forms.save') }}
            </x-button>
        </div>
    </div>
</div>
