<div>
    <div
        class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ __('forms.emergencyContact') }}
        </h5>

        <x-forms.form-row>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="emergency_contact_first_name" class="default-label">
                        {{ __('forms.firstName') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.emergencyContact.firstName"
                                   type="text"
                                   id="emergency_contact_first_name"
                    />
                </x-slot>

                @error('patientRequest.patient.emergencyContact.firstName')
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
                        {{ __('forms.lastName') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.emergencyContact.lastName"
                                   type="text"
                                   id="emergency_contact_last_name"
                    />
                </x-slot>

                @error('patientRequest.patient.emergencyContact.lastName')
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
                        {{ __('forms.secondName') }}
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.emergencyContact.secondName"
                                   type="text"
                                   id="emergency_contact_second_name"
                    />
                </x-slot>

                @error('patientRequest.patient.emergencyContact.secondName')
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
                {{ __('forms.phones') }} *
            </x-forms.label>

            <x-forms.form-phone :phones="$patientRequest->patient['emergencyContact']['phones'] ?? []"
                                :property="'patientRequest.patient.emergencyContact'"
            />
        </x-forms.form-row>

        @include('livewire.patient._parts._addresses')

    </div>
</div>
