@php
    $svgSprite = file_get_contents(resource_path('images/sprite.svg'));
@endphp

<div aria-hidden="true" class="hidden">
    {!! $svgSprite !!}
</div>

<section class="section-form">
    <x-section-navigation class="breadcrumb-form">
        <x-slot name="title">
            {{ __('patients.interaction') }} - {{ $firstName }} {{ $lastName }} {{ $secondName ?? '' }}
        </x-slot>
    </x-section-navigation>

    <form class="form">
        @include('livewire.encounter._parts._aside_navigation')
        @include('livewire.encounter._parts._main_data')
        @include('livewire.encounter._parts._reasons')
        @include('livewire.encounter._parts._diagnoses')
        @include('livewire.encounter._parts._actions')
        @include('livewire.encounter._parts._additional_data')
        @include('livewire.encounter._parts._immunization')

        <div class="flex gap-8">
            <button wire:click.prevent="" type="submit" class="button-minor">
                {{ __('forms.delete') }}
            </button>

            <button wire:click.prevent="save" type="submit" class="button-primary">
                {{ __('forms.save') }}
            </button>

            <button wire:click.prevent="create('signedContent')"
                    type="button"
                    class="button-sync flex items-center gap-2"
            >
                <svg width="16" height="17">
                    <use xlink:href="#svg-key"></use>
                </svg>
                {{ __('forms.complete_the_interaction_and_sign') }}
                <svg width="16" height="17">
                    <use xlink:href="#svg-arrow-right"></use>
                </svg>
            </button>
        </div>

        @if($showModal === 'signedContent')
            @include('livewire.patient._parts.modals._modal_signed_content')
        @endif
    </form>

    <x-forms.loading/>
</section>
