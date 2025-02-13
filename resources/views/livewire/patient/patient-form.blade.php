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

                <fieldset class="fieldset">
                    <legend class="legend flex items-baseline gap-2">
                        <x-checkbox class="default-checkbox mb-2"
                                    wire:model.live="isIncapable"
                                    id="isIncapable"
                        />

                        {{ __('forms.incapable') }}
                    </legend>

                    @if($isIncapable)
                        @include('livewire.patient._parts._search_confidant_person')
                        @include('livewire.patient._parts._confidant_person')
                    @endif
                </fieldset>

                @include('livewire.patient._parts._authentication_methods')

                <x-forms.form-row class="flex-col justify-between items-center">
                    <div class="xl:w-1/4 text-left">
                        <x-secondary-button wire:click="closeModal">
                            {{ __('Назад') }}
                        </x-secondary-button>
                    </div>

                    <div class="xl:w-1/4 text-right">
                        <button wire:click.prevent="createPerson('patient')" type="button"
                                class="btn-primary">
                            {{ __('Відправити на затвердження') }}
                        </button>
                    </div>

                    <div class="xl:w-1/4">
                        <button wire:click.prevent="createApplication('patient')" type="button" class="btn-primary">
                            {{ __('Зберегти в заявки') }}
                        </button>
                    </div>
                </x-forms.form-row>
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
</div>
