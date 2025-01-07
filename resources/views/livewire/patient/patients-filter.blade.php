<div>
    <div class="text-gray-900 text-xl leading-normal mt-6">{{ __('forms.patientLegalRepresentative') }}</div>
    <div x-show="showFilter" class="mt-6">
        <div class="w-[90%]">
            <div class="grid gap-6 mb-8 md:grid-cols-3">
                <x-forms.form-group>
                    <x-slot name="input">
                        <x-forms.input-with-icon id="filter_first_name"
                                                 wire:model="patientsFilter.firstName"
                                                 type="text"
                                                 autocomplete="off"
                                                 placeholder="{{ __('forms.firstName') }}"
                                                 svgId="svg-x"
                        />
                    </x-slot>

                    @error('patientsFilter.firstName')
                    <x-slot name="error">
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                    </x-slot>
                    @enderror
                </x-forms.form-group>

                <x-forms.form-group>
                    <x-slot name="input">
                        <x-forms.input-with-icon id="filter_last_name"
                                                 wire:model="patientsFilter.lastName"
                                                 autocomplete="off"
                                                 type="text"
                                                 placeholder="{{ __('forms.lastName') }}"
                                                 svgId="svg-x"
                        />
                    </x-slot>

                    @error('patientsFilter.lastName')
                    <x-slot name="error">
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                    </x-slot>
                    @enderror
                </x-forms.form-group>

                <x-forms.form-group>
                    <x-slot name="input">
                        <x-forms.input-date :maxDate="now()->subYears(14)->format('Y-m-d')"
                                            id="filter_birth_date"
                                            wire:model="patientsFilter.birthDate"
                                            type="text"
                                            placeholder="{{ __('forms.birthDate') }}"
                        />
                    </x-slot>

                    @error('patientsFilter.birthDate')
                    <x-slot name="error">
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                    </x-slot>
                    @enderror
                </x-forms.form-group>
            </div>
        </div>

        <x-forms.form-group>
            <x-slot name="label">
                <x-forms.button-with-icon wire:click="$toggle('showAdditionalParams')"
                                          class="gray-button"
                                          label="{{ __('Додаткові параметри пошуку') }}"
                                          svgId="svg-adjustments"
                />
            </x-slot>
        </x-forms.form-group>

        @if($showAdditionalParams)
            <div class="w-[60%]">
                <div class="grid gap-6 mb-4 mt-8 md:grid-cols-2">
                    <x-forms.form-group>
                        <x-slot name="input">
                            <x-forms.input-with-icon id="filter_second_name"
                                                     wire:model="patientsFilter.secondName"
                                                     autocomplete="off"
                                                     type="text"
                                                     placeholder="{{ __('forms.secondName') }}"
                                                     svgId="svg-x"
                            />
                        </x-slot>
                    </x-forms.form-group>

                    <x-forms.form-group>
                        <x-slot name="input">
                            <x-forms.input-with-icon id="filter_tax_id"
                                                     wire:model="patientsFilter.taxId"
                                                     autocomplete="off"
                                                     type="text"
                                                     placeholder="{{ __('forms.RNOCPP') }} ({{ __('forms.ipn') }})"
                                                     svgId="svg-x"
                            />
                        </x-slot>
                    </x-forms.form-group>

                    <x-forms.form-group>
                        <x-slot name="input">
                            <x-forms.input-with-icon id="filter_phone_number"
                                                     wire:model="patientsFilter.phoneNumber"
                                                     autocomplete="off"
                                                     type="text"
                                                     placeholder="{{ __('forms.phone') }}"
                                                     svgId="svg-x"
                            />
                        </x-slot>
                    </x-forms.form-group>

                    <x-forms.form-group>
                        <x-slot name="input">
                            <x-forms.input-with-icon id="filter_birth_certificate"
                                                     wire:model="patientsFilter.birthCertificate"
                                                     autocomplete="off"
                                                     type="text"
                                                     placeholder="{{ __('forms.birthCertificate') }}"
                                                     svgId="svg-x"
                            />
                        </x-slot>
                    </x-forms.form-group>
                </div>
            </div>
        @endif
        <x-forms.form-group class="py-4">
            <x-slot name="label">
                <x-forms.button-with-icon wire:click="searchForPerson"
                                          class="default-button"
                                          label="{{ __('Шукати представника') }}"
                                          svgId="svg-search"
                />
            </x-slot>
        </x-forms.form-group>
    </div>
</div>
