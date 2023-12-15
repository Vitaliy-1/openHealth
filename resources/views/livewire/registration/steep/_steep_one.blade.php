<x-slot name="title">
    {{  __('Єдрпоу') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group>
        <x-slot name="label">
            <x-forms.label class="default-label" for="edrpou" name="label" id="edrpou">
                {{__('Єдрпоу')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.edrpou" type="text" id="edrpou"
                           placeholder="{{__('Єдрпоу')}}"/>
        </x-slot>
        @error('form.edrpou')
        <x-slot name="error">
            <x-forms.error name="message">
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</div>
