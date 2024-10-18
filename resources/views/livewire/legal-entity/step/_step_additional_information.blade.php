<x-forms.form-row  class="flex-wrap">
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="additional_information_receiver_funds_code"
                           name="label">
                {{__('forms.receiver_funds_code')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.receiver_funds_code"
                           type="text" id="additional_information_receiver_funds_coder"/>
        </x-slot>
        @error('legal_entity_form.receiver_funds_code')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="legal_entity_form..additional_information_beneficiary"
                           name="label">

                {{__('forms.beneficiary')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.beneficiary"
                           type="text" id="additional_information_beneficiary"/>
        </x-slot>
        @error('legal_entity_form.beneficiary')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="additional_information_archive_date"
                           name="label">
                {{__("forms.archive_date")}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date wire:model="legal_entity_form.archive.date"
                          id="additional_information_archive_date"/>
        </x-slot>
        @error('legal_entity_form.archive.date')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label class="default-label" for="additional_information_archive_place"
                           name="label">
                {{__('forms.archive_place')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.archive.place"
                           type="text" id="additional_information_archive_place"/>
        </x-slot>
        @error('legal_entity_form.archive.place')

        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror

    </x-forms.form-group>
</x-forms.form-row>
