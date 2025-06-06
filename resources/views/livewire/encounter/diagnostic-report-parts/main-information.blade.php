<fieldset class="fieldset">
    <legend class="legend">
        {{ __('patients.main_information') }}
    </legend>

    <div>
        {{-- Category --}}
        <div class="form-row-modal">
            <div>
                <label for="diagnosticCategory" class="label-modal">
                    {{ __('forms.category') }}
                </label>
                <select x-model="modalDiagnosticReport.category[0].coding[0].code"
                        id="diagnosticCategory"
                        class="input-modal"
                        type="text"
                        required
                >
                    <option selected value="">{{ __('forms.select') }}</option>
                    @foreach($this->dictionaries['eHealth/diagnostic_report_categories'] as $key => $category)
                        <option value="{{ $key }}" wire:key="{{ $key }}">
                            {{ $category }}
                        </option>
                    @endforeach
                </select>

                <p class="text-error text-xs"
                   x-show="!Object.keys(diagnosticReportCategoriesDictionary).includes(modalDiagnosticReport.category[0].coding[0].code)"
                >
                    {{ __('forms.field_empty') }}
                </p>
            </div>
        </div>

        {{-- Services --}}
        <div class="form-row-modal">
            <div>
                <label for="serviceCode" class="label-modal">
                    {{ __('forms.services') }}
                </label>
                <x-select2 modelPath="modalDiagnosticReport.code.identifier.value"
                           dictionaryName="custom/services"
                           id="serviceCode"
                />

                <p class="text-error text-xs"
                   x-show="!servicesDictionary.some(service => service.id === modalDiagnosticReport.code.identifier.value)"
                >
                    {{ __('forms.field_empty') }}
                </p>
            </div>
        </div>

        {{-- Is referral available --}}
        <div>
            <div class="form-row-3">
                <div class="form-group group">
                    <input x-model="modalDiagnosticReport.isReferralAvailable"
                           @click="modalDiagnosticReport.isReferralAvailable = !modalDiagnosticReport.isReferralAvailable"
                           type="checkbox"
                           name="isReferralAvailable"
                           id="isReferralAvailable"
                           class="default-checkbox mb-1"
                    />
                    <label class="default-p" for="isReferralAvailable">
                        {{ __('patients.referral_available') }}
                    </label>
                </div>
            </div>

            {{-- When referral available --}}
            <template x-if="modalDiagnosticReport.isReferralAvailable">
                <div>
                    <div class="form-row-modal" x-cloak>
                        <div>
                            <label for="referralType" class="label-modal">
                                {{ __('patients.requisition_type') }}
                            </label>
                            <select id="referralType"
                                    class="input-modal"
                                    type="text"
                                    x-model="modalDiagnosticReport.referralType"
                                    required
                            >
                                <option selected value="">{{ __('forms.select') }}</option>
                                <option value="electronic">{{ __('patients.electronic') }}</option>
                                <option value="paper">{{ __('patients.paper') }}</option>
                            </select>
                        </div>

                        {{-- Electronic referral --}}
                        <template x-if="modalDiagnosticReport.referralType === 'electronic'" x-transition>
                            <div>
                                <label for="eReferralNumber" class="label-modal">
                                    {{ __('forms.number') }}
                                </label>
                                <input wire:model="form.encounter.episode.identifier.value"
                                       type="text"
                                       name="eReferralNumber"
                                       id="eReferralNumber"
                                       class="input-modal"
                                       placeholder=" "
                                       required
                                       autocomplete="off"
                                />
                            </div>
                        </template>
                    </div>

                    {{-- Paper referral --}}
                    <template x-if="modalDiagnosticReport.referralType === 'paper'" x-transition>
                        <div>
                            <div class="form-row-modal">
                                <div>
                                    <label for="requisition" class="label-modal">
                                        {{ __('forms.number') }}
                                    </label>
                                    <input x-model="modalDiagnosticReport.paperReferral.requisition"
                                           type="text"
                                           name="requisition"
                                           id="requisition"
                                           class="input-modal"
                                           autocomplete="off"
                                    >
                                </div>

                                <div>
                                    <label for="requesterEmployeeName" class="label-modal">
                                        {{ __('patients.author') }}
                                    </label>
                                    <input x-model="modalDiagnosticReport.paperReferral.requesterEmployeeName"
                                           type="text"
                                           name="requesterEmployeeName"
                                           id="requesterEmployeeName"
                                           class="input-modal"
                                           autocomplete="off"
                                    >
                                </div>
                            </div>

                            <div class="form-row-modal">
                                <div>
                                    <label for="requesterLegalEntityEdrpou" class="label-modal">
                                        {{ __('patients.edrpou_of_the_issuing_institution') }}
                                    </label>
                                    <input x-model="modalDiagnosticReport.paperReferral.requesterLegalEntityEdrpou"
                                           type="text"
                                           name="requesterLegalEntityEdrpou"
                                           id="requesterLegalEntityEdrpou"
                                           class="input-modal"
                                           autocomplete="off"
                                           required
                                    >

                                    <p class="text-error text-xs"
                                       x-show="(modalDiagnosticReport.paperReferral.requesterLegalEntityEdrpou?.trim() || '').length < 1"
                                    >
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>

                                <div>
                                    <label for="requesterLegalEntityName" class="label-modal">
                                        {{ __('patients.name_of_the_institution_that_issued_it') }}
                                    </label>
                                    <input x-model="modalDiagnosticReport.paperReferral.requesterLegalEntityName"
                                           type="text"
                                           name="requesterLegalEntityName"
                                           id="requesterLegalEntityName"
                                           class="input-modal"
                                           autocomplete="off"
                                           required
                                    >

                                    <p class="text-error text-xs"
                                       x-show="(modalDiagnosticReport.paperReferral.requesterLegalEntityName?.trim() || '').length < 1"
                                    >
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>
                            </div>

                            <div class="form-row-modal">
                                <div>
                                    <label for="serviceRequestDate" class="label-modal">
                                        {{ __('patients.date') }}
                                    </label>
                                    <div class="relative flex items-center">
                                        <svg width="20" height="20" class="svg-input absolute left-2.5 pointer-events-none">
                                            <use xlink:href="#svg-calendar-week"></use>
                                        </svg>
                                        <input x-model="modalDiagnosticReport.paperReferral.serviceRequestDate"
                                               type="text"
                                               name="serviceRequestDate"
                                               id="serviceRequestDate"
                                               class="datepicker-input input-modal !pl-10"
                                               autocomplete="off"
                                               required
                                        >
                                    </div>

                                    <p class="text-error text-xs"
                                       x-show="(modalDiagnosticReport.paperReferral.serviceRequestDate?.trim() || '').length < 1"
                                    >
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>

                                <div>
                                    <label for="note" class="label-modal">
                                        {{ __('patients.notes') }}
                                    </label>
                                    <input x-model="modalDiagnosticReport.paperReferral.note"
                                           type="text"
                                           name="note"
                                           id="note"
                                           class="input-modal"
                                           autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Conclusion code by ICD-10 --}}
        <div x-data="{
                 selected: null,
                 results: $wire.entangle('results'),
                 showResults: false
             }"
             class="form-row-modal relative"
        >
            <div>
                <label for="conclusionCode" class="label-modal">
                    {{ __('patients.conclusion_code') }}
                </label>
                <input type="text"
                       @input.debounce.300ms="
                           let value = $event.target.value;
                           let isEnglish = /^[a-zA-Z]+$/.test(value);

                           if ((isEnglish && value.length >= 1) || (!isEnglish && value.length >= 3)) {
                               $wire.searchICD10(value);
                               showResults = true;
                           }
                       "
                       @focus="if (modalDiagnosticReport.query.length >= ( /^[a-zA-Z]+$/.test(modalDiagnosticReport.query) ? 1 : 3 )) showResults = true"
                       @click.away="showResults = false"
                       x-model="modalDiagnosticReport.query"
                       id="conclusionCode"
                       name="conclusionCode"
                       class="input-modal"
                       placeholder="{{ __('forms.select') }}"
                       autocomplete="off"
                />

                <div x-show="showResults && results.length > 0"
                     class="absolute left-0 top-full z-10 max-h-80 w-full overflow-auto overscroll-contain rounded-lg border dark:bg-gray-800 border-gray-200 bg-white p-1.5 shadow-lg"
                >
                    <ul>
                        <template x-for="(result, index) in results" :key="index">
                            <li class="group flex w-full cursor-pointer items-center rounded-md px-2 py-1.5 transition-colors dark:bg-gray-800 dark:text-white"
                                @click="
                                    selected = result;
                                    modalDiagnosticReport.query = result.code + ' - ' + result.description;
                                    modalDiagnosticReport.conclusionCode.coding[0].code = result.code;
                                    showResults = false
                                "
                            >
                                <span x-text="result.code + ' - ' + result.description"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                <p x-show="showResults && results.length == 0" class="px-2 py-1.5 text-gray-600">
                    {{ __('forms.nothing_found') }}
                </p>

                <x-forms.loading/>
            </div>
        </div>

        {{-- Conclusion --}}
        <div class="form-row">
            <div>
                <label for="conclusion" class="label-modal">
                    {{ __('patients.conclusion') }}
                </label>
                <div>
                    <textarea rows="4"
                              x-model="modalDiagnosticReport.conclusion"
                              id="conclusion"
                              name="conclusion"
                              class="textarea"
                              placeholder="{{ __('patients.write_comment_here') }}"
                              maxlength="1000"
                    ></textarea>
                </div>
            </div>
        </div>
    </div>
</fieldset>
