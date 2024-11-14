<div>
    <div
        class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ __('forms.patient_information') }}
        </h5>

        <x-forms.form-row>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="patient_first_name" class="default-label">
                        {{ __('forms.first_name') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.first_name"
                                   type="text"
                                   id="patient_first_name"
                    />
                </x-slot>

                @error('patientRequest.patient.first_name')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>

            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="patient_last_name" class="default-label">
                        {{ __('forms.last_name') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.last_name"
                                   type="text"
                                   id="patient_last_name"
                    />
                </x-slot>

                @error('patientRequest.patient.last_name')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>

            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="patient_second_name" class="default-label">
                        {{ __('forms.second_name') }}
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.second_name"
                                   type="text"
                                   id="patient_second_name"
                    />
                </x-slot>

                @error('patientRequest.patient.second_name')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>

        <x-forms.form-row>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="birth_date" class="default-label">
                        {{ __('forms.birth_date') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input-date :maxDate="now()->subYears(18)->format('Y-m-d')"
                                        wire:model="patientRequest.patient.birth_date"
                                        id="birth_date"
                    />
                </x-slot>

                @error('patientRequest.patient.birth_date')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>

            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="birth_country" class="default-label">
                        {{ __('forms.birth_country') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.birth_country"
                                   type="text"
                                   id="birth_country"
                    />
                </x-slot>

                @error('patientRequest.patient.birth_country')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>

            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="birth_settlement" class="default-label">
                        {{ __('forms.birth_settlement') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.birth_settlement"
                                   type="text"
                                   id="birth_settlement"
                    />
                </x-slot>

                @error('patientRequest.patient.birth_settlement')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>

        <x-forms.form-row class="flex-col" gap="gap-1">
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="gender" class="default-label">
                        {{ __('forms.gender') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.select class="default-input"
                                    wire:model="patientRequest.patient.gender"
                                    type="text"
                                    id="gender"
                    >
                        <x-slot name="option">
                            <option>
                                {{ __('forms.select') }} {{ __('forms.gender') }}
                            </option>

                            @foreach($this->dictionaries['GENDER'] as $key => $gender)
                                <option value="{{ $key }}">{{ $gender }}</option>
                            @endforeach
                        </x-slot>
                    </x-forms.select>
                </x-slot>

                @error('patientRequest.patient.gender')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>
    </div>
</div>
