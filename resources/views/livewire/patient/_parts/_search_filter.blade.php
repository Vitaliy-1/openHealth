<div x-show="showFilter">
    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="patientRequest.patientsFilter.firstName"
                   type="text"
                   name="filterFirstName"
                   id="filterFirstName"
                   class="input peer @error('patientRequest.patientsFilter.firstName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />

            <label for="filterFirstName" class="label">
                {{ __('forms.firstName') }}
            </label>

            @error('patientRequest.patientsFilter.firstName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="patientRequest.patientsFilter.lastName"
                   type="text"
                   name="filterLastName"
                   id="filterLastName"
                   class="input peer @error('patientRequest.patientsFilter.lastName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />

            <label for="filterFirstName" class="label">
                {{ __('forms.lastName') }}
            </label>

            @error('patientRequest.patientsFilter.lastName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <svg class="svg-input" width="20" height="20">
                <use xlink:href="#svg-calendar-week"></use>
            </svg>

            <input wire:model="patientRequest.patientsFilter.birthDate"
                   type="text"
                   name="filterBirthDate"
                   id="filterBirthDate"
                   class="datepicker-input input peer @error('patientRequest.patientsFilter.birthDate') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />

            <label for="filterBirthDate" class="label">
                {{__('forms.birthDate')}}
            </label>

            @error('patientRequest.patientsFilter.birthDate')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>

    <div x-data="{ showAdditionalParams: false }">
        <button class="flex items-center gap-2 gray-button"
                @click="showAdditionalParams = !showAdditionalParams"
        >
            <svg width="16" height="16">
                <use xlink:href="#svg-adjustments"></use>
            </svg>
            <span>{{ __('Додаткові параметри пошуку') }}</span>
        </button>

        <template x-if="showAdditionalParams">
            <div>
                <div class="form-row-3">
                    <div class="form-group group">
                        <input wire:model="patientRequest.patientsFilter.secondName"
                               type="text"
                               name="filterSecondName"
                               id="filterSecondName"
                               class="input peer @error('patientRequest.patientsFilter.secondName') input-error @enderror"
                               placeholder=" "
                               autocomplete="off"
                        />

                        <label for="filterSecondName" class="label">
                            {{ __('forms.secondName') }}
                        </label>

                        @error('patientRequest.patientsFilter.secondName')
                        <p class="text-error">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="form-group group">
                        <input wire:model="patientRequest.patientsFilter.taxId"
                               type="text"
                               name="filterTaxId"
                               id="filterTaxId"
                               class="input peer @error('patientRequest.patientsFilter.taxId') input-error @enderror"
                               placeholder=" "
                               maxlength="10"
                               autocomplete="off"
                        />

                        <label for="filterTaxId" class="label">
                            {{ __('forms.RNOCPP') }} ({{ __('forms.ipn') }})
                        </label>

                        @error('patientRequest.patientsFilter.taxId')
                        <p class="text-error">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group group">
                        <input wire:model="patientRequest.patientsFilter.phoneNumber"
                               name="filterPhoneNumber"
                               id="filterPhoneNumber"
                               type="text"
                               class="input peer @error('patientRequest.patientsFilter.phoneNumber') input-error @enderror"
                               placeholder=" "
                               autocomplete="off"
                        />

                        <label for="filterPhoneNumber" class="label">
                            {{ __('forms.phone_number') }}
                        </label>

                        @error('patientRequest.patientsFilter.phoneNumber')
                        <p class="text-error">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="form-group group">
                        <input wire:model="patientRequest.patientsFilter.birthCertificate"
                               type="text"
                               name="filterBirthCertificate"
                               id="filterBirthCertificate"
                               class="input peer @error('patientRequest.patientsFilter.birthCertificate') input-error @enderror"
                               placeholder=" "
                               autocomplete="off"
                        />

                        <label for="filterBirthCertificate" class="label">
                            {{ __('forms.birthCertificate') }}
                        </label>

                        @error('patientRequest.patientsFilter.birthCertificate')
                        <p class="text-error">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
