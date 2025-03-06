@php
    $svgSprite = file_get_contents(resource_path('images/sprite.svg'));
@endphp

<div>
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
            @include('livewire.encounter._parts._patient_data')
            @include('livewire.encounter._parts._aside_navigation')
            @include('livewire.encounter._parts._diagnoses')

            <div class="text-center">
                <button wire:click.prevent="save(['encounter', 'episode'])" type="submit" class="alternative-button">
                    {{ __('forms.save') }}
                </button>
            </div>
            <button wire:click="create('signedContent')"
                    type="button"
                    class="default-button flex items-center gap-2"
            >
                <svg width="16" height="17">
                    <use xlink:href="#svg-key"></use>
                </svg>
                {{ __('Підписати КЕПом') }}
                <svg width="16" height="17">
                    <use xlink:href="#svg-arrow-right"></use>
                </svg>
            </button>
            @if($showModal === 'signedContent')
                @include('livewire.patient._parts.modals._modal_signed_content')
            @endif
        </form>
        <x-forms.loading/>
    </section>
</div>
