<div>
    <div x-show="showFilter"
         x-transition
         class="mt-6">
        <div class="w-[60%]">
            <div class="grid gap-6 mb-6 md:grid-cols-4">
                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="filter_first_name">
                            {{ __('forms.firstName') }} *
                        </x-forms.label>
                    </x-slot>

                    <x-slot name="input">
                        <x-forms.input id="filter_first_name"
                                       wire:model="patientsFilter.firstName" type="text"
                                       autocomplete="off"
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
                    <x-slot name="label">
                        <x-forms.label for="filter_last_name">
                            {{ __('forms.lastName') }} *
                        </x-forms.label>
                    </x-slot>

                    <x-slot name="input">
                        <x-forms.input id="filter_last_name"
                                       wire:model="patientsFilter.lastName" type="text"
                                       autocomplete="off"
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
                    <x-slot name="label">
                        <x-forms.label for="filter_birth_date">
                            {{ __('forms.birthDate') }} *
                        </x-forms.label>
                    </x-slot>

                    <x-slot name="input">
                        <x-forms.input-date :maxDate="now()->subYears(14)->format('Y-m-d')"
                                            id="filter_birth_date"
                                            wire:model="patientsFilter.birthDate" type="text"
                                            autocomplete="off"
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

                <x-forms.form-group class="flex justify-center">
                    <x-slot name="label">
                        <button wire:click="searchPerson" class="btn btn-primary">
                            {{ __('Шукати') }}
                        </button>
                    </x-slot>
                </x-forms.form-group>

                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="filter_second_name">
                            {{ __('forms.secondName') }}
                        </x-forms.label>
                    </x-slot>

                    <x-slot name="input">
                        <x-forms.input id="filter_second_name"
                                       wire:model="patientsFilter.secondName" type="text"
                                       autocomplete="off"
                        />
                    </x-slot>
                </x-forms.form-group>

                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="filter_tax_id">
                            {{ __('forms.taxId') }}
                        </x-forms.label>
                    </x-slot>

                    <x-slot name="input">
                        <x-forms.input id="filter_tax_id"
                                       wire:model="patientsFilter.taxId" type="text"
                                       autocomplete="off"
                        />
                    </x-slot>
                </x-forms.form-group>

                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="filter_phone_number">
                            {{ __('forms.phone') }}
                        </x-forms.label>
                    </x-slot>

                    <x-slot name="input">
                        <x-forms.input id="filter_phone_number"
                                       wire:model="patientsFilter.phoneNumber" type="text"
                                       autocomplete="off"
                        />
                    </x-slot>
                </x-forms.form-group>

                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="filter_birth_certificate">
                            {{ __('forms.birth_certificate') }}
                        </x-forms.label>
                    </x-slot>

                    <x-slot name="input">
                        <x-forms.input id="filter_birth_certificate"
                                       wire:model="patientsFilter.birthCertificate" type="text"
                                       autocomplete="off"
                        />
                    </x-slot>
                </x-forms.form-group>
            </div>
        </div>
    </div>
</div>
