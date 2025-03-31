@php
    $user = Auth::user();
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
            <form class="form">
                @include('livewire.patient._parts._patient')
                @include('livewire.patient._parts._documents')
                @include('livewire.patient._parts._identity')
                @include('livewire.patient._parts._contact_data')
                @include('livewire.patient._parts._addresses')
                @include('livewire.patient._parts._emergency_contact')
                @include('livewire.patient._parts._incapacitated')
                @include('livewire.patient._parts._authentication_methods')

                <div class="flex xl:flex-row gap-6 justify-between items-center">
                    <a href="{{ route('patient.index') }}" class="button-minor">
                        {{ __('forms.back') }}
                    </a>
                    @if($user->hasRole('DOCTOR'))
                        <button wire:click.prevent="createPerson" class="button-primary">
                            {{ __('forms.send_for_approval') }}
                        </button>
                    @endif
                    @if($user->hasAnyRole(['DOCTOR', 'RECEPTIONIST']))
                        <button wire:click.prevent="createApplication" class="button-primary">
                            {{ __('patients.save_to_application') }}
                        </button>
                    @endif
                </div>
            </form>
        </section>

    @elseif($viewState === 'new')
        <section class="section-form">
            <form class="form">
                @include('livewire.patient._parts._signature')
            </form>
        </section>
    @endif

    <x-forms.loading/>
</div>
