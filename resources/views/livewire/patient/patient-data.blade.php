<div>
    <div class="flex items-center gap-14 mb-10">
        <p>{{ __('Верифікація в ЕСОЗ:') }} {{ __('patients.' . $patient['status']) }}</p>

        <div>
            <button wire:click.once="getVerificationStatus"
                    type="button"
                    class="flex items-center gap-2 default-button"
            >
                {{ __('Оновити статус') }}
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
                <span>{{ __('Паспортні дані') }}</span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                    <use xlink:href="#svg-chevron"></use>
                </svg>
            </button>
        </h2>
        <div id="accordion-open-body-1" class="hidden" aria-labelledby="accordion-open-heading-1" wire:ignore.self>
            <div class="accordion-content dark:bg-gray-900 border-b-0">
                <div class="form-row-4 items-baseline">
                    <div class="form-group group">
                        <p>Прізвище</p>
                    </div>
                    <div>
                        <input wire:model="patient.last_name"
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
                        <p>Ім’я</p>
                    </div>
                    <div>
                        <input wire:model="patient.first_name"
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
                <span>{{ __('Контактні дані') }}</span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                    <use xlink:href="#svg-chevron"></use>
                </svg>
            </button>
        </h2>
        <div id="accordion-open-body-2" class="hidden" aria-labelledby="accordion-open-heading-2" wire:ignore.self>
            <div class="accordion-content border-b-0">
                <div class="form-row-4 items-baseline">
                    <div class="form-group group">
                        <p>Телефон</p>
                    </div>
                    <div>
                        <input wire:model="patient.phones.number"
                               type="text"
                               name="phoneNumber"
                               id="patientLastName"
                               class="input"
                               placeholder=" "
                               required
                               autocomplete="off"
                        />
                    </div>
                </div>
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
                <span>{{ __('Законний представник пацієнта') }}</span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                    <use xlink:href="#svg-chevron"></use>
                </svg>
            </button>
        </h2>
        <div id="accordion-open-body-3" class="hidden" aria-labelledby="accordion-open-heading-3" wire:ignore.self>
            <div class="accordion-content border-t-0">
                @if(isset($patient['confidantPersonRelationships']))
                    @foreach($patient['confidantPersonRelationships'] as $key => $confidantPersonRelationship)
                        <div class="form-row-4 items-baseline">
                            <div class="form-group group">
                                <p>{{ __('ПІБ') }}</p>
                            </div>
                            <div>
                                <input
                                    wire:model="patient.confidantPersonRelationships.{{ $key }}.confidant_person.name"
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
                                <p>{{ __('Активний до') }}</p>
                            </div>
                            <div>
                                <input wire:model="patient.confidantPersonRelationships.{{ $key }}.active_to"
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
                <span>{{ __('Методи автентифікації') }}</span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0">
                    <use xlink:href="#svg-chevron"></use>
                </svg>
            </button>
        </h2>
        <div id="accordion-open-body-4" class="hidden" aria-labelledby="accordion-open-heading-4" wire:ignore.self>
            <div class="accordion-content border-t-0">
                <div class="form-row-4 items-baseline">
                    <div class="form-group group">
                        <p>{{ __('Метод автентифікації') }}</p>
                    </div>
                    <div>
                        <input wire:model="patient.authenticationMethod"
                               type="text"
                               name="authenticationMethod"
                               id="authenticationMethod"
                               class="input"
                               placeholder=" "
                               required
                               autocomplete="off"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
