<div>
    <div
        class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ __('forms.emergency_contact') }}
        </h5>

        <x-forms.form-row>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="emergency_contact_first_name" class="default-label">
                        {{ __('forms.first_name') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.emergency_contact.firstName"
                                   type="text"
                                   id="emergency_contact_first_name"
                    />
                </x-slot>

                @error('patientRequest.patient.emergency_contact.firstName')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>

            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="emergency_contact_last_name" class="default-label">
                        {{ __('forms.last_name') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.emergency_contact.lastName"
                                   type="text"
                                   id="emergency_contact_last_name"
                    />
                </x-slot>

                @error('patientRequest.patient.emergency_contact.lastName')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>

            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="emergency_contact_second_name" class="default-label">
                        {{ __('forms.second_name') }}
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.emergency_contact.secondName"
                                   type="text"
                                   id="emergency_contact_second_name"
                    />
                </x-slot>

                @error('patientRequest.patient.emergency_contact.secondName')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>

        <x-forms.form-row cols="flex-col" gap="gap-0">
            <x-forms.label name="label" class="default-label">
                {{ __('forms.phones') }}
            </x-forms.label>

            <x-forms.form-group class="mb-2">
                <x-slot name="label">
                    <div class="flex-row flex gap-6 items-center">
                        <div class="w-1/4">
                            <x-forms.select wire:model.defer="patientRequest.patient.emergency_contact.phones.type"
                                            class="default-select">
                                <x-slot name="option">
                                    <option>{{ __('forms.phone_type') }}</option>
                                    @foreach($this->dictionaries['PHONE_TYPE'] as $key => $phone_type)
                                        <option value="{{ $key }}">{{ $phone_type }}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>

                            @error("patientRequest.patient.emergency_contact.phones.type")
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                            @enderror
                        </div>
                        <div class="w-1/4">
                            <x-forms.input class="default-input"
                                           x-mask="+380999999999"
                                           wire:model="patientRequest.patient.emergency_contact.phones.number"
                                           type="text"
                                           placeholder="{{ __('+ 3(80)00 000 00 00 ') }}"
                            />

                            @error("patientRequest.patient.emergency_contact.phones.number")
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                            @enderror
                        </div>
                    </div>
                </x-slot>
            </x-forms.form-group>
        </x-forms.form-row>

        @include('livewire.patient._parts._addresses')

    </div>
</div>
