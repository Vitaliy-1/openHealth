<div>
    <x-section-navigation class="breadcrumb-form">
        <x-slot name="title">{{ $pageTitle  }}</x-slot>
    </x-section-navigation>

    <section class="section-form">
        <form
            wire:submit="save"
            class="form"
            x-data="{
                employeeType: $wire.entangle('form.party.employeeType'),
                isDoctor() {
                    return {{ Js::from(config('ehealth.doctors_type')) }}.includes(this.employeeType);
                }
            }"
        >
            @include('livewire.employee._parts._employee')
            @include('livewire.employee._parts._documents')

            <template x-if="isDoctor()">
                <div>
                    @include('livewire.employee._parts._education')
                    @include('livewire.employee._parts._specialities')
                    @include('livewire.employee._parts._science_degree')
                    @include('livewire.employee._parts._qualifications')
                </div>
            </template>

            <div class="form-button-group">
                <button type="button" class="button-minor">
                    {{__('forms.cancel')}}
                </button>
                <button type="submit" class="button-primary">
                    {{__('forms.save')}}
                </button>
                <button wire:click="signedComplete('signedContent')" type="button" class="button-primary">
                    {{ __('forms.send_for_approval') }}
                </button>
            </div>
        </form>
    </section>

    <x-forms.loading />
</div>
