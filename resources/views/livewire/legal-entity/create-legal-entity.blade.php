<div>
    <x-section-navigation x-data="{ showFilter: false }" class="">
        <x-slot name="title">{{ __('Зарееструвати заклад ') }}</x-slot>
    </x-section-navigation>

    <section class="section-form">
        <div
            x-data="{
                activeStep: {{ $activeStep }},
                isEdit: @json($isEdit),
                headers: [],
                openModal: false,
                isSendDisabled : true,
                isTermDisabled: true,
                cleanHeaders() {
                    this.headers = [];
                },

                addHeader(title, index) {
                    const stepData = {
                        title,
                        index,
                        complete: index < this.activeStep,
                    };

                    this.headers.push(stepData);

                    this.headers.sort((a, b) => a.index - b.index);
                },

                isLastStep(stepNum = null) {
                    return stepNum ? this.headers.length === stepNum : this.headers.length === this.activeStep;
                }
            }"
            x-init="cleanHeaders()"
            wire:key="active-{{ $activeStep }}"
            class="steps"
        >
            <div >
                {{-- Steps Header --}}
                <ol class="steps-header">
                    <template x-for="header in headers" :key="`step-header-${header.index}-${activeStep}`">
                        <li
                            x-data="{
                                isActive: activeStep === header.index,
                                isValidationError: false
                            }"
                            @click="if (header.index <= {{ $currentStep }}) { activeStep = header.index; }"
                            x-init="
                                $watch('$wire.validationErrorStep', value => {
                                    isValidationError = value === header.title;
                                });
                            "
                            class="flex md:w-max-content items-center"
                            :class="{ 'cursor-pointer': header.index <= {{ $currentStep }} }"
                        >
                            {{-- Prepend part to the title --}}
                            <template x-if="!isActive">
                                <span x-text="header.index"
                                    class="steps-header_index"
                                    :class="{
                                        'step-completed-color': header.complete && !isActive && !isValidationError,
                                        'step-incomplete-color': !isActive && !header.complete && !isValidationError,
                                        'text-red-500': isValidationError
                                    }"></span>
                            </template>

                            <template x-if="isActive">
                                <g transform="scale(1.3)" class="hidden sm:inline" :class="{ 'text-blue-600': isActive }">
                                    <svg class="w-4 h-4 sm:w-4 sm:h-4 mx-2.5" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                    </svg>
                                </g>
                            </template>

                            {{-- Title itself --}}
                            <span
                                x-text="header.title"
                                class="steps-header_title"
                                :class="{
                                    'step-completed-color': header.complete && !isActive && !isValidationError,
                                    'step-active-color': isActive && !isValidationError,
                                    'text-red-500': isValidationError,
                                    'after:content-[\'/\']': !isLastStep(header.index)
                                }"
                            ></span>

                            {{-- Last Step --}}
                            <template x-if="!isLastStep(header.index)">
                                <svg
                                    fill="none"
                                    aria-hidden="true"
                                    viewBox="0 0 12 10"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-3 h-3 ms-2 sm:ms-4 hidden sm:inline 3rtl:rotate-180"
                                >
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                                </svg>
                            </template>
                        </li>
                    </template>
                </ol>
            </div>

            <div
                class="form-row"
            >
                {{-- Step Body --}}
                <form id="legal_entity_form">
                    @include('livewire.legal-entity.step._step_edrpou')
                    @include('livewire.legal-entity.step._step_owner')
                    @include('livewire.legal-entity.step._step_contact')
                    @include('livewire.legal-entity.step._step_residence_address')
                    @include('livewire.legal-entity.step._step_accreditation')
                    @include('livewire.legal-entity.step._step_license')
                    @include('livewire.legal-entity.step._step_additional_information')
                    @include('livewire.legal-entity.step._step_public_offer')

                    <div class="steps-footer">
                        {{-- Agreement checkbox --}}
                        <div class="xl:w-1/2" x-show="isLastStep()" x-cloak>
                            <div class="flex items-center">
                                <input type="checkbox" value="isTermDisabled" id="public_offer_consent"
                                    x-on:click="isSendDisabled = !isSendDisabled"
                                    wire:model="legalEntityForm.public_offer.consent" :disabled="isTermDisabled"
                                    :checked="!isTermDisabled"
                                    class="steps-agreement_checkbox"
                                />
                                <label
                                    for="public_offer_consent"
                                    class="steps-agreement_label"
                                >
                                    {{ __('forms.agree') }}
                                    <button
                                        type="button"
                                        class="steps-agreement_button cursor-pointer"
                                        @click="openModal = !openModal"
                                    >
                                        {{ __('forms.withTerms') }}
                                    </button>
                                </label>
                            </div>

                            @error('legalEntityForm.public_offer.consent')
                                <div class='validation-error'>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="w-full flex justify-end">
                            <template x-if="isLastStep()">
                                <button
                                    type="button"
                                    id="submit_button"
                                    class="button-primary cursor-pointer"
                                    wire:click="createLegalEntity"
                                    :disabled="isSendDisabled"
                                >
                                    {{ __('forms.sendRequest') }}
                                </button>
                            </template>

                            <template x-if="!isLastStep()">
                                <button
                                    type="button"
                                    id="next_button"
                                    class="default-button cursor-pointer"
                                    @click="$wire.nextStep(activeStep).then(result => result ? activeStep={{ $currentStep }} : activeStep)"
                                >
                                    {{ __('forms.next') }}
                                </button>
                            </template>
                        </div>
                    </div>
                </form>

                <!-- Main modal -->
                <div
                    x-cloak
                    role="dialog"
                    tabindex="-1"
                    aria-modal="true"
                    x-show="openModal"
                    x-id="['le-modal-term']"
                    :aria-labelledby="$id('le-modal-term')"
                    class="fixed inset-0 z-[100] overflow-y-auto"
                    x-on:keydown.escape.prevent.stop="openModal = false"
                >
                    <!-- Modal overlay -->
                    <div
                        x-show="openModal"
                        @click="openModal = false"
                        class="fixed z-20 inset-0 bg-black/25"
                    ></div>

                    <!-- Modal content -->
                    <div
                        x-on:click.stop
                        x-trap.noscroll.inert="openModal"
                        class="relative w-[70%] h-[75vh] rounded-xl bg-white shadow-lg mx-auto mt-[15vh] flex flex-col dark:bg-gray-800 z-22"
                    >
                        <!-- Modal header -->
                        <div class="p-6 border-b mb-2">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Terms of Use</h3>
                                <p class="block text-xs text-gray-900 dark:text-white">Last updated 01.01.2025</p>
                            </div>

                            <button
                                type="button"
                                @click="openModal = false"
                                class="absolute top-4 right-4 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center cursor-pointer"
                            >
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="flex-1 overflow-y-auto p-6">
                            @include('components.terms')
                        </div>

                        <!-- Modal footer -->
                        <div class="flex gap-4 flex-wrap justify-center p-4 border-t mt-2 mx-auto">
                            <button
                                type="button"
                                class="default-button cursor-pointer"
                                @click="isTermDisabled = false; isSendDisabled = false; openModal = false;"
                            >
                                {{ __('forms.agree') }}
                            </button>

                            <button
                                type="button"
                                class="alternative-button cursor-pointer"
                                @click="isTermDisabled = true; isSendDisabled = true; openModal = false;"
                            >
                                {{ __('forms.decline') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        <x-forms.loading />
        </div>
    </section>
</div>
