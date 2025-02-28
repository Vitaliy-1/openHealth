@php
    $svgSprite = file_get_contents(resource_path('images/sprite.svg'));
@endphp

<div>
    <div aria-hidden="true" class="hidden">
        {!! $svgSprite !!}
    </div>

    <x-section-navigation class="breadcrumb-form">
        <x-slot name="title">{{ __('patients.add_patient') }}</x-slot>
    </x-section-navigation>

    @if($viewState === 'default')
        <section class="section-form">
            <form action="#" class="form">
                @include('livewire.patient._parts._patient')
                @include('livewire.patient._parts._documents')
                @include('livewire.patient._parts._identity')
                @include('livewire.patient._parts._contact_data')
                @include('livewire.patient._parts._emergency_contact')
                @include('livewire.patient._parts._incapacitated')
                @include('livewire.patient._parts._authentication_methods')

                <div class="flex xl:flex-row gap-6 justify-between items-center">
                    <a href="{{ route('patient.index') }}" class="button-minor">
                        {{ __('forms.back') }}
                    </a>
                    @if(auth()->user()->hasRole('DOCTOR'))
                        <button wire:click.prevent="createPerson('patient')" class="button-primary">
                            {{ __('forms.send_for_approval') }}
                        </button>
                    @endif
                    @if(auth()->user()->hasRole('DOCTOR|RECEPTIONIST'))
                        <button wire:click.prevent="createApplication('patient')" class="button-primary">
                            {{ __('patients.save_to_application') }}
                        </button>
                    @endif
                </div>
            </form>
        </section>

    @elseif($viewState === 'new')
        <section class="section-form">
            <form action="#" class="form">
                @include('livewire.patient._parts._signature')
            </form>
        </section>
    @endif

    @if($showModal === 'documents')
        @include('livewire.patient._parts.modals._modal_document')
    @elseif($showModal === 'documentsRelationship')
        @include('livewire.patient._parts.modals._modal_document_relationship')
    @endif

    <x-forms.loading/>
</div>
