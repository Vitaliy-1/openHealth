<div x-show="showFilter">
    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="form.patientsFilter.firstName"
                   type="text"
                   name="filterFirstName"
                   id="filterFirstName"
                   class="input peer @error('form.patientsFilter.firstName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />

            <label for="filterFirstName" class="label">
                {{ __('forms.first_name') }}
            </label>

            @error('form.patientsFilter.firstName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="form.patientsFilter.lastName"
                   type="text"
                   name="filterLastName"
                   id="filterLastName"
                   class="input peer @error('form.patientsFilter.lastName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />

            <label for="filterFirstName" class="label">
                {{ __('forms.last_name') }}
            </label>

            @error('form.patientsFilter.lastName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <svg class="svg-input" width="20" height="20">
                <use xlink:href="#svg-calendar-week"></use>
            </svg>

            <input wire:model="form.patientsFilter.birthDate"
                   datepicker-max-date="{{ now()->format('Y-m-d') }}"
                   type="text"
                   name="filterBirthDate"
                   id="filterBirthDate"
                   class="datepicker-input input peer @error('form.patientsFilter.birthDate') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />

            <label for="filterBirthDate" class="label">
                {{ __('forms.birth_date') }}
            </label>

            @error('form.patientsFilter.birthDate')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>

    <div x-data="{ showAdditionalParams: false }">
        <button class="flex items-center gap-2 gray-button"
                @click.prevent="showAdditionalParams = !showAdditionalParams"
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
                        <input wire:model="form.patientsFilter.secondName"
                               type="text"
                               name="filterSecondName"
                               id="filterSecondName"
                               class="input peer @error('form.patientsFilter.secondName') input-error @enderror"
                               placeholder=" "
                               autocomplete="off"
                        />

                        <label for="filterSecondName" class="label">
                            {{ __('forms.second_name') }}
                        </label>

                        @error('form.patientsFilter.secondName')
                        <p class="text-error">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="form-group group">
                        <input wire:model="form.patientsFilter.taxId"
                               type="text"
                               name="filterTaxId"
                               id="filterTaxId"
                               class="input peer @error('form.patientsFilter.taxId') input-error @enderror"
                               placeholder=" "
                               maxlength="10"
                               autocomplete="off"
                        />

                        <label for="filterTaxId" class="label">
                            {{ __('forms.rnokpp') }} ({{ __('forms.ipn') }})
                        </label>

                        @error('form.patientsFilter.taxId')
                        <p class="text-error">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group group">
                        <input wire:model="form.patientsFilter.phoneNumber"
                               name="filterPhoneNumber"
                               id="filterPhoneNumber"
                               type="text"
                               class="input peer @error('form.patientsFilter.phoneNumber') input-error @enderror"
                               placeholder=" "
                               autocomplete="off"
                        />

                        <label for="filterPhoneNumber" class="label">
                            {{ __('forms.phone_number') }}
                        </label>

                        @error('form.patientsFilter.phoneNumber')
                        <p class="text-error">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="form-group group">
                        <input wire:model="form.patientsFilter.birthCertificate"
                               type="text"
                               name="filterBirthCertificate"
                               id="filterBirthCertificate"
                               class="input peer @error('form.patientsFilter.birthCertificate') input-error @enderror"
                               placeholder=" "
                               autocomplete="off"
                        />

                        <label for="filterBirthCertificate" class="label">
                            {{ __('forms.birth_certificate') }}
                        </label>

                        @error('form.patientsFilter.birthCertificate')
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
