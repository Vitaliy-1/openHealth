<div>
    <div class="py-4">
        <h3 class="font-medium text-2xl	text-black dark:text-white">
            {{ __('forms.patient_information') }}
        </h3>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="first_name" class="default-label">
                    {{ __('forms.first_name') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.patient.first_name"
                               type="text"
                               id="first_name"
                />
            </x-slot>
            @error('patient_request.patient.first_name')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="last_name" class="default-label">
                    {{ __('forms.last_name') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.patient.last_name"
                               type="text"
                               id="last_name"
                />
            </x-slot>
            @error('patient_request.patient.last_name')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="second_name" class="default-label">
                    {{__('forms.second_name')}}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.patient.second_name"
                               type="text"
                               id="second_name"
                />
            </x-slot>
            @error('patient_request.patient.second_name')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="birth_date" class="default-label">
                    {{__('forms.birth_date')}} *
                </x-forms.label>
            </x-slot>

            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.patient.birth_date"
                               type="date"
                               id="birth_date"
                />
            </x-slot>
            @error('patient_request.patient.birth_date')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="last_name" class="default-label">
                    {{ __('forms.birth_country') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.patient.birth_country"
                               type="text"
                               id="birth_country"
                />
            </x-slot>
            @error('patient_request.patient.birth_country')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="birth_settlement" class="default-label">
                    {{ __('forms.birth_settlement') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.patient.birth_settlement"
                               type="text"
                               id="birth_settlement"
                />
            </x-slot>
            @error('patient_request.patient.birth_settlement')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col gap-0">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="gender" class="default-label">
                    {{ __('forms.gender') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.select class="default-input" wire:model="patient_request.patient.gender" type="text"
                                id="gender">
                    <x-slot name="option">
                        <option> {{ __('forms.select') }} {{ __('forms.gender') }}</option>
                        @foreach($this->dictionaries['GENDER'] as $k => $gender)
                            <option value="{{ $k }}">{{ $gender }}</option>
                        @endforeach
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('patient_request.patient.gender')
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
            <x-button wire:click="store('patient')" type="submit" class="btn-primary d-flex max-w-[150px]">
                {{ __('forms.save') }}
            </x-button>
        </div>
    </div>
</div>
