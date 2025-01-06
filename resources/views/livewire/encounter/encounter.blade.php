<div>
    <x-section-navigation>
        <x-slot name="title">{{ __('patients.encounter_create') }}</x-slot>
    </x-section-navigation>

    <div class="inline-block min-w-full align-middle">
        <x-forms.forms-section submit="store">
            <x-slot name='form'>
            </x-slot>
        </x-forms.forms-section>
    </div>

    <x-forms.loading/>
</div>