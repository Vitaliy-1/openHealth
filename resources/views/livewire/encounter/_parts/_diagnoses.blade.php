@php
    $conditionCodes = $this->dictionaries['eHealth/ICPC2/condition_codes'];
    ksort($conditionCodes);
@endphp
<fieldset class="fieldset" id="diagnoses-section">
    <legend class="legend">
        {{ __('patients.diagnoses') }}
    </legend>

    @if(count($allEpisodes) > 0)
        @include('livewire.encounter._parts._diagnoses_table')
    @endif
    <div x-data="{ showDiagnose: $wire.entangle('showDiagnose') }">
        <button @click.prevent="showDiagnose = !showDiagnose" class="default-button my-4">
            {{ __('patients.add_diagnose') }}
        </button>

        <div x-show="showDiagnose">
            <div class="form-row-3">
                <div class="form-group group">
                    <label for="reasonCode" class="sr-only">
                        {{ __('forms.select') }} {{ __('patients.icpc-2_status_code') }}
                    </label>
                    <select wire:model="form.conditions.code.coding.0.code"
                            id="reasonCode"
                            class="input-select peer @error('form.conditions.code.coding.*.code') input-error @enderror"
                            required
                    >
                        <option selected>{{ __('patients.icpc-2_status_code') }} *</option>
                        @foreach($conditionCodes as $key => $conditionCode)
                            <option value="{{ $key }}" wire:key="{{ $key }}">{{ $conditionCode }}</option>
                        @endforeach
                    </select>

                    @error('form.conditions.code.coding.*.code')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="form-group group"
                     x-data="{ query: '', selected: null, results: $wire.entangle('results') }"
                >
                    <input type="text"
                           x-on:input.debounce.500ms="if ($event.target.value.length >= 3) { $wire.handleInput($event.target.value) }"
                           x-model="query"
                           id="icd10Code"
                           class="input peer"
                           placeholder="{{ __('forms.select') }} {{ __('patients.icd-10') }}"
                           autocomplete="off"
                           wire:model="form.conditions.code.coding.1.code"
                    />

                    <div wire:loading.remove
                         x-show="query.length > 2 && results.length > 0"
                         class="right-0 z-10 mt-2 max-h-80 w-full overflow-y-scroll overscroll-contain rounded-lg border border-gray-200 bg-white p-1.5 shadow-sm outline-none"
                    >
                        <ul>
                            <template x-for="(result, index) in results" :key="index">
                                <li class="group flex w-full cursor-pointer items-center rounded-md px-2 py-1.5 transition-colors"
                                    @click="selected = result; query = result.code + ' - ' + result.description; $wire.set('form.conditions.code.coding.1.code', result.code); query = '';">
                                    <span x-text="result.code + ' - ' + result.description"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <p x-show="results.length == 0" class="px-2 py-1.5 text-gray-600">
                        {{ __('forms.nothing_found') }}
                    </p>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group group">
                    <input type="text"
                           name="searchDiagnose"
                           id="searchDiagnose"
                           class="input peer @error('form.searchDiagnose') input-error @enderror"
                           placeholder=" "
                           required
                           autocomplete="off"
                    />
                    <label for="searchDiagnose" class="label">
                        {{ __('') }}
                    </label>

                    @error('form.searchDiagnose')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="form-group group">
                    <button wire:click.prevent="searchForConditions"
                            class="flex items-center gap-2 default-button"
                    >
                        <svg width="16" height="16">
                            <use xlink:href="#svg-search"></use>
                        </svg>
                        <span>{{ __('Шукати попередні стани пацієнта') }}</span>
                    </button>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group group">
                    <select wire:model="form.encounter.diagnoses.role.coding.0.code"
                            id="diagnoseCode"
                            class="input-select peer @error('form.encounter.diagnoses.role.coding.*.code') input-error @enderror"
                            required
                    >
                        <option selected>{{ __('forms.type') }} *</option>
                        @foreach($this->dictionaries['eHealth/diagnosis_roles'] as $key => $diagnosisRole)
                            <option value="{{ $key }}" wire:key="{{ $key }}">{{ $diagnosisRole }}</option>
                        @endforeach
                    </select>

                    @error('form.encounter.diagnoses.role.coding.*.code')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                <div class="form-group group">
                    <select wire:model="form.encounter.diagnoses.rank"
                            id="diagnoseRank"
                            class="input-select peer @error('form.encounter.diagnoses.rank') input-error @enderror"
                            required
                    >
                        <option selected>{{ __('patients.priority') }}</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" wire:key="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>

                    @error('form.encounter.diagnoses.rank')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group group">
                    <select wire:model="form.conditions.clinical_status"
                            id="clinicalStatus"
                            class="input-select peer @error('form.conditions.clinical_status') input-error @enderror"
                            required
                    >
                        <option selected>{{ __('patients.clinical_status') }} *</option>
                        @foreach($this->dictionaries['eHealth/condition_clinical_statuses'] as $key => $clinicalStatus)
                            <option value="{{ $key }}" wire:key="{{ $key }}">{{ $clinicalStatus }}</option>
                        @endforeach
                    </select>

                    @error('form.conditions.clinical_status')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                <div class="form-group group">
                    <select wire:model="form.conditions.verification_status"
                            id="verificationStatus"
                            class="input-select peer @error('form.conditions.verification_status') input-error @enderror"
                            required
                    >
                        <option selected>{{ __('patients.verification_status') }} *</option>
                        @foreach($this->dictionaries['eHealth/condition_verification_statuses'] as $key => $verificationStatus)
                            <option value="{{ $key }}" wire:key="{{ $key }}">{{ $verificationStatus }}</option>
                        @endforeach
                    </select>

                    @error('form.conditions.verification_status')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group group">
                    <svg class="svg-input" width="20" height="20">
                        <use xlink:href="#svg-calendar-week"></use>
                    </svg>
                    <input wire:model="form.conditions.onsetDate"
                           datepicker-max-date="{{ now()->format('Y-m-d') }}"
                           datepicker-autoselect-today
                           type="text"
                           name="onsetDate"
                           id="onsetDate"
                           class="datepicker-input input peer @error('form.conditions.onsetDate') input-error @enderror"
                           placeholder=" "
                           required
                    >
                    <label for="date" class="label">
                        {{ __('forms.start_date') }}
                    </label>

                    @error('form.conditions.onsetDate')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="form-group group" onclick="document.getElementById('onsetTime').showPicker()">
                    <svg class="svg-input right-1" width="16" height="16">
                        <use xlink:href="#svg-clock"></use>
                    </svg>
                    <input wire:model="form.conditions.onsetTime"
                           type="time"
                           name="onsetTime"
                           id="onsetTime"
                           class="input peer @error('form.encounter.onsetTime') input-error @enderror"
                           placeholder=" "
                           required
                    />
                    <label for="onsetDate" class="label">
                        {{ __('Час початку') }}
                    </label>

                    @error('form.encounter.onsetTime')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group group">
                    <svg class="svg-input" width="20" height="20">
                        <use xlink:href="#svg-calendar-week"></use>
                    </svg>
                    <input wire:model="form.conditions.assertedDate"
                           datepicker-max-date="{{ now()->format('Y-m-d') }}"
                           datepicker-autoselect-today
                           type="text"
                           name="assertedDate"
                           id="assertedDate"
                           class="datepicker-input input peer @error('form.conditions.assertedDate') input-error @enderror"
                           placeholder=" "
                           required
                    >
                    <label for="date" class="label">
                        {{ __('Дата внесення') }}
                    </label>

                    @error('form.conditions.assertedDate')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="form-group group" onclick="document.getElementById('assertedTime').showPicker()">
                    <svg class="svg-input right-1" width="16" height="16">
                        <use xlink:href="#svg-clock"></use>
                    </svg>
                    <input wire:model="form.conditions.assertedTime"
                           type="time"
                           name="assertedTime"
                           id="assertedTime"
                           class="input peer @error('form.conditions.assertedTime') input-error @enderror"
                           placeholder=" "
                           required
                    />
                    <label for="periodStart" class="label">
                        {{ __('Час внесення') }}
                    </label>

                    @error('form.conditions.assertedTime')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group group">
                    <label for="severityCondition" class="sr-only">
                        {{ __('forms.select') }} {{ __('patients.severity_of_the_condition') }}
                    </label>
                    <select wire:model="form.conditions.severity.coding.0.code"
                            id="severityCondition"
                            class="input-select peer @error('form.diagnoses.condition.identifier.value') input-error @enderror"
                            required
                    >
                        <option selected>{{ __('patients.severity_of_the_condition') }}</option>
                        @foreach($this->dictionaries['eHealth/condition_severities'] as $key => $conditionSeverity)
                            <option value="{{ $key }}" wire:key="{{ $key }}">{{ $conditionSeverity }}</option>
                        @endforeach
                    </select>

                    @error('form.diagnoses.condition.identifier.value')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <button wire:click.prevent="createDiagnose" class="default-button my-4">
                {{ __('patients.create_diagnose') }}
            </button>

            <button wire:click.prevent="saveDiagnoseChange" class="default-button my-4">
                {{ __('patients.save_changes') }}
            </button>
        </div>
    </div>
</fieldset>
