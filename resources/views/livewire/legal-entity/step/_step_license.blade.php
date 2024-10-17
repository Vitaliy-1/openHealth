<x-forms.form-row class="flex-wrap">
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="license_type"
                           name="label">
                {{__('forms.license_type')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select readonly class="default-input"
                            wire:model.defer="legal_entity_form.license.type"
                            disabled
                            type="text" id="license_type">
                <x-slot name="option">
                    @foreach($dictionaries['LICENSE_TYPE'] as $k => $license_type)
                        <option value="{{ $k }}" {{ $k === 'MSP' ? 'selected' : '' }}>
                            {{ $license_type }}
                        </option>
                    @endforeach
                </x-slot>
            </x-forms.select>
        </x-slot>
        @error('legal_entity_form.license.type')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="license_license_number"
                           name="label">
                {{__('forms.license_number')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.license.license_number"
                           type="text" id="license_license_number"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="license_issued_by"
                           name="label">
                {{__('forms.license_issued_by')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.license.issued_by"
                           type="text" id="license_issued_by"/>
        </x-slot>
        @error('legal_entity_form.license.issued_by')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="license_issued_date"
                           name="label">
                {{__('forms.license_issued_date')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date  wire:model="legal_entity_form.license.issued_date"
                            id="license_issued_date"/>
        </x-slot>
        @error('legal_entity_form.license.issued_date')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="license_active_from_date"
                           name="label">
                {{__('forms.license_active_from_date')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date  wire:model="legal_entity_form.license.active_from_date"
                           id="license_active_from_date"/>
        </x-slot>
        @error('legal_entity_form.license.active_from_date')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="license_expiry_date"
                           name="label">
                {{__('forms.license_expiry_date')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date wire:model="legal_entity_form.license.expiry_date"
                           id="license_expiry_date"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="license_what_licensed"
                           name="label">
                {{__('forms.license_what_licensed')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.license.what_licensed"
                           type="text" id="license_what_licensed"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="license_order_no"
                           name="label">
                {{__('forms.license_order_no')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.license.order_no"
                           type="text" id="license_order_no"/>
        </x-slot>
        @error('legal_entity_form.license.order_no')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

</x-forms.form-row>
