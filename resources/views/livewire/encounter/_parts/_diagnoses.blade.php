@php
    $conditionCodes = $this->dictionaries['eHealth/ICPC2/condition_codes'];
    ksort($conditionCodes);
@endphp
{{-- Component to input values to the table through the Modal, built with Alpine --}}

<fieldset class="fieldset"
          {{-- Binding conditions to Alpine, it will be re-used in the modal.
            Note that it's necessary for modal to work properly --}}
          x-data="{
                  conditions: $wire.entangle('form.conditions'),
                  diagnoses: $wire.entangle('form.encounter.diagnoses'),
                  openModal:false,
                  modalCondition: new Condition(),
                  newCondition: false,
                  item: 0,
                  conditionCodesDictionary: $wire.dictionaries['eHealth/ICPC2/condition_codes'],
                  diagnosisRolesDictionary: $wire.dictionaries['eHealth/diagnosis_roles'],
                  conditionClinicalStatusesRolesDictionary: $wire.dictionaries['eHealth/condition_clinical_statuses'],
                  conditionVerificationStatusesDictionary: $wire.dictionaries['eHealth/condition_verification_statuses'],
              }"
>
    <legend class="legend">
        <h2>{{ __('patients.diagnoses') }}</h2>
    </legend>

    <table class="table-input w-inherit">
        <thead class="thead-input">
        <tr>
            <th scope="col" class="th-input">{{ __('patients.code_and_name') }}</th>
            <th scope="col" class="th-input">{{ __('forms.type') }}</th>
            <th scope="col" class="th-input">{{ __('patients.clinical_status') }}</th>
            <th scope="col" class="th-input">{{ __('patients.verification_status') }}</th>
            <th scope="col" class="th-input">{{ __('forms.comment') }}</th>
            <th scope="col" class="th-input">{{ __('forms.action') }}</th>
        </tr>
        </thead>
        <tbody>
        <template x-for="(condition, index) in conditions">
            <tr>
                <td class="td-input"
                    x-text="`${ condition.code.coding[0]['code'] } - ${ conditionCodesDictionary[condition.code.coding[0]['code']] }`"
                ></td>
                <td class="td-input"
                    x-text="`${ diagnosisRolesDictionary[condition.diagnoses.role.coding[0].code] }`"
                ></td>
                <td class="td-input"
                    x-text="`${ conditionClinicalStatusesRolesDictionary[condition.clinicalStatus] }`"
                ></td>
                <td class="td-input"
                    x-text="`${ conditionVerificationStatusesDictionary[condition.verificationStatus] }`"
                ></td>
                <td class="td-input"
                    x-text="`${ condition.asserter.identifier.type.text }`"
                >
                </td>
                <td class="td-input">
                    {{-- That all that is needed for the dropdown --}}
                    <div x-data="{
                                 openDropdown: false,
                                 toggle() {
                                     if (this.openDropdown) {
                                         return this.close()
                                     }

                                     this.$refs.button.focus()

                                     this.openDropdown = true
                                 },
                                 close(focusAfter) {
                                     if (!this.openDropdown) return

                                     this.openDropdown = false

                                     focusAfter && focusAfter.focus()
                                 }
                             }"
                         @keydown.escape.prevent.stop="close($refs.button)"
                         @focusin.window="! $refs.panel.contains($event.target) && close()"
                         x-id="['dropdown-button']"
                         class="relative"
                    >
                        {{-- Dropdown Button --}}
                        <button x-ref="button"
                                @click="toggle()"
                                :aria-expanded="openDropdown"
                                :aria-controls="$id('dropdown-button')"
                                type="button"
                                class="cursor-pointer"
                        >
                            <svg class="w-6 h-6 text-gray-800 dark:text-gray-200" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                 viewBox="0 0 24 24"
                            >
                                <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M7 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h1m4-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.441 1.559a1.907 1.907 0 0 1 0 2.698l-6.069 6.069L10 19l.674-3.372 6.07-6.07a1.907 1.907 0 0 1 2.697 0Z"
                                />
                            </svg>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div class="absolute" style="left: 50%"> {{-- Center a dropdown panel --}}
                            <div x-ref="panel"
                                 x-show="openDropdown"
                                 x-transition.origin.top.left
                                 @click.outside="close($refs.button)"
                                 :id="$id('dropdown-button')"
                                 x-cloak
                                 class="dropdown-panel relative"
                                 style="left: -50%" {{-- Center a dropdown panel --}}
                            >

                                <button @click.prevent="
                                            openModal = true; {{-- Open the modal --}}
                                            item = index; {{-- Identify the item we are corrently editing --}}
                                            {{-- Replace the previous condition with the current, don't assign object directly (modalCondition = condition) to avoid reactiveness --}}
{{--                                            modalCondition = new Condition(condition);--}}
                                            modalCondition = new Condition(condition);
                                            newCondition = false; {{-- This condition is already created --}}
                                        "
                                        class="dropdown-button"
                                >
                                    {{ __('forms.edit') }}
                                </button>

                                <button @click.prevent="conditions.splice(index, 1); close($refs.button)"
                                        class="dropdown-button dropdown-delete"
                                >
                                    {{ __('forms.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </template>
        </tbody>
    </table>

    <div>
        {{-- Button to trigger the modal --}}
        <button @click.prevent="
                        openModal = true; {{-- Open the Modal --}}
                        newCondition = true; {{-- We are adding a new condition --}}
                        modalCondition = new Condition(); {{-- Replace the data of the previous condition with a new one--}}
                    "
                class="item-add my-5"
        >
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                 viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
            </svg>
            {{ __('forms.add') }}
        </button>

        {{-- Modal --}}
        <template x-teleport="body"> {{-- This moves the modal at the end of the body tag --}}
            <div x-show="openModal"
                 style="display: none"
                 @keydown.escape.prevent.stop="openModal = false"
                 role="dialog"
                 aria-modal="true"
                 x-id="['modal-title']"
                 :aria-labelledby="$id('modal-title')" {{-- This associates the modal with unique ID --}}
                 class="modal"
            >

                {{-- Overlay --}}
                <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-black/25"></div>

                {{-- Panel --}}
                <div x-show="openModal"
                     x-transition
                     @click="openModal = false"
                     class="relative flex min-h-screen items-center justify-center p-4"
                >
                    <div @click.stop
                         x-trap.noscroll.inert="openModal"
                         class="modal-content h-fit w-full lg:max-w-7xl"
                    >
                        {{-- Title --}}
                        <h3 class="modal-header" :id="$id('modal-title')">{{ __('patients.diagnoses') }}</h3>

                        {{-- Content --}}
                        <form>
                            <div class="form-row-modal">
                                <div>
                                    <label for="conditionReasonCode" class="label-modal">
                                        {{ __('patients.icpc-2_status_code') }}
                                    </label>
                                    <select x-model="modalCondition.conditions.code.coding[0].code"
                                            id="conditionReasonCode"
                                            class="input-modal"
                                            type="text"
                                            required
                                    >
                                        <option selected>{{ __('forms.select') }}</option>
                                        @foreach($conditionCodes as $key => $conditionCode)
                                            <option value="{{ $key }}" wire:key="{{ $key }}">
                                                {{ $key }} - {{ $conditionCode }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- Check if the picked value is the one from the dictionary --}}
                                    <p class="text-error text-xs"
                                       x-show="!Object.keys(conditionCodesDictionary).includes(modalCondition.conditions.code.coding[0].code)"
                                    >
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>

                                <div x-data="{
                                        selected: null,
                                        results: $wire.entangle('results'),
                                        showResults: false
                                    }"
                                     class="relative"
                                >
                                    <label for="reasonCode" class="label-modal">
                                        {{ __('patients.icd-10') }}
                                    </label>
                                    <input type="text"
                                           @input.debounce.500ms="
                                               let value = $event.target.value;
                                               let isEnglish = /^[a-zA-Z]+$/.test(value);

                                               if ((isEnglish && value.length >= 1) || (!isEnglish && value.length >= 3)) {
                                                   $wire.searchICD10(value);
                                                   showResults = true;
                                               }
                                           "
                                           @focus="if (modalCondition.query.length >= ( /^[a-zA-Z]+$/.test(modalCondition.query) ? 1 : 3 )) showResults = true"
                                           @click.away="showResults = false"
                                           x-model="modalCondition.query"
                                           id="icd10Code"
                                           class="input-modal"
                                           placeholder="{{ __('forms.select') }}"
                                           autocomplete="off"
                                    />

                                    <div x-show="showResults && results.length > 0"
                                         class="absolute left-0 top-full z-10 max-h-80 w-full overflow-auto overscroll-contain rounded-lg border border-gray-200 bg-white p-1.5 shadow-lg"
                                    >
                                        <ul>
                                            <template x-for="(result, index) in results" :key="index">
                                                <li class="group flex w-full cursor-pointer items-center rounded-md px-2 py-1.5 transition-colors"
                                                    @click="selected = result; modalCondition.query = result.code + ' - ' + result.description; modalCondition.conditions.code.coding[1].code = result.code; showResults = false"
                                                >
                                                    <span x-text="result.code + ' - ' + result.description"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>

                                    <p x-show="showResults && results.length == 0"
                                       class="px-2 py-1.5 text-gray-600">
                                        {{ __('forms.nothing_found') }}
                                    </p>
                                </div>

                                <div>
                                    <label for="diagnoseCode" class="label-modal">
                                        {{ __('forms.type') }}
                                    </label>
                                    <select x-model="modalCondition.conditions.diagnoses.role.coding[0].code"
                                            id="diagnoseCode"
                                            class="input-modal"
                                            type="text"
                                            required
                                    >
                                        <option selected>{{ __('forms.select') }}</option>
                                        @foreach($this->dictionaries['eHealth/diagnosis_roles'] as $key => $diagnosisRole)
                                            <option value="{{ $key }}" wire:key="{{ $key }}">
                                                {{ $diagnosisRole }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <p class="text-error text-xs"
                                       x-show="!Object.keys(diagnosisRolesDictionary).includes(modalCondition.conditions.diagnoses.role.coding[0].code)"
                                    >
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>

                                <div>
                                    <label for="rank" class="label-modal">
                                        {{ __('patients.priority') }}
                                    </label>
                                    <select x-model.number="modalCondition.conditions.diagnoses.rank"
                                            id="rank"
                                            class="input-modal"
                                            type="text"
                                            required
                                    >
                                        <option selected>{{ __('forms.select') }}</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div>
                                    <label for="clinicalStatus" class="label-modal">
                                        {{ __('patients.clinical_status') }}
                                    </label>
                                    <select x-model="modalCondition.conditions.clinicalStatus"
                                            id="clinicalStatus"
                                            class="input-modal"
                                            type="text"
                                            required
                                    >
                                        <option selected>{{ __('forms.select') }}</option>
                                        @foreach($this->dictionaries['eHealth/condition_clinical_statuses'] as $key => $clinicalStatus)
                                            <option value="{{ $key }}" wire:key="{{ $key }}">
                                                {{ $clinicalStatus }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <p class="text-error text-xs"
                                       x-show="!Object.keys(conditionClinicalStatusesRolesDictionary).includes(modalCondition.conditions.clinicalStatus)"
                                    >
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>

                                <div>
                                    <label for="verificationStatus" class="label-modal">
                                        {{ __('patients.verification_status') }}
                                    </label>
                                    <select x-model="modalCondition.conditions.verificationStatus"
                                            id="verificationStatus"
                                            class="input-modal"
                                            type="text"
                                            required
                                    >
                                        <option selected>{{ __('forms.select') }}</option>
                                        @foreach($this->dictionaries['eHealth/condition_verification_statuses'] as $key => $verificationStatus)
                                            <option value="{{ $key }}" wire:key="{{ $key }}">
                                                {{ $verificationStatus }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <p class="text-error text-xs"
                                       x-show="!Object.keys(conditionVerificationStatusesDictionary).includes(modalCondition.conditions.verificationStatus)"
                                    >
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>

                                <div class="relative">
                                    <svg width="20" height="20"
                                         class="svg-input absolute left-1 !top-2/3 transform -translate-y-1/2 pointer-events-none"
                                    >
                                        <use xlink:href="#svg-calendar-week"></use>
                                    </svg>
                                    <label for="onsetDate" class="label-modal">
                                        {{ __('forms.start_date') }}
                                    </label>
                                    <input x-model="modalCondition.conditions.onsetDate"
                                           datepicker-max-date="{{ now()->format('Y-m-d') }}"
                                           type="text"
                                           name="onsetDate"
                                           id="onsetDate"
                                           class="datepicker-input input-modal"
                                           autocomplete="off"
                                           required
                                    >

                                    <p class="text-error text-xs"
                                       x-show="modalCondition.conditions.onsetDate.trim() === ''"
                                    >
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>

                                <div class="relative" onclick="document.getElementById('onsetTime').showPicker()">
                                    <svg width="20" height="20"
                                         class="svg-input absolute right-3 !top-2/3 transform -translate-y-1/2 pointer-events-none"
                                    >
                                        <use xlink:href="#svg-clock"></use>
                                    </svg>
                                    <label for="onsetTime" class="label-modal">
                                        {{ __('forms.start_time') }}
                                    </label>
                                    <input x-model="modalCondition.conditions.onsetTime"
                                           datepicker-max-date="{{ now()->format('Y-m-d') }}"
                                           type="time"
                                           name="onsetTime"
                                           id="onsetTime"
                                           class="input-modal"
                                           autocomplete="off"
                                           required
                                    >

                                    <p class="text-error text-xs"
                                       x-show="modalCondition.conditions.onsetTime.trim() === ''"
                                    >
                                        {{ __('forms.field_empty') }}
                                    </p>
                                </div>

                                <div class="relative">
                                    <svg width="20" height="20"
                                         class="svg-input absolute left-1 !top-2/3 transform -translate-y-1/2 pointer-events-none"
                                    >
                                        <use xlink:href="#svg-calendar-week"></use>
                                    </svg>
                                    <label for="assertedDate" class="label-modal">
                                        {{ __('patients.entry_date') }}
                                    </label>
                                    <input x-model="modalCondition.conditions.assertedDate"
                                           datepicker-max-date="{{ now()->format('Y-m-d') }}"
                                           type="text"
                                           name="assertedDate"
                                           id="assertedDate"
                                           class="datepicker-input input-modal"
                                           autocomplete="off"
                                           required
                                    >
                                </div>

                                <div class="relative" onclick="document.getElementById('assertedTime').showPicker()">
                                    <svg width="20" height="20"
                                         class="svg-input absolute right-3 !top-2/3 transform -translate-y-1/2 pointer-events-none"
                                    >
                                        <use xlink:href="#svg-clock"></use>
                                    </svg>
                                    <label for="assertedTime" class="label-modal">
                                        {{ __('patients.entry_time') }}
                                    </label>
                                    <input x-model="modalCondition.conditions.assertedTime"
                                           datepicker-max-date="{{ now()->format('Y-m-d') }}"
                                           type="time"
                                           name="assertedTime"
                                           id="assertedTime"
                                           class="input-modal"
                                           autocomplete="off"
                                           required
                                    >
                                </div>

                                <div>
                                    <label for="severityCondition" class="label-modal">
                                        {{ __('patients.severity_of_the_condition') }}
                                    </label>
                                    <select x-model="modalCondition.conditions.severity.coding[0].code"
                                            id="severityCondition"
                                            class="input-modal"
                                            type="text"
                                            required
                                    >
                                        <option selected>{{ __('forms.select') }}</option>
                                        @foreach($this->dictionaries['eHealth/condition_severities'] as $key => $conditionSeverity)
                                            <option value="{{ $key }}" wire:key="{{ $key }}">
                                                {{ $conditionSeverity }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div x-data="{ primarySource: 'performer' }" class="mt-12">
                                <div class="flex gap-20 md:mb-5 mb-4">
                                    <h2 class="default-p">{{ __('patients.primary_source') }}</h2>
                                    <div class="flex items-center">
                                        <input @change="primarySource = 'performer'"
                                               x-model.boolean="modalCondition.conditions.primarySource"
                                               id="performer"
                                               type="radio"
                                               value="true"
                                               name="primarySource"
                                               class="default-radio"
                                               :checked="primarySource === 'performer'"
                                        >
                                        <label for="performer"
                                               class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                                        >
                                            {{ __('patients.performer') }}
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input @change="primarySource = 'otherSource'"
                                               x-model.boolean="modalCondition.conditions.primarySource"
                                               id="otherSource"
                                               type="radio"
                                               value="false"
                                               name="primarySource"
                                               class="default-radio"
                                               :checked="primarySource === 'otherSource'"
                                        >
                                        <label for="otherSource"
                                               class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                                        >
                                            {{ __('patients.other_source') }}
                                        </label>
                                    </div>
                                </div>

                                <div x-show="primarySource === 'performer'">
                                    <div class="form-row-modal">
                                        <div class="form-group group">
                                                <textarea x-model="modalCondition.conditions.asserter.identifier.type.text"
                                                          id="doctorComment"
                                                          name="doctorComment"
                                                          class="textarea"
                                                          rows="4"
                                                          placeholder="{{ __('patients.write_comment_here') }}"
                                                ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="primarySource === 'otherSource'">
                                    <div class="form-row-modal !mb-12">
                                        <div>
                                            <label for="reportOrigin" class="label-modal">
                                                {{ __('patients.information_source') }}
                                            </label>
                                            <select x-model="modalCondition.conditions.reportOrigin.coding[0].code"
                                                    id="reportOrigin"
                                                    class="input-modal"
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
                                        </div>
                                    </div>

                                    @include('livewire.encounter._parts._evidences')
                                </div>
                            </div>

                            <div class="mt-6 flex justify-between space-x-2">
                                <button type="button"
                                        @click="openModal = false"
                                        class="button-minor"
                                >
                                    {{ __('forms.cancel') }}
                                </button>

                                <button @click.prevent="
                                            delete modalCondition.query;
                                            modalCondition.conditions.code.coding = modalCondition.conditions.code.coding.filter(c => c.code.trim() !== '');

                                            if(newCondition !== false) {
                                                diagnoses.push(modalCondition.conditions.diagnoses);
                                                conditions.push(modalCondition.conditions);
                                            } else {
                                                conditions[item] = modalCondition.conditions;
                                            }

                                            openModal = false;
                                        "
                                        class="button-primary"
                                        :disabled="!(modalCondition.conditions.clinicalStatus.trim().length > 0 && modalCondition.conditions.verificationStatus.trim().length > 0
                                            && modalCondition.conditions.code.coding[0].code.trim().length > 0 && modalCondition.conditions.diagnoses.role.coding[0].code
                                        )"
                                >
                                    {{ __('forms.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
</fieldset>

<script>
    /**
     * Representation of the user's personal conditions
     */
    class Condition {
        conditions = {
            primarySource: true,
            asserter: {
                identifier: {
                    type: {
                        coding: [
                            { system: 'eHealth/resources', code: 'employee' }
                        ],
                        text: ''
                    }
                }
            },
            reportOrigin: {
                coding: [
                    { system: 'eHealth/report_origins', code: '' }
                ]
            },
            code: {
                coding: [
                    { system: 'eHealth/ICPC2/condition_codes', code: '' },
                    { system: 'eHealth/ICD10_AM/condition_codes', code: '' }
                ]
            },
            clinicalStatus: '',
            verificationStatus: '',
            onsetDate: new Date().toISOString().split('T')[0],
            onsetTime: new Date().toLocaleTimeString('uk-UA', { hour: '2-digit', minute: '2-digit', hour12: false }),
            assertedDate: new Date().toISOString().split('T')[0],
            assertedTime: new Date().toLocaleTimeString('uk-UA', { hour: '2-digit', minute: '2-digit', hour12: false }),
            severity: {
                coding: [
                    { system: 'eHealth/condition_severities', code: '' }
                ]
            },
            diagnoses: {
                condition: {
                    identifier: {
                        type: {
                            coding: [{ system: 'eHealth/resources', code: 'condition' }]
                        }
                    }
                },
                role: {
                    coding: [{ system: 'eHealth/diagnosis_roles', code: '' }]
                },
                rank: ''
            }
        };
        query = '';

        constructor(obj = null) {
            if (obj) {
                this.conditions = JSON.parse(JSON.stringify(obj.conditions || obj));
            }
        }
    }
</script>
