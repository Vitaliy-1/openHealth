<fieldset class="fieldset">
    <legend class="legend">
        {{ __('forms.additional_info') }}
    </legend>

    {{-- Information source (doctor or patient) --}}
    <div class="flex gap-20 mb-8">
        <h2 class="default-p font-bold">{{ __('patients.information_source') }}</h2>
        {{-- Doctor --}}
        <div class="flex items-center">
            <input x-model.boolean="modalDiagnosticReport.primarySource"
                   id="performer"
                   type="radio"
                   value="true"
                   name="primarySource"
                   class="default-radio"
                   :checked="modalDiagnosticReport.primarySource === true"
            >
            <label for="performer" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ __('patients.performer') }}
            </label>
        </div>

        {{-- Patient --}}
        <div class="flex items-center">
            <input x-model.boolean="modalDiagnosticReport.primarySource"
                   id="patient"
                   type="radio"
                   value="false"
                   name="primarySource"
                   class="default-radio"
                   :checked="modalDiagnosticReport.primarySource === false"
            >
            <label for="patient" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ __('forms.patient') }}
            </label>
        </div>
    </div>

    {{-- When patient selected --}}
    <div x-show="modalDiagnosticReport.primarySource === false" x-transition>
        <div class="form-row-modal">
            <div>
                <label for="reportOrigin" class="label-modal">
                    {{ __('patients.source_link') }}
                </label>
                <select class="input-modal"
                        x-model="modalDiagnosticReport.reportOrigin.coding[0].code"
                        id="reportOrigin"
                        type="text"
                        required
                >
                    <option value="" selected>{{ __('forms.select') }}</option>
                    @foreach($this->dictionaries['eHealth/report_origins'] as $key => $reportOrigin)
                        <option value="{{ $key }}" wire:key="{{ $key }}">
                            {{ $reportOrigin }}
                        </option>
                    @endforeach
                </select>

                <p class="text-error text-xs"
                   x-show="!Object.keys($wire.dictionaries['eHealth/report_origins']).includes(modalDiagnosticReport.reportOrigin.coding[0].code)"
                >
                    {{ __('forms.field_empty') }}
                </p>
            </div>
        </div>
    </div>

    <div class="form-row-modal">
        <div>
            <label for="resultsInterpreterText" class="label-modal">
                {{ __('patients.the_doctor_who_interpreted_the_results') }}
            </label>
            <input x-model="modalDiagnosticReport.resultsInterpreter.text"
                   type="text"
                   name="resultsInterpreterText"
                   id="resultsInterpreterText"
                   class="input-modal"
                   placeholder="{{ __('patients.full_name_of_the_doctor_who_interpreted_the_results') }}"
                   autocomplete="off"
            >

            <p class="text-error text-xs" x-show="modalDiagnosticReport.resultsInterpreter.text.trim() === ''">
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>

    {{-- Recorded by --}}
    <div class="form-row-modal">
        <div>
            <label for="recordedBy" class="label-modal">
                {{ __('patients.doctor_submitting_a_report_to_the_system') }}
            </label>
            <input type="text"
                   name="recordedBy"
                   id="recordedBy"
                   class="input-modal"
                   autocomplete="off"
                   disabled
                   value="{{ $employeeFullName }}"
            >
        </div>
    </div>

    {{-- Issued datetime --}}
    <div class="form-row-3">
        <div>
            <label for="issuedDate" class="label-modal">
                {{ __('patients.date_and_time_of_entry') }}
            </label>
            <div class="relative flex items-center">
                <svg width="20" height="20" class="svg-input absolute left-2.5 pointer-events-none">
                    <use xlink:href="#svg-calendar-week"></use>
                </svg>
                <input x-model="modalDiagnosticReport.issuedDate"
                       datepicker-max-date="{{ now()->format('Y-m-d') }}"
                       type="text"
                       name="issuedDate"
                       id="issuedDate"
                       class="datepicker-input input-modal !pl-10"
                       autocomplete="off"
                       required
                >
            </div>

            <p class="text-error text-xs" x-show="modalDiagnosticReport.issuedDate.trim() === ''">
                {{ __('forms.field_empty') }}
            </p>
        </div>

        <div class="w-1/2" onclick="document.getElementById('issuedTime').showPicker()">
            <label for="issuedTime" class="hidden">
                {{ __('patients.time') }}
            </label>

            <div class="relative flex items-center mt-7">
                <svg width="20" height="20" class="svg-input absolute left-2.5 pointer-events-none">
                    <use xlink:href="#svg-clock"></use>
                </svg>
                <input x-model="modalDiagnosticReport.issuedTime"
                       @input="$event.target.blur()"
                       datepicker-max-date="{{ now()->format('Y-m-d') }}"
                       type="time"
                       name="issuedTime"
                       id="issuedTime"
                       class="input-modal !pl-10"
                       autocomplete="off"
                       required
                >
            </div>

            <p class="text-error text-xs" x-show="modalDiagnosticReport.issuedTime.trim() === ''">
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>

    {{-- Start effective period datetime --}}
    <div class="form-row-3">
        <div>
            <label for="effectivePeriodStartDate" class="label-modal">
                {{ __('patients.reception_start_date_and_time') }}
            </label>
            <div class="relative flex items-center">
                <svg width="20" height="20" class="svg-input absolute left-2.5 pointer-events-none">
                    <use xlink:href="#svg-calendar-week"></use>
                </svg>
                <input x-model="modalDiagnosticReport.effectivePeriodStartDate"
                       datepicker-max-date="{{ now()->format('Y-m-d') }}"
                       type="text"
                       name="effectivePeriodStartDate"
                       id="effectivePeriodStartDate"
                       class="datepicker-input input-modal !pl-10"
                       autocomplete="off"
                       required
                >
            </div>

            <p class="text-error text-xs" x-show="modalDiagnosticReport.effectivePeriodStartDate.trim() === ''">
                {{ __('forms.field_empty') }}
            </p>
        </div>

        <div class="w-1/2" onclick="document.getElementById('effectivePeriodStartTime').showPicker()">
            <label for="effectivePeriodStartTime" class="hidden">
                {{ __('patients.time') }}
            </label>

            <div class="relative flex items-center mt-7">
                <svg width="20" height="20" class="svg-input absolute left-2.5 pointer-events-none">
                    <use xlink:href="#svg-clock"></use>
                </svg>
                <input x-model="modalDiagnosticReport.effectivePeriodStartTime"
                       @input="$event.target.blur()"
                       datepicker-max-date="{{ now()->format('Y-m-d') }}"
                       type="time"
                       name="effectivePeriodStartTime"
                       id="effectivePeriodStartTime"
                       class="input-modal !pl-10"
                       autocomplete="off"
                       required
                >
            </div>

            <p class="text-error text-xs" x-show="modalDiagnosticReport.effectivePeriodStartTime.trim() === ''">
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>

    {{-- End effective period datetime --}}
    <div class="form-row-3">
        <div>
            <label for="effectivePeriodEndDate" class="label-modal">
                {{ __('patients.reception_end_date_and_time') }}
            </label>
            <div class="relative flex items-center">
                <svg width="20" height="20" class="svg-input absolute left-2.5 pointer-events-none">
                    <use xlink:href="#svg-calendar-week"></use>
                </svg>
                <input x-model="modalDiagnosticReport.effectivePeriodEndDate"
                       datepicker-max-date="{{ now()->format('Y-m-d') }}"
                       type="text"
                       name="effectivePeriodEndDate"
                       id="effectivePeriodEndDate"
                       class="datepicker-input input-modal !pl-10"
                       autocomplete="off"
                       required
                >
            </div>

            <p class="text-error text-xs" x-show="modalDiagnosticReport.effectivePeriodEndDate.trim() === ''">
                {{ __('forms.field_empty') }}
            </p>
        </div>

        <div class="w-1/2" onclick="document.getElementById('effectivePeriodEndTime').showPicker()">
            <label for="effectivePeriodEndTime" class="hidden">
                {{ __('patients.time') }}
            </label>

            <div class="relative flex items-center mt-7">
                <svg width="20" height="20" class="svg-input absolute left-2.5 pointer-events-none">
                    <use xlink:href="#svg-clock"></use>
                </svg>
                <input x-model="modalDiagnosticReport.effectivePeriodEndTime"
                       @input="$event.target.blur()"
                       datepicker-max-date="{{ now()->format('Y-m-d') }}"
                       type="time"
                       name="effectivePeriodEndTime"
                       id="effectivePeriodEndTime"
                       class="input-modal !pl-10"
                       autocomplete="off"
                       required
                >
            </div>

            <p class="text-error text-xs" x-show="modalDiagnosticReport.effectivePeriodEndTime.trim() === ''">
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>
</fieldset>
