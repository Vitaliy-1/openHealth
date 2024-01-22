<x-slot name="title">
    {{  __('7. Додаткова інформація') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="additional_information_receiver_funds_code"
                           name="label">
                {{__('forms.receiver_funds_code')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.additional_information.receiver_funds_code"
                           type="text" id="additional_information_receiver_funds_coder"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="legal_entity_form..additional_information_beneficiary"
                           name="label">

                {{__('forms.beneficiary')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.additional_information.beneficiary"
                           type="text" id="additional_information_beneficiary"/>
        </x-slot>
    </x-forms.form-group>

</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="additional_information_archive_date"
                           name="label">
                {{__("forms.archive_date")}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.additional_information.archive.date"
                           type="date" id="additional_information_archive_date"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="additional_information_archive_place"
                           name="label">
                {{__('forms.archive_place')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.additional_information.archive.place"
                           type="text" id="additional_information_archive_place"/>
        </x-slot>
    </x-forms.form-group>

</div>
