<div>

    <x-section-navigation class="">
        <x-slot name="title">{{ __('Співробітники') }}</x-slot>
        <x-slot name="navigation">

        </x-slot>
    </x-section-navigation>

    <div class="flex bg-white  p-6 flex-col ">


        @include('livewire.employee._parts._employee')
        @include('livewire.employee._parts._documents')
        @if( isset($employeeRequest->employeeType) && $employeeRequest->employeeType === 'DOCTOR')
            @include('livewire.employee._parts._education')
            @include('livewire.employee._parts._specialities')
            @include('livewire.employee._parts._science_degree')
            @include('livewire.employee._parts._specialities')
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
    @if($showModal === 'educations' )
        @include('livewire.employee._parts.modals._modal_education')
    @elseif($showModal === 'documents')
        @include('livewire.employee._parts.modals._modal_documents')
    @elseif($showModal === 'specialities' )
        @include('livewire.employee._parts.modals._modal_specialities')
    @elseif($showModal === 'signedContent' )
        @include('livewire.employee._parts.modals._modal_signed_content')
    @elseif($showModal === 'scienceDegree' )
        @include('livewire.employee._parts.modals._modal_science_degree')
    @elseif($showModal === 'qualifications')
        @include('livewire.employee._parts.modals._modal_qualifications')
    @endif

    <x-forms.loading/>

</div>



