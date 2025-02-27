@php
    use App\Enums\Person\AuthenticationMethod;
    $svgSprite = file_get_contents(resource_path('images/sprite.svg'));
@endphp

<x-layouts.patient :id="$id" :firstName="$firstName" :lastName="$lastName" :secondName="$secondName">
    <div aria-hidden="true" class="hidden">
        {!! $svgSprite !!}
    </div>

    <div class="breadcrumb-form p-4">
        <div class="flex items-center gap-14 mb-10">
            <p class="default-p">
                {{ __('patients.verification_in_eHealth') }}: {{ __('patients.' . $verificationStatus) }}
            </p>

            <div>
                <button wire:click.once="getVerificationStatus"
                        type="button"
                        class="flex items-center gap-2 default-button"
                >
                    {{ __('patients.update_status') }}
                    <svg width="16" height="17">
                        <use xlink:href="#svg-refresh"></use>
                    </svg>
                </button>
            </div>
        </div>

        <div id="accordion-open" data-accordion="open">
            <h2 id="accordion-open-heading-1">
                <button type="button"
                        class="accordion-button rounded-t-xl border-b-0"
                        data-accordion-target="#accordion-open-body-1"
                        aria-expanded="true"
                        aria-controls="accordion-open-body-1"
                >
                    <span class="text-lg">{{ __('patients.passport_data') }}</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                        <use xlink:href="#svg-chevron"></use>
                    </svg>
                </button>
            </h2>
            <div id="accordion-open-body-1" class="hidden" aria-labelledby="accordion-open-heading-1" wire:ignore.self>
                <div class="accordion-content dark:bg-gray-900 border-b-0">
                    <div class="form-row-4 items-baseline">
                        <div class="form-group group">
                            <p class="default-p">{{ __('forms.lastName') }}</p>
                        </div>
                        <div>
                            <input wire:model="lastName"
                                   type="text"
                                   name="lastName"
                                   id="lastName"
                                   class="input"
                                   placeholder=" "
                                   required
                                   autocomplete="off"
                            />
                        </div>
                    </div>

                    <div class="form-row-4 items-baseline">
                        <div class="form-group group">
                            <p class="default-p">{{ __('forms.firstName') }}</p>
                        </div>
                        <div>
                            <input wire:model="firstName"
                                   type="text"
                                   name="firstName"
                                   id="firstName"
                                   class="input"
                                   placeholder=" "
                                   required
                                   autocomplete="off"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <h2 id="accordion-open-heading-2">
                <button type="button"
                        class="accordion-button border-b-0"
                        data-accordion-target="#accordion-open-body-2"
                        aria-expanded="false"
                        aria-controls="accordion-open-body-2"
                >
                    <span class="text-lg">{{ __('patients.contact_data') }}</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                        <use xlink:href="#svg-chevron"></use>
                    </svg>
                </button>
            </h2>
            <div id="accordion-open-body-2" class="hidden" aria-labelledby="accordion-open-heading-2" wire:ignore.self>
                <div class="accordion-content dark:bg-gray-900 border-b-0">
                    @foreach($phones as $key => $phone)
                        <div class="form-row-4 items-baseline">
                            <div class="form-group group">
                                <p class="default-p">{{ __('forms.phone') }}</p>
                            </div>
                            <div>
                                <input wire:model="phones.{{ $key }}.number"
                                       type="text"
                                       name="phoneNumber_{{ $key }}"
                                       id="phoneNumber_{{ $key }}"
                                       class="input"
                                       placeholder=" "
                                       required
                                       autocomplete="off"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <h2 id="accordion-open-heading-3">
                <button wire:click.once="getConfidantPersons"
                        type="button"
                        class="accordion-button border-b-0"
                        data-accordion-target="#accordion-open-body-3"
                        aria-expanded="false"
                        aria-controls="accordion-open-body-3"
                >
                    <span class="text-lg">{{ __('patients.patient_legal_representative') }}</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                        <use xlink:href="#svg-chevron"></use>
                    </svg>
                </button>
            </h2>
            <div id="accordion-open-body-3" class="hidden" aria-labelledby="accordion-open-heading-3" wire:ignore.self>
                <div class="accordion-content dark:bg-gray-900 border-t-0">
                    @if(!empty($confidantPersonRelationships))
                        @foreach($confidantPersonRelationships as $key => $confidantPersonRelationship)
                            <div class="form-row-4 items-baseline">
                                <div class="form-group group">
                                    <p class="default-p">{{ __('forms.full_name') }}</p>
                                </div>
                                <div>
                                    <input
                                        wire:model="confidantPersonRelationships.{{ $key }}.confidant_person.name"
                                        type="text"
                                        name="name_{{ $key }}"
                                        id="name_{{ $key }}"
                                        class="input"
                                        placeholder=" "
                                        required
                                        autocomplete="off"
                                    />
                                </div>
                            </div>
                            <div class="form-row-4 items-baseline">
                                <div class="form-group group">
                                    <p class="default-p">{{ __('forms.active_to') }}</p>
                                </div>
                                <div>
                                    <input wire:model="confidantPersonRelationships.{{ $key }}.active_to"
                                           type="text"
                                           name="activeTo_{{ $key }}"
                                           id="activeTo_{{ $key }}"
                                           class="input"
                                           placeholder=" "
                                           required
                                           autocomplete="off"
                                    />
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="default-p">{{ __('patients.confidant_person_not_exist') }}</p>
                    @endif
                </div>
            </div>

            <h2 id="accordion-open-heading-4">
                <button wire:click.once="getAuthenticationMethods"
                        type="button"
                        class="accordion-button"
                        data-accordion-target="#accordion-open-body-4"
                        aria-expanded="false"
                        aria-controls="accordion-open-body-4"
                >
                    <span class="text-lg">{{ __('patients.authentication_methods') }}</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                        <use xlink:href="#svg-chevron"></use>
                    </svg>
                </button>
            </h2>
            <div id="accordion-open-body-4" class="hidden" aria-labelledby="accordion-open-heading-4" wire:ignore.self>
                <div class="accordion-content dark:bg-gray-900 border-t-0">
                    @foreach($authenticationMethods as $key => $authenticationMethod)
                        <div class="form-row-4 items-baseline">
                            <div class="form-group group">
                                <p>{{ __('patients.auth_method') }}</p>
                            </div>
                            <div>
                                <input wire:model="authenticationMethods.{{ $key }}.type"
                                       type="hidden"
                                       name="authenticationMethod"
                                       id="authenticationMethod_{{ $key }}"
                                />
                                <input value="{{ AuthenticationMethod::from($authenticationMethod['type'])->label() }}"
                                       type="text"
                                       class="input"
                                       placeholder=" "
                                       autocomplete="off"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <x-forms.loading/>
</x-layouts.patient>
