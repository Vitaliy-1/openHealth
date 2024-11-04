<div>
    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="relation_type" class="default-label">
                    {{ __('forms.relation_type') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.select
                    class="default-input" wire:model="patient_request.legal_representative.relation_type"
                    type="text"
                    id="legal_representative.relation_type">
                    <x-slot name="option">
                        <option>{{ __('forms.select') }} {{ __('forms.relation_type') }}</option>
                        @foreach(App\Enums\Person\RelationType::cases() as $case)
                            <option value="{{ $case->value }}">{{ $case->label() }}</option>
                        @endforeach
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('patient_request.legal_representative.relation_type')
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
                <x-forms.label for="legal_representative.first_name" class="default-label">
                    {{ __('forms.first_name') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.legal_representative.first_name"
                               type="text"
                               id="legal_representative.first_name"/>
            </x-slot>
            @error('patient_request.legal_representative.first_name')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="legal_representative.last_name" class="default-label">
                    {{ __('forms.last_name') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.legal_representative.last_name"
                               type="text"
                               id="legal_representative.last_name"/>
            </x-slot>
            @error('patient_request.legal_representative.last_name')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="legal_representative.second_name" class="default-label">
                    {{__('forms.second_name')}}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.legal_representative.second_name"
                               type="text"
                               id="legal_representative.second_name"/>
            </x-slot>
            @error('patient_request.legal_representative.second_name')
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
                <x-forms.input class="default-input" type="date" id="birth_date"
                               wire:model="patient_request.legal_representative.birth_date"/>
            </x-slot>
            @error('patient_request.legal_representative.birth_date')
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
                    {{ __('forms.country_of_birth') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.legal_representative.country_of_birth"
                               type="text"
                               id="country_of_birth"/>
            </x-slot>
            @error('patient_request.legal_representative.country_of_birth')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="city_of_birth" class="default-label">
                    {{ __('forms.city_of_birth') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.legal_representative.city_of_birth"
                               type="text"
                               id="city_of_birth"/>
            </x-slot>
            @error('patient_request.legal_representative.city_of_birth')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="gender" class="default-label">
                    {{ __('forms.gender') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.select
                    class="default-input" wire:model="patient_request.legal_representative.gender" type="text"
                    id="gender">
                    <x-slot name="option">
                        <option> {{ __('forms.select') }} {{ __('forms.gender') }}</option>
                        @foreach($this->dictionaries['GENDER'] as $k => $gender)
                            <option value="{{ $k }}">{{ $gender }}</option>
                        @endforeach
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('patient_request.legal_representative.gender')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="secret" class="default-label">
                    {{ __('forms.secret') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.legal_representative.secret"
                               type="text"
                               id="secret"/>
            </x-slot>
            @error('patient_request.legal_representative.secret')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label class="default-label" for="tax_id">
                    {{ __('forms.number') }} {{ __('forms.RNOCPP') }}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input maxlength="10" class="default-input" checked
                               wire:model="patient_request.legal_representative.tax_id" type="text" id="tax_id"/>
            </x-slot>
            @error('patient_request.legal_representative.tax_id')
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
                <x-forms.input class="default-input" wire:model="patient_request.legal_representative.unzr" type="text"
                               id="unzr"/>
            </x-slot>
            @error('patient_request.legal_representative.unzr')
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
            <x-button wire:click="store('legal_representative')" type="submit" class="btn-primary d-flex max-w-[150px]">
                {{ __('forms.save') }}
            </x-button>
        </div>
    </div>
</div>
