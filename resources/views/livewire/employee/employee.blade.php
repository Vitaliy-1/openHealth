<div>

    <x-section-navigation class="breadcrumb-form">
        <x-slot name="title">{{ $pageTitle  }}</x-slot>
    </x-section-navigation>

    <section class="section-form">
        <form wire:submit="save"
              class="form"
              {{-- employeeType controls which documents are required from the user to fill --}}
              x-data="{employeeType: $wire.entangle('form.party.employeeType')}"
        >
            @include('livewire.employee._parts._employee')
            @include('livewire.employee._parts._documents')
            @include('livewire.employee._parts._education')
            @include('livewire.employee._parts._specialities')
            @include('livewire.employee._parts._science_degree')
            @include('livewire.employee._parts._qualifications')

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

    {{--
    <div class="flex bg-white  p-6 flex-col">
        @if(isset($employeeRequest->party['employeeType']) && in_array($employeeRequest->party['employeeType'],config('ehealth.doctors_type')))
            @include('livewire.employee._parts._education')
            @include('livewire.employee._parts._specialities')
            @include('livewire.employee._parts._science_degree')
            @include('livewire.employee._parts._qualifications')
        @endif

    </div>
    --}}
    <x-forms.loading/>

</div>



