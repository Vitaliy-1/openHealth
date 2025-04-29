<fieldset class="fieldset">
    <legend class="legend">
        {{ __('patients.data') }}
    </legend>

    <div class="form-row-3">
        {{-- Fix autofocus that was on date --}}
        <button class="sr-only" autofocus tabindex="-1"></button>
        <div>
            <label for="immunizationDate" class="label-modal">
                {{ __('patients.date') }}
            </label>
            <div class="relative flex items-center">
                <svg width="20" height="20"
                     class="svg-input absolute left-2.5 pointer-events-none"
                >
                    <use xlink:href="#svg-calendar-week"></use>
                </svg>
                <input x-model="modalImmunization.date"
                       datepicker-max-date="{{ now()->format('Y-m-d') }}"
                       type="text"
                       name="immunizationDate"
                       id="immunizationDate"
                       class="datepicker-input input-modal !pl-10"
                       autocomplete="off"
                       required
                >
            </div>

            <p class="text-error text-xs" x-show="modalImmunization.date.trim() === ''">
                {{ __('forms.field_empty') }}
            </p>
        </div>

        <div class="w-1/2">
            <label for="immunizationTime" class="label-modal" onclick="document.getElementById('time').showPicker()">
                {{ __('patients.time') }}
            </label>

            <div class="relative flex items-center">
                <svg width="20" height="20"
                     class="svg-input absolute left-2.5 pointer-events-none"
                >
                    <use xlink:href="#svg-clock"></use>
                </svg>
                <input x-model="modalImmunization.time"
                       datepicker-max-date="{{ now()->format('Y-m-d') }}"
                       type="time"
                       name="immunizationTime"
                       id="immunizationTime"
                       class="input-modal !pl-10"
                       autocomplete="off"
                       required
                >
            </div>

            <p class="text-error text-xs" x-show="modalImmunization.time.trim() === ''">
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>

    <div class="mt-12">
        <div class="flex gap-20 md:mb-5 mb-4">
            <h2 class="default-p font-bold">{{ __('patients.has_it_been_done') }}</h2>
            <div class="flex items-center">
                <input x-model="modalImmunization.notGiven"
                       @change="modalImmunization.notGiven = false"
                       id="yes"
                       type="radio"
                       value="false"
                       name="notGiven"
                       class="default-radio"
                       :checked="modalImmunization.notGiven === false"
                >
                <label for="yes" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('forms.yes') }}
                </label>
            </div>

            <div class="flex items-center">
                <input x-model="modalImmunization.notGiven"
                       @change="modalImmunization.notGiven = true"
                       id="no"
                       type="radio"
                       value="true"
                       name="notGiven"
                       class="default-radio"
                       :checked="modalImmunization.notGiven === true"
                >
                <label for="no" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('forms.no') }}
                </label>
            </div>
        </div>

        <div>
            <div x-show="modalImmunization.notGiven === false" class="form-group group">
                <template x-for="(reason, index) in modalImmunization.explanation.reasons" :key="index">
                    <div class="form-row-modal md:mb-0">
                        <div class="form-group group">
                            <label :for="'reasonExplanation-' + index" class="label-modal">
                                {{ __('patients.reasons') }}
                            </label>
                            <select x-model="reason.coding[0].code"
                                    :id="'reasonExplanation-' + index"
                                    class="input-modal"
                                    required
                            >
                                <option selected>{{ __('forms.select') }}</option>
                                @foreach($this->dictionaries['eHealth/reason_explanations'] as $key => $reasonExplanation)
                                    <option value="{{ $key }}" wire:key="{{ $key }}">
                                        {{ $reasonExplanation }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="text-error text-xs"
                               x-show="!Object.keys(reasonExplanationsDictionary).includes(reason.coding[0].code)"
                            >
                                {{ __('forms.field_empty') }}
                            </p>
                        </div>

                        <!-- Remove Button -->
                        <template x-if="index == modalImmunization.explanation.reasons.length - 1 & index != 0">
                            <button type="button"
                                    @click="modalImmunization.explanation.reasons.pop(), index--"
                                    class="item-remove"
                            >
                                <svg>
                                    <use xlink:href="#svg-minus"></use>
                                </svg>
                                {{ __('forms.delete') }}
                            </button>
                        </template>
                        <!-- Add Button -->
                        <template x-if="index === modalImmunization.explanation.reasons.length - 1">
                            <button type="button"
                                    @click="modalImmunization.explanation.reasons.push({ coding: [{ system: 'eHealth/reason_explanations', code: '' }] })"
                                    class="item-add lg:justify-self-start"
                                    :class="{ 'lg:justify-self-start': index > 0 }"
                            >
                                <svg>
                                    <use xlink:href="#svg-plus"></use>
                                </svg>
                                {{ __('forms.add') }}
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            <div x-show="modalImmunization.notGiven === true" class="form-group group !w-1/2">
                <label for="reasonsNotGiven" class="label-modal">
                    {{ __('patients.reasons') }}
                </label>
                <select type="text"
                        x-model="modalImmunization.explanation.reasonsNotGiven.coding[0].code"
                        id="reasonsNotGiven"
                        class="input-modal"
                        required
                >
                    <option selected>{{ __('forms.select') }}</option>
                    @foreach($this->dictionaries['eHealth/reason_not_given_explanations'] as $key => $reasonNotGivenExplanation)
                        <option value="{{ $key }}" wire:key="{{ $key }}">
                            {{ $reasonNotGivenExplanation }}
                        </option>
                    @endforeach
                </select>

                <p class="text-error text-xs"
                   x-show="!Object.keys(reasonNotGivenExplanationsDictionary).includes(modalImmunization.explanation.reasonsNotGiven.coding[0].code)"
                >
                    {{ __('forms.field_empty') }}
                </p>
            </div>
        </div>
    </div>

    <div class="mt-12">
        <div class="flex gap-20 md:mb-5 mb-4">
            <h2 class="default-p font-bold">{{ __('patients.information_source') }}</h2>
            <div class="flex items-center">
                <input @change="modalImmunization.primarySource = true"
                       x-model.boolean="modalImmunization.primarySource"
                       id="performer"
                       type="radio"
                       value="true"
                       name="primarySource"
                       class="default-radio"
                       :checked="modalImmunization.primarySource === true"
                >
                <label for="performer" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('patients.performer') }}
                </label>
            </div>

            <div class="flex items-center">
                <input @change="modalImmunization.primarySource = false"
                       x-model.boolean="modalImmunization.primarySource"
                       id="patient"
                       type="radio"
                       value="false"
                       name="primarySource"
                       class="default-radio"
                       :checked="modalImmunization.primarySource === false"
                >
                <label for="patient" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ __('forms.patient') }}
                </label>
            </div>
        </div>

        <div x-show="modalImmunization.primarySource === false">
            <div class="form-row-modal">
                <div>
                    <label for="reportOrigin" class="label-modal">
                        {{ __('patients.source_link') }}
                    </label>
                    <select class="input-modal"
                            x-model="modalImmunization.reportOrigin.coding[0].code"
                            id="reportOrigin"
                            type="text"
                            required
                    >
                        <option selected>{{ __('forms.select') }}</option>
                        @foreach($this->dictionaries['eHealth/immunization_report_origins'] as $key => $reportOrigin)
                            <option value="{{ $key }}" wire:key="{{ $key }}">
                                {{ $reportOrigin }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row-modal">
                <div class="form-group group">
                    <label for="doctorComment" class="label-modal">
                        {{ __('forms.additional_info') }}
                    </label>
                    <textarea class="textarea"
                              x-model="modalImmunization.reportOrigin.text"
                              id="doctorComment"
                              name="doctorComment"
                              rows="4"
                              placeholder="{{ __('patients.write_comment_here') }}"
                    ></textarea>
                </div>
            </div>
        </div>
    </div>
</fieldset>
