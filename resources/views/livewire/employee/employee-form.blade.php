<div x-data="{ employeeId: @entangle('employeeId') }">

    <x-section-navigation class="breadcrumb-form">
        <x-slot name="title">{{ __('Додати співробітника') }}</x-slot>
    </x-section-navigation>

    <section class="section-form">
        <form action="#"
              class="form"
        >
            @include('livewire.employee._parts._employee2')
        </form>
    </section>

    <div class="flex bg-white  p-6 flex-col ">
        @include('livewire.employee._parts._employee')
        @include('livewire.employee._parts._documents')
        @if(isset($employeeRequest->party['employeeType']) && in_array($employeeRequest->party['employeeType'],config('ehealth.doctors_type')))
            @include('livewire.employee._parts._education')
            @include('livewire.employee._parts._specialities')
            @include('livewire.employee._parts._science_degree')
            @include('livewire.employee._parts._qualifications')
        @endif
        <div class="mb-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center ">
            <div class="xl:w-1/4 text-left">
                <x-secondary-button wire:click="closeModal()">
                    {{__('Назад')}}
                </x-secondary-button>
            </div>
            <div class="xl:w-1/4 text-right">
                <button wire:click="signedComplete('signedContent')" type="button" class="default-button">
                    {{__('Відправити на затвердження')}}
                </button>
            </div>
        </div>
    </div>
    <x-dialog-modal maxWidth="3xl" class="w-3 h-full" wire:ignore wire:model="showModal">
        <x-slot name="title">
            {{__('forms.'.$showModal)}}
        </x-slot>
        <x-slot name="content">
            @php
                $submitAction = $mode === 'edit'
                    ? "update(''$showModal', '$keyProperty', '$singleProperty'')"
                    : "store('$showModal', '$singleProperty')";
            @endphp
            <x-forms.forms-section-modal submit="{!! $submitAction !!}">
                <x-slot name="form">
                    @if(view()->exists('livewire.employee._parts.modals._modal_'.$showModal))
                        @include('livewire.employee._parts.modals._modal_'.$showModal)
                    @else
                        <p>{{ __('Invalid modal type') }}</p>
                    @endif
                </x-slot>
            </x-forms.forms-section-modal>
        </x-slot>
    </x-dialog-modal>
    <x-forms.loading/>

</div>



