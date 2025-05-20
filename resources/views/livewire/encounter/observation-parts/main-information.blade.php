<fieldset class="fieldset">
    <legend class="legend">
        {{ __('patients.main_information') }}
    </legend>

    <div class="flex gap-20 md:mb-5 mb-4">
        <h2 class="default-p font-bold">{{ __('patients.information_source') }}</h2>
        <div class="flex items-center">
            <input @change="modalObservation.primarySource = true"
                   x-model.boolean="modalObservation.primarySource"
                   id="performer"
                   type="radio"
                   value="true"
                   name="primarySource"
                   class="default-radio"
                   :checked="modalObservation.primarySource === true"
            >
            <label for="performer" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ __('patients.performer') }}
            </label>
        </div>

        <div class="flex items-center">
            <input @change="modalObservation.primarySource = false"
                   x-model.boolean="modalObservation.primarySource"
                   id="patient"
                   type="radio"
                   value="false"
                   name="primarySource"
                   class="default-radio"
                   :checked="modalObservation.primarySource === false"
            >
            <label for="patient" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ __('patients.other_source') }}
            </label>
        </div>
    </div>

    <div x-data="{
             codeMap: $wire.entangle('observationCodeMap'),
             valueMap: $wire.entangle('observationValueMap'),
             codeableConceptValues: $wire.entangle('codeableConceptValues')
         }"
    >

        <div x-show="modalObservation.primarySource === false">
            <div class="form-row-modal">
                <div>
                    <label for="reportOrigin" class="label-modal">
                        {{ __('patients.source_link') }}
                    </label>
                    <select class="input-modal"
                            x-model="modalObservation.reportOrigin.coding[0].code"
                            id="reportOrigin"
                            type="text"
                            required
                    >
                        <option selected>{{ __('forms.select') }}</option>
                        @foreach($this->dictionaries['eHealth/report_origins'] as $key => $reportOrigin)
                            <option value="{{ $key }}" wire:key="{{ $key }}">
                                {{ $reportOrigin }}
                            </option>
                        @endforeach
                    </select>

                    <p class="text-error text-xs"
                       x-show="!Object.keys(reportOriginsDictionary).includes(modalObservation.reportOrigin.coding[0].code)"
                    >
                        {{ __('forms.field_empty') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="form-row-modal">
            <div>
                <label for="performerCategories" class="label-modal">
                    {{ __('forms.category') }}
                </label>
                <select class="input-modal"
                        x-model="modalObservation.categories.coding[0].code"
                        id="performerCategories"
                        type="text"
                        required
                >
                    <option selected>{{ __('forms.select') }}</option>
                    <template x-for="
                                  (label, key) in modalObservation.codingSystem === 'loinc'
                                      ? $wire.dictionaries['eHealth/observation_categories']
                                      : $wire.dictionaries['eHealth/ICF/observation_categories']
                              "
                              :key="key"
                    >
                        <option :value="key" x-text="label"></option>
                    </template>
                </select>

                <p class="text-error text-xs"
                   x-show="!(
                       Object.keys(observationCategoriesDictionary).includes(modalObservation.categories.coding[0].code)
                       || Object.keys(icfObservationCategoriesDictionary).includes(modalObservation.categories.coding[0].code)
                   )"
                >
                    {{ __('forms.field_empty') }}
                </p>
            </div>

            <div>
                <label for="performerCode" class="label-modal">
                    {{ __('patients.code') }}
                </label>

                {{-- Show select2 when code is laboratory --}}
                <template x-if="modalObservation.categories.coding[0].code === 'laboratory'">
                    <x-select2 modelPath="modalObservation.code.coding[0].code"
                               dictionaryName="eHealth/LOINC/observation_codes"
                               id="performerCode"
                    />
                </template>

                {{-- Show select2 for ICF --}}
                <template x-if="
                              modalObservation.codingSystem === 'icf' &&
                              ['functions', 'structures', 'activities', 'environmental'].includes(modalObservation.categories.coding[0].code)
                          "
                >
                    <x-select2 modelPath="modalObservation.code.coding[0].code"
                               dictionaryName="eHealth/ICF/classifiers"
                               id="performerCode"
                    />
                </template>

                <template x-if="
                              modalObservation.categories.coding[0].code !== 'laboratory' &&
                              modalObservation.codingSystem === 'loinc'
                          "
                >
                    <select class="input-modal"
                            x-model="modalObservation.code.coding[0].code"
                            id="performerCode"
                            type="text"
                            required
                    >
                        <option selected>{{ __('forms.select') }}</option>

                        <template x-if="modalObservation.codingSystem === 'loinc'">
                            <div>
                                @foreach($this->dictionaries['eHealth/LOINC/observation_codes'] as $key => $code)
                                    <template x-if="
                                                  !modalObservation.categories.coding[0].code ||
                                                  (codeMap[modalObservation.categories.coding[0].code]?.includes('{{ $key }}'))
                                              "
                                    >
                                        <option value="{{ $key }}">{{ $code }}</option>
                                    </template>
                                @endforeach
                            </div>
                        </template>
                    </select>
                </template>

                <p class="text-error text-xs"
                   x-show="!(
                       Object.keys(observationCodesDictionary).includes(modalObservation.code.coding[0].code) ||
                       Object.keys(icfObservationCodesDictionary).includes(modalObservation.code.coding[0].code)
                   )"
                >
                    {{ __('forms.field_empty') }}
                </p>
            </div>
        </div>

        {{-- value codeable concept --}}
        <template x-if="
                      valueMap[modalObservation.code.coding[0].code] &&
                      valueMap[modalObservation.code.coding[0].code][1] === 'value_codeable_concept'
                  "
        >
            <div class="form-row-modal">
                <div>
                    <label for="valueCodeableConcept" class="label-modal">
                        {{ __('patients.value') }}
                    </label>

                    <select class="input-modal"
                            x-model="modalObservation.valueCodeableConcept"
                            id="valueCodeableConcept"
                            type="text"
                            required
                    >
                        <option selected>{{ __('forms.select') }}</option>
                        <template :key="key"
                                  x-for="(label, key) in codeableConceptValues[valueMap[modalObservation.code.coding[0].code]?.[0]]"
                        >
                            <option :value="key" x-text="label"></option>
                        </template>
                    </select>
                </div>
            </div>
        </template>

        {{-- value boolean --}}
        <template x-if="
                      valueMap[modalObservation.code.coding[0].code] &&
                      valueMap[modalObservation.code.coding[0].code][1] === 'value_boolean'
                  "
        >
            <div class="flex gap-20">
                <h3 class="default-p font-bold">{{ __('patients.value') }}</h3>

                <div>
                    <input @change="modalObservation.valueBoolean = true"
                           x-model.boolean="modalObservation.valueBoolean"
                           id="valueBooleanYes"
                           type="radio"
                           value="yes"
                           name="valueBoolean"
                           class="default-radio"
                    >
                    <label for="valueBooleanYes" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        {{ __('forms.yes') }}
                    </label>
                </div>

                <div>
                    <input @change="modalObservation.valueBoolean = false"
                           x-model.boolean="modalObservation.valueBoolean"
                           id="valueBooleanNo"
                           type="radio"
                           value="no"
                           name="valueBoolean"
                           class="default-radio"
                    >
                    <label for="valueBooleanNo" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        {{ __('forms.no') }}
                    </label>
                </div>

                <p class="text-error text-xs" x-show="modalObservation.valueBoolean === undefined">
                    {{ __('forms.field_empty') }}
                </p>
            </div>
        </template>

        {{-- value string --}}
        <template x-if="
                      valueMap[modalObservation.code.coding[0].code] &&
                      valueMap[modalObservation.code.coding[0].code][1] === 'value_string'
                  "
        >
            <div class="form-row-modal">
                <div>
                    <label for="valueString" class="label-modal">
                        {{ __('patients.value') }}
                    </label>
                    <input x-model="modalObservation.valueString"
                           type="text"
                           name="valueString"
                           id="valueString"
                           class="input-modal"
                           autocomplete="off"
                    >

                    <p class="text-error text-xs" x-show="(modalObservation.valueString || '').trim().length === 0">
                        {{ __('forms.field_empty') }}
                    </p>
                </div>
            </div>
        </template>

        {{-- value quantity --}}
        <div x-show="
                      valueMap[modalObservation.code.coding[0].code] &&
                      valueMap[modalObservation.code.coding[0].code][1] === 'value_quantity'
                  "
        >
            <div class="form-row-modal">
                <form class="max-w-xs mx-auto">
                    <label for="valueQuantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('patients.value') }}
                        <span x-text="
                                  modalObservation.code.coding?.[0]?.code && modalObservation.code.coding[0].code !== 'Обрати' && valueMap[modalObservation.code.coding[0].code]?.[2] ?
                                  `(одиниця виміру &quot;${valueMap[modalObservation.code.coding[0].code][2]}&quot;)` :
                                  ''
                              "
                        ></span>
                    </label>
                    <div class="relative flex items-center max-w-[8rem]">
                        <button type="button"
                                id="decrement-button"
                                @click="
                                    const min = parseInt(valueMap[modalObservation.code.coding?.[0]?.code]?.[0]?.split('-')?.[0] || 0);
                                    modalObservation.valueQuantity = Math.max(min, (modalObservation.valueQuantity || 0) - 1);
                                "
                                class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"
                        >
                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2"
                            >
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M1 1h16"
                                />
                            </svg>
                        </button>

                        <input type="text"
                               id="valueQuantity"
                               aria-describedby="quantity"
                               class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               placeholder="1"
                               required
                               x-model.number="modalObservation.valueQuantity"
                               autocomplete="off"
                               :min="valueMap[modalObservation.code.coding[0].code]?.[0]?.split('-')?.[0] || 0"
                               :max="valueMap[modalObservation.code.coding[0].code]?.[0]?.split('-')?.[1] || 1000"
                               @input="
                                   const min = parseInt(valueMap[modalObservation.code.coding[0].code]?.[0]?.split('-')?.[0] || 0);
                                   const max = parseInt(valueMap[modalObservation.code.coding[0].code]?.[0]?.split('-')?.[1] || 1000);
                                   if (modalObservation.valueQuantity < min) modalObservation.valueQuantity = min;
                                   if (modalObservation.valueQuantity > max) modalObservation.valueQuantity = max;
                               "
                        />

                        <button type="button"
                                id="increment-button"
                                @click="
                                    const max = parseInt(valueMap[modalObservation.code.coding?.[0]?.code]?.[0]?.split('-')?.[1] || 1000);
                                    modalObservation.valueQuantity = Math.min(max, (modalObservation.valueQuantity || 0) + 1);
                                "
                                class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"
                        >
                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18"
                            >
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M9 1v16M1 9h16"
                                />
                            </svg>
                        </button>
                    </div>

                    <p id="quantity" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <span x-text="
                            modalObservation?.code?.coding?.[0]?.code && valueMap[modalObservation.code.coding[0].code]?.[0] ?
                            'від ' + valueMap[modalObservation.code.coding[0].code][0].split('-')[0] + ' до ' + valueMap[modalObservation.code.coding[0].code][0].split('-')[1] :
                            ''
                        "
                        ></span>
                    </p>
                </form>
            </div>
        </div>

        {{-- value date time --}}
        <template x-if="
                      valueMap[modalObservation.code.coding[0].code] &&
                      valueMap[modalObservation.code.coding[0].code][1] === 'value_date_time'
                  "
        >
            <div class="form-row-3">
                <div>
                    <label for="valueDate" class="label-modal">
                        {{ __('patients.date') }}
                    </label>
                    <div class="relative flex items-center">
                        <svg width="20" height="20" class="svg-input absolute left-2.5 pointer-events-none">
                            <use xlink:href="#svg-calendar-week"></use>
                        </svg>
                        <input x-model="modalObservation.valueDate"
                               datepicker-max-date="{{ now()->format('Y-m-d') }}"
                               type="text"
                               name="valueDate"
                               id="valueDate"
                               class="datepicker-input input-modal !pl-10"
                               autocomplete="off"
                               required
                        >
                    </div>

                    <p class="text-error text-xs" x-show="(modalObservation.valueDate || '').trim().length === 0">
                        {{ __('forms.field_empty') }}
                    </p>
                </div>

                <div class="w-1/2" onclick="document.getElementById('valueTime').showPicker()">
                    <label for="valueTime" class="label-modal">
                        {{ __('patients.time') }}
                    </label>

                    <div class="relative flex items-center">
                        <svg width="20" height="20" class="svg-input absolute left-2.5 pointer-events-none">
                            <use xlink:href="#svg-clock"></use>
                        </svg>
                        <input x-model="modalObservation.valueTime"
                               datepicker-max-date="{{ now()->format('Y-m-d') }}"
                               type="time"
                               name="valueTime"
                               id="valueTime"
                               class="input-modal !pl-10"
                               autocomplete="off"
                               required
                        >
                    </div>

                    <p class="text-error text-xs" x-show="(modalObservation.valueTime || '').trim().length === 0">
                        {{ __('forms.field_empty') }}
                    </p>
                </div>
            </div>
        </template>

        {{-- Функції організму (b) --}}
        <template x-if="modalObservation.code.coding[0].code.startsWith('b')">
            <div>
                <h3 class="default-p font-bold my-10">{{ __('patients.components') }}</h3>

                <div class="form-row-modal">
                    <div>
                        <label for="extentCode" class="label-modal">
                            {{ __('patients.extent_or_magnitude_of_violation') }}
                        </label>

                        <select class="input-modal"
                                x-init="
                                    modalObservation.components[0].code.coding[0].code = 'extent_or_magnitude_of_impairment';
                                    modalObservation.components[0].code.coding[0].system = 'eHealth/ICF/qualifiers';
                                "
                                id="extentCode"
                                type="text"
                                required
                                disabled
                        >
                            <option value="extent_or_magnitude_of_impairment">
                                {{ $this->dictionaries['eHealth/ICF/qualifiers']['extent_or_magnitude_of_impairment'] }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="extentInterpretationCode" class="label-modal">
                            {{ __('patients.interpretation') }}
                        </label>

                        <select class="input-modal"
                                x-model="modalObservation.components[0].interpretation.coding[0].code"
                                @change="modalObservation.components[0].interpretation.coding[0].system = 'eHealth/ICF/qualifiers/extent_or_magnitude_of_impairment'"
                                id="extentInterpretationCode"
                                type="text"
                                required
                        >
                            <option selected>{{ __('forms.select') }}</option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/extent_or_magnitude_of_impairment'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>

                        <p class="text-error text-xs"
                           x-show="!Object.keys(extentOrMagnitudeOfImpairmentDictionary).includes(modalObservation.components[0].interpretation.coding[0].code)"
                        >
                            {{ __('forms.field_empty') }}
                        </p>
                    </div>
                </div>
            </div>
        </template>

        {{-- Структури організму (s) --}}
        <template x-if="modalObservation.code.coding[0].code.startsWith('s')">
            <div>
                <h3 class="default-p font-bold my-10">{{ __('patients.components') }}</h3>

                <div class="form-row-modal">
                    <div>
                        <label for="extentCode" class="label-modal">
                            {{ __('patients.extent_or_magnitude_of_violation') }}
                        </label>

                        <select class="input-modal"
                                x-init="
                                    modalObservation.components[0].code.coding[0].code = 'extent_or_magnitude_of_impairment';
                                    modalObservation.components[0].code.coding[0].system = 'eHealth/ICF/qualifiers';
                                "
                                id="extentCode"
                                type="text"
                                required
                                disabled
                        >
                            <option value="extent_or_magnitude_of_impairment">
                                {{ $this->dictionaries['eHealth/ICF/qualifiers']['extent_or_magnitude_of_impairment'] }}
                            </option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/extent_or_magnitude_of_impairment'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="extentInterpretationCode" class="label-modal">
                            {{ __('patients.interpretation') }}
                        </label>

                        <select class="input-modal"
                                x-model="modalObservation.components[0].interpretation.coding[0].code"
                                @change="modalObservation.components[0].interpretation.coding[0].system = 'eHealth/ICF/qualifiers/extent_or_magnitude_of_impairment'"
                                id="extentInterpretationCode"
                                type="text"
                                required
                        >
                            <option selected>{{ __('forms.select') }}</option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/extent_or_magnitude_of_impairment'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>

                        <p class="text-error text-xs"
                           x-show="!Object.keys(extentOrMagnitudeOfImpairmentDictionary).includes(modalObservation.components[0].interpretation.coding[0].code)"
                        >
                            {{ __('forms.field_empty') }}
                        </p>
                    </div>
                </div>

                <div class="form-row-modal">
                    <div x-init="
                        modalObservation.components[1] = {
                            code: {
                                coding: [{
                                    system: 'eHealth/ICF/qualifiers',
                                    code: 'nature_of_change_in_body_structure'
                                }],
                                text: ''
                            },
                            interpretation: {
                                coding: [{
                                    system: '',
                                    code: ''
                                }],
                                text: ''
                            }
                        };
                    "
                    >
                        <label for="natureCode" class="label-modal">
                            {{ __('patients.extent_or_magnitude_of_violation') }}
                        </label>

                        <select class="input-modal"
                                id="natureCode"
                                type="text"
                                required
                                disabled
                        >
                            <option value="nature_of_change_in_body_structure">
                                {{ $this->dictionaries['eHealth/ICF/qualifiers']['nature_of_change_in_body_structure'] }}
                            </option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/nature_of_change_in_body_structure'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="natureInterpretationCode" class="label-modal">
                            {{ __('patients.interpretation') }}
                        </label>

                        <select class="input-modal"
                                x-model="modalObservation.components[1].interpretation.coding[0].code"
                                @change="modalObservation.components[1].interpretation.coding[0].system = 'eHealth/ICF/qualifiers/nature_of_change_in_body_structure'"
                                id="natureInterpretationCode"
                                type="text"
                                required
                        >
                            <option selected>{{ __('forms.select') }}</option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/nature_of_change_in_body_structure'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>

                        <p class="text-error text-xs"
                           x-show="!Object.keys(natureOfChangeInBodyStructureDictionary).includes(modalObservation.components[1].interpretation.coding[0].code)"
                        >
                            {{ __('forms.field_empty') }}
                        </p>
                    </div>
                </div>

                <div class="form-row-modal">
                    <div x-init="
                        modalObservation.components[2] = {
                            code: {
                                coding: [{
                                    system: 'eHealth/ICF/qualifiers',
                                    code: 'anatomical_localization'
                                }],
                                text: ''
                            },
                            interpretation: {
                                coding: [{
                                    system: '',
                                    code: ''
                                }],
                                text: ''
                            }
                        };
                    "
                    >
                        <label for="anatomicalCode" class="label-modal">
                            {{ __('patients.extent_or_magnitude_of_violation') }}
                        </label>

                        <select class="input-modal"
                                id="anatomicalCode"
                                type="text"
                                required
                                disabled
                        >
                            <option value="anatomical_localization">
                                {{ $this->dictionaries['eHealth/ICF/qualifiers']['anatomical_localization'] }}
                            </option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/anatomical_localization'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="anatomicalInterpretationCode" class="label-modal">
                            {{ __('atients.interpretation') }}
                        </label>

                        <select class="input-modal"
                                x-model="modalObservation.components[2].interpretation.coding[0].code"
                                @change="modalObservation.components[2].interpretation.coding[0].system = 'eHealth/ICF/qualifiers/anatomical_localization'"
                                id="anatomicalInterpretationCode"
                                type="text"
                                required
                        >
                            <option selected>{{ __('forms.select') }}</option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/anatomical_localization'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>

                        <p class="text-error text-xs"
                           x-show="!Object.keys(anatomicalLocalizationDictionary).includes(modalObservation.components[2].interpretation.coding[0].code)"
                        >
                            {{ __('forms.field_empty') }}
                        </p>
                    </div>
                </div>
            </div>
        </template>

        {{-- Активність та Участь (d) --}}
        <template x-if="modalObservation.code.coding[0].code.startsWith('d')">
            <div>
                <h3 class="default-p font-bold my-10">{{ __('patients.components') }}</h3>

                <div class="form-row-modal">
                    <div>
                        <label for="performanceCode" class="label-modal">
                            {{ __('patients.extent_or_magnitude_of_violation') }}
                        </label>

                        <select class="input-modal"
                                x-init="
                                    modalObservation.components[0].code.coding[0].code = 'performance';
                                    modalObservation.components[0].code.coding[0].system = 'eHealth/ICF/qualifiers';
                                "
                                id="performanceCode"
                                type="text"
                                required
                                disabled
                        >
                            <option value="performance">
                                {{ $this->dictionaries['eHealth/ICF/qualifiers']['performance'] }}
                            </option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/performance'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="performanceInterpretationCode" class="label-modal">
                            {{ __('patients.interpretation') }}
                        </label>

                        <select class="input-modal"
                                x-model="modalObservation.components[0].interpretation.coding[0].code"
                                @change="modalObservation.components[0].interpretation.coding[0].system = 'eHealth/ICF/qualifiers/performance'"
                                id="performanceInterpretationCode"
                                type="text"
                                required
                        >
                            <option selected>{{ __('forms.select') }}</option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/performance'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>

                        <p class="text-error text-xs"
                           x-show="!Object.keys(performanceDictionary).includes(modalObservation.components[0].interpretation.coding[0].code)"
                        >
                            {{ __('forms.field_empty') }}
                        </p>
                    </div>
                </div>

                <div class="form-row-modal">
                    <div x-init="
                        modalObservation.components[1] = {
                            code: {
                                coding: [{
                                    system: 'eHealth/ICF/qualifiers',
                                    code: 'capacity'
                                }],
                                text: ''
                            },
                            interpretation: {
                                coding: [{
                                    system: '',
                                    code: ''
                                }],
                                text: ''
                            }
                        };
                    "
                    >
                        <label for="capacityCode" class="label-modal">
                            {{ __('patients.extent_or_magnitude_of_violation') }}
                        </label>

                        <select class="input-modal"
                                id="capacityCode"
                                type="text"
                                required
                                disabled
                        >
                            <option value="capacity">
                                {{ $this->dictionaries['eHealth/ICF/qualifiers']['capacity'] }}
                            </option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/capacity'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="capacityInterpretationCode" class="label-modal">
                            {{ __('atients.interpretation') }}
                        </label>

                        <select class="input-modal"
                                x-model="modalObservation.components[1].interpretation.coding[0].code"
                                @change="modalObservation.components[1].interpretation.coding[0].system = 'eHealth/ICF/qualifiers/capacity'"
                                id="capacityInterpretationCode"
                                type="text"
                                required
                        >
                            <option selected>{{ __('forms.select') }}</option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/capacity'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>

                        <p class="text-error text-xs"
                           x-show="!Object.keys(capacityDictionary).includes(modalObservation.components[1].interpretation.coding[0].code)"
                        >
                            {{ __('forms.field_empty') }}
                        </p>
                    </div>
                </div>
            </div>
        </template>

        {{-- Фактори середовища (e) --}}
        <template x-if="modalObservation.code.coding[0].code.startsWith('e')">
            <div>
                <h3 class="default-p font-bold my-10">{{ __('patients.components') }}</h3>

                <div class="form-row-modal">
                    <div>
                        <label for="barrierCode" class="label-modal">
                            {{ __('patients.extent_or_magnitude_of_violation') }}
                        </label>

                        <select class="input-modal"
                                x-init="
                                    modalObservation.components[0].code.coding[0].code = 'barrier_or_facilitator';
                                    modalObservation.components[0].code.coding[0].system = 'eHealth/ICF/qualifiers';
                                "
                                id="barrierCode"
                                type="text"
                                required
                                disabled
                        >
                            <option value="barrier_or_facilitator">
                                {{ $this->dictionaries['eHealth/ICF/qualifiers']['barrier_or_facilitator'] }}
                            </option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/barrier_or_facilitator'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="barrierInterpretationCode" class="label-modal">
                            {{ __('patients.interpretation') }}
                        </label>

                        <select class="input-modal"
                                x-model="modalObservation.components[0].interpretation.coding[0].code"
                                @change="modalObservation.components[0].interpretation.coding[0].system = 'eHealth/ICF/qualifiers/barrier_or_facilitator'"
                                id="barrierInterpretationCode"
                                type="text"
                                required
                        >
                            <option selected>{{ __('forms.select') }}</option>
                            @foreach($this->dictionaries['eHealth/ICF/qualifiers/barrier_or_facilitator'] as $key => $code)
                                <option value="{{ $key }}">{{ $code }}</option>
                            @endforeach
                        </select>

                        <p class="text-error text-xs"
                           x-show="!Object.keys(barrierOrFacilitatorDictionary).includes(modalObservation.components[0].interpretation.coding[0].code)"
                        >
                            {{ __('forms.field_empty') }}
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </div>
</fieldset>
