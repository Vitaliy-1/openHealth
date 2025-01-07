<div>
    <div
        class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ __('forms.patientInformation') }}
        </h5>

        <x-forms.form-row>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="patient_first_name" class="default-label">
                        {{ __('forms.firstName') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.firstName"
                                   type="text"
                                   id="patient_first_name"
                    />
                </x-slot>

                @error('patientRequest.patient.firstName')
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
                        {{ __('forms.lastName') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.lastName"
                                   type="text"
                                   id="patient_last_name"
                    />
                </x-slot>

                @error('patientRequest.patient.lastName')
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
                        {{ __('forms.secondName') }}
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.secondName"
                                   type="text"
                                   id="patient_second_name"
                    />
                </x-slot>

                @error('patientRequest.patient.secondName')
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
                        {{ __('forms.birthDate') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input-date wire:model="patientRequest.patient.birthDate"
                                        id="birth_date"
                    />
                </x-slot>

                @error('patientRequest.patient.birthDate')
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
                        {{ __('forms.birthCountry') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.birthCountry"
                                   type="text"
                                   id="birth_country"
                    />
                </x-slot>

                @error('patientRequest.patient.birthCountry')
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
                        {{ __('forms.birthSettlement') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.birthSettlement"
                                   type="text"
                                   id="birth_settlement"
                    />
                </x-slot>

                @error('patientRequest.patient.birthSettlement')
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

            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="unzr" class="default-label">
                        {{ __('forms.UNZR') }}
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   maxlength="14"
                                   wire:model="patientRequest.patient.unzr"
                                   type="text"
                                   id="unzr"
                    />
                </x-slot>

                @error('patientRequest.patient.unzr')
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
