<x-slot name="title">
    {{  __('7. Додаткова інформація') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_receiver_funds_code"
                           name="label">
                {{__('Код одержувача/розпорядника бюджетних коштів для Казначейства')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.receiver_funds_coder"
                           type="text" id="form_receiver_funds_coder"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_beneficiary"
                           name="label">
                {{__('Інформація про власника (бенефіціара) закладу')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.beneficiary"
                           type="text" id="form_beneficiary"/>
        </x-slot>
    </x-forms.form-group>

</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_archive_date"
                           name="label">
                {{__('Дата передачі паперових документів до архіву')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.archive.date"
                           type="date" id="form_archive_date"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_archive_place"
                           name="label">
                {{__('Адреса будівлі, де знаходяться паперові документи')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.archive.place"
                           type="text" id="orm_archive_place"/>
        </x-slot>
    </x-forms.form-group>

</div>
