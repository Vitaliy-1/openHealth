<div>
    <div class="py-4">
        <h3 class="font-medium text-2xl	text-black dark:text-white">
            {{ __('forms.emergency_contact') }}
        </h3>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="emergency_contact.first_name" class="default-label">
                    {{ __('forms.first_name') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.emergency_contact.first_name"
                               type="text"
                               id="emergency_contact.first_name"
                />
            </x-slot>
            @error('patient_request.emergency_contact.first_name')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="emergency_contact.last_name" class="default-label">
                    {{ __('forms.last_name') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.emergency_contact.last_name"
                               type="text"
                               id="emergency_contact.last_name"
                />
            </x-slot>
            @error('patient_request.emergency_contact.last_name')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="emergency_contact.second_name" class="default-label">
                    {{__('forms.second_name')}}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.emergency_contact.second_name"
                               type="text"
                               id="emergency_contact.second_name"
                />
            </x-slot>
            @error('patient_request.emergency_contact.second_name')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4">
        <x-forms.label name="label" class="default-label">
            {{ __('forms.phones') }}
        </x-forms.label>

        <x-forms.form-group class="mb-2">
            <x-slot name="label">
                <div class="flex-row flex gap-6 items-center">
                    <div class="w-1/4">
                        <x-forms.select
                            wire:model.defer="patient_request.emergency_contact.phones.type"
                            class="default-select">
                            <x-slot name="option">
                                <option>{{ __('forms.phone_type') }}</option>
                                @foreach($this->dictionaries['PHONE_TYPE'] as $k => $phone_type)
                                    <option value="{{ $k }}">{{ $phone_type }}</option>
                                @endforeach
                            </x-slot>
                        </x-forms.select>
                        @error("patient_request.emergency_contact.phones.type")
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                        @enderror
                    </div>
                    <div class="w-1/4">
                        <x-forms.input x-mask="+380999999999" class="default-input"
                                       wire:model="patient_request.emergency_contact.phones.number"
                                       type="text"
                                       placeholder="{{ __('+ 3(80)00 000 00 00 ') }}"
                        />

                        @error("patient_request.emergency_contact.phones.number")
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                        @enderror
                    </div>
                </div>
            </x-slot>
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col xl:flex-row justify-between items-center">
        <div class="xl:w-1/4 text-left"></div>
        <div class="xl:w-1/4 text-right">
            <x-button wire:click="store('emergency_contact')" type="submit" class="btn-primary d-flex max-w-[150px]">
                {{ __('forms.save') }}
            </x-button>
        </div>
    </div>
</div>
