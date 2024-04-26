<x-slot name="title">
    {{  __('4. Адресса ') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div>
<livewire:components.koatuu-search  :addresses="$legal_entity_form->addresses ?? []" :class="'grid grid-cols-2 gap-6'" />

</div>



