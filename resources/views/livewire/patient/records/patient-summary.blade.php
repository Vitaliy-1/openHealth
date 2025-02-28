@php
    $svgSprite = file_get_contents(resource_path('images/sprite.svg'));
@endphp

<x-layouts.patient :id="$id" :firstName="$firstName" :lastName="$lastName" :secondName="$secondName">
    <div aria-hidden="true" class="hidden">
        {!! $svgSprite !!}
    </div>

    <div class="breadcrumb-form p-4">
        <button wire:click.prevent=""
                class="default-button mb-10"
        >
            {{ __('patients.get_access_to_medical_data') }}
        </button>

        <div id="accordion-open" data-accordion="open">
            <h2 id="accordion-open-heading-1">
                <button wire:click.once="getEpisodes"
                        type="button"
                        class="accordion-button rounded-t-xl border-b-0"
                        data-accordion-target="#accordion-open-body-1"
                        aria-expanded="true"
                        aria-controls="accordion-open-body-1"
                >
                    <span>{{ __('patients.episodes') }}</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                        <use xlink:href="#svg-chevron"></use>
                    </svg>
                </button>
            </h2>
            <div id="accordion-open-body-1" class="hidden" aria-labelledby="accordion-open-heading-1" wire:ignore.self>
                <div class="accordion-content border-b-0">
                    <div class="form-row-4 items-baseline">

                    </div>
                </div>
            </div>

            <h2 id="accordion-open-heading-2">
                <button wire:click.once="getDiagnoses"
                        type="button"
                        class="accordion-button border-b-0"
                        data-accordion-target="#accordion-open-body-2"
                        aria-expanded="false"
                        aria-controls="accordion-open-body-2"
                >
                    <span>{{ __('patients.diagnoses') }}</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                        <use xlink:href="#svg-chevron"></use>
                    </svg>
                </button>
            </h2>
            <div id="accordion-open-body-2" class="hidden" aria-labelledby="accordion-open-heading-2" wire:ignore.self>
                <div class="accordion-content border-b-0">
                    <div class="form-row-4 items-baseline">
                    </div>
                </div>
            </div>

            <h2 id="accordion-open-heading-3">
                <button wire:click.once="getObservations"
                        type="button"
                        class="accordion-button"
                        data-accordion-target="#accordion-open-body-3"
                        aria-expanded="false"
                        aria-controls="accordion-open-body-3"
                >
                    <span>{{ __('patients.observations') }}</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                        <use xlink:href="#svg-chevron"></use>
                    </svg>
                </button>
            </h2>
            <div id="accordion-open-body-3" class="hidden" aria-labelledby="accordion-open-heading-3" wire:ignore.self>
                <div class="accordion-content border-t-0">
                    <div class="form-row-4 items-baseline">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-forms.loading/>
</x-layouts.patient>
