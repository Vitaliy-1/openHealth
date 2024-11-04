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
                    class="default-input"
                    wire:model="patient_request.confidant_person.relation_type"
                    type="text"
                    id="confidant_person.relation_type">
                    <x-slot name="option">
                        <option>{{ __('forms.select') }} {{ __('forms.relation_type') }}</option>
                        @foreach(App\Enums\Person\RelationType::cases() as $case)
                            <option value="{{ $case->value }}">{{ $case->label() }}</option>
                        @endforeach
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('patient_request.confidant_person.relation_type')
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
                <x-forms.label for="confidant_person.first_name" class="default-label">
                    {{ __('forms.first_name') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.confidant_person.first_name"
                               type="text"
                               id="confidant_person.first_name"
                />
            </x-slot>
            @error('patient_request.confidant_person.first_name')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="confidant_person.last_name" class="default-label">
                    {{ __('forms.last_name') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.confidant_person.last_name"
                               type="text"
                               id="confidant_person.last_name"
                />
            </x-slot>
            @error('patient_request.confidant_person.last_name')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="confidant_person.second_name" class="default-label">
                    {{__('forms.second_name')}}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.confidant_person.second_name"
                               type="text"
                               id="confidant_person.second_name"
                />
            </x-slot>
            @error('patient_request.confidant_person.second_name')
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
                <x-forms.input class="default-input"
                               wire:model="patient_request.confidant_person.birth_date"
                               type="date"
                               id="confidant_person.birth_date"
                />
            </x-slot>
            @error('patient_request.confidant_person.birth_date')
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
                    {{ __('forms.birth_country') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.confidant_person.birth_country"
                               type="text"
                               id="confidant_person.birth_country"
                />
            </x-slot>
            @error('patient_request.confidant_person.birth_country')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="birth_settlement" class="default-label">
                    {{ __('forms.birth_settlement') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="patient_request.confidant_person.birth_settlement"
                               type="text"
                               id="confidant_person.birth_settlement"
                />
            </x-slot>
            @error('patient_request.confidant_person.birth_settlement')
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
                    class="default-input" wire:model="patient_request.confidant_person.gender" type="text"
                    id="confidant_person.gender">
                    <x-slot name="option">
                        <option> {{ __('forms.select') }} {{ __('forms.gender') }}</option>
                        @foreach($this->dictionaries['GENDER'] as $k => $gender)
                            <option value="{{ $k }}">{{ $gender }}</option>
                        @endforeach
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('patient_request.confidant_person.gender')
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
                <x-forms.input class="default-input"
                               wire:model="patient_request.confidant_person.secret"
                               type="text"
                               id="confidant_person.secret"
                />
            </x-slot>
            @error('patient_request.confidant_person.secret')
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
                               wire:model="patient_request.confidant_person.tax_id" type="text"
                               id="confidant_person.tax_id"
                />
            </x-slot>
            @error('patient_request.confidant_person.tax_id')
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
                               wire:model="patient_request.confidant_person.unzr"
                               type="text"
                               id="confidant_person.unzr"
                />
            </x-slot>
            @error('patient_request.confidant_person.unzr')
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
            <x-button wire:click="store('confidant_person')" type="submit" class="btn-primary d-flex max-w-[150px]">
                {{ __('forms.save') }}
            </x-button>
        </div>
    </div>
</div>
