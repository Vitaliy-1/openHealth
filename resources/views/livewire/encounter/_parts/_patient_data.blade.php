<fieldset class="fieldset" id="patient-data-section">
    <legend class="legend">
        {{ __('patients.basic_data') }}
    </legend>

    <div>
        <div class="form-row-3">
            <div class="form-group group">
                <input type="text"
                       name="patientFullName"
                       id="patientFullName"
                       class="input peer"
                       placeholder=" "
                       autocomplete="off"
                       disabled
                       value="{{ $lastName }} {{ $firstName }} {{ $secondName }}"
                />
                <label for="patientFullName" class="label">
                    {{ __('patients.patient_full_name') }}
                </label>
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group group">
                <input wire:model="performerFullName"
                       type="text"
                       name="employeeFullName"
                       id="employeeFullName"
                       class="input peer"
                       placeholder=" "
                       autocomplete="off"
                       disabled
                />
                <label for="employeeFullName" class="label">
                    {{ __('patients.employee_full_name') }}
                </label>
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group group">
                <select wire:model="form.encounter.division.identifier.value"
                        id="divisionNames"
                        class="input-select peer @error('form.encounter.division.identifier.value') input-error @enderror"
                >
                    <option selected>{{ __('patients.division_name') }}</option>
                    @foreach($divisions as $key => $division)
                        <option value="{{ $division['uuid'] }}" wire:key="{{ $key }}">{{ $division['name'] }}</option>
                    @endforeach
                </select>

                @error('form.encounter.division.identifier.value')
                <p class="text-error">
                    {{ $message }}
                </p>
                @enderror
            </div>
        </div>

        <div x-data="{ isReferralAvailable: false }">
            <div class="form-group group">
                <input @click="isReferralAvailable = !isReferralAvailable"
                       type="checkbox"
                       name="isReferralAvailable"
                       id="isReferralAvailable"
                       class="default-checkbox mb-1"
                />
                <label for="isReferralAvailable">
                    {{ __('patients.referral_available') }}
                </label>
            </div>

            <div class="form-row-3">
                <div class="form-group group" x-show="isReferralAvailable" x-cloak>
                    <input wire:model="form.referralNumber"
                           type="text"
                           name="requisitionNumber"
                           id="requisitionNumber"
                           class="input peer @error('form.referralNumber') input-error @enderror"
                           placeholder=" "
                           required
                           autocomplete="off"
                    />
                    <label for="requisitionNumber" class="label">
                        {{ __('patients.referral_number') }}
                    </label>

                    @error('form.referralNumber')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="form-group group" x-show="isReferralAvailable" x-cloak>
                    <button wire:click.prevent="searchForReferralNumber()"
                            class="flex items-center gap-2 default-button"
                    >
                        <svg width="16" height="16">
                            <use xlink:href="#svg-search"></use>
                        </svg>
                        <span>{{ __('patients.search_for_referral') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group group">
                <label for="interactionClass" class="sr-only">
                    {{ __('forms.select') }} {{ __('patients.interaction_class') }}
                </label>
                <select wire:model="form.encounter.class.code"
                        id="interactionClass"
                        class="input-select peer @error('form.encounter.class.code') input-error @enderror"
                        required
                >
                    <option selected>{{ __('patients.interaction_class') }} *</option>
                    @foreach($this->dictionaries['eHealth/encounter_classes'] as $key => $encounterClass)
                        <option value="{{ $key }}" wire:key="{{ $key }}">{{ $encounterClass }}</option>
                    @endforeach
                </select>

                @error('form.encounter.class.code')
                <p class="text-error">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="form-group group">
                <label for="interactionType" class="sr-only">
                    {{ __('forms.select') }} {{ __('patients.interaction_type') }}
                </label>
                <select wire:model="form.encounter.type.coding.0.code"
                        id="interactionType"
                        class="input-select peer @error('form.encounter.type.coding.code') input-error @enderror"
                        required
                >
                    <option selected>{{ __('patients.interaction_type') }} *</option>
                    @foreach($this->dictionaries['eHealth/encounter_types'] as $key => $encounterType)
                        <option value="{{ $key }}" wire:key="{{ $key }}">{{ $encounterType }}</option>
                    @endforeach
                </select>

                @error('form.encounter.type.coding.code')
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
                <input wire:model="form.encounter.period.date"
                       datepicker-max-date="{{ now()->format('Y-m-d') }}"
                       datepicker-autoselect-today
                       type="text"
                       name="date"
                       id="date"
                       class="datepicker-input input peer @error('form.encounter.period.date') input-error @enderror"
                       placeholder=" "
                       required
                >
                <label for="date" class="label">
                    {{ __('patients.data') }}
                </label>

                @error('form.encounter.period.date')
                <p class="text-error">
                    {{ $message }}
                </p>
                @enderror
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group group" onclick="document.getElementById('periodStart').showPicker()">
                <svg class="svg-input right-1" width="16" height="16">
                    <use xlink:href="#svg-clock"></use>
                </svg>
                <input wire:model="form.encounter.period.start"
                       type="time"
                       name="periodStart"
                       id="periodStart"
                       class="input peer @error('form.encounter.period.start') input-error @enderror"
                       placeholder=" "
                       required
                />
                <label for="periodStart" class="label">
                    {{ __('patients.period_start') }}
                </label>

                @error('form.encounter.period.start')
                <p class="text-error">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="form-group group" onclick="document.getElementById('periodEnd').showPicker()">
                <svg class="svg-input right-1" width="16" height="16">
                    <use xlink:href="#svg-clock"></use>
                </svg>
                <input wire:model="form.encounter.period.end"
                       type="time"
                       name="periodEnd"
                       id="periodEnd"
                       class="input peer @error('form.encounter.period.end') input-error @enderror"
                       placeholder=" "
                       required
                />
                <label for="periodStart" class="label">
                    {{ __('patients.period_end') }}
                </label>

                @error('form.encounter.period.end')
                <p class="text-error">
                    {{ $message }}
                </p>
                @enderror
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group group">
                <label for="priority" class="sr-only">
                    {{ __('forms.select') }} {{ __('patients.priority') }}
                </label>
                <select wire:model="form.encounter.priority.coding.0.code"
                        id="priority"
                        class="input-select peer @error('form.encounter.priority.coding.code') input-error @enderror"
                        required
                >
                    <option selected>{{ __('forms.select') }} {{ __('patients.priority') }}</option>
                    @foreach($this->dictionaries['eHealth/encounter_priority'] as $key => $encounterPriority)
                        <option value="{{ $key }}" wire:key="{{ $key }}">{{ $encounterPriority }}</option>
                    @endforeach
                </select>

                @error('form.encounter.priority.coding.code')
                <p class="text-error">
                    {{ $message }}
                </p>
                @enderror
            </div>
        </div>

        <div x-data="{ episodeType: 'new' }">
            <div class="form-row-3">
                <div class="flex items-center">
                    <input @change="episodeType = 'existing'"
                           id="existingEpisode"
                           type="radio"
                           value="existing"
                           name="episode"
                           class="default-radio"
                           :checked="episodeType === 'existing'"
                    >
                    <label for="existingEpisode" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        {{ __('patients.existing_episode') }}
                    </label>
                </div>
                <div class="flex items-center">
                    <input @change="episodeType = 'new'"
                           id="newEpisode"
                           type="radio"
                           value="new"
                           name="episode"
                           class="default-radio"
                           :checked="episodeType === 'new'"
                    >
                    <label for="newEpisode" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        {{ __('patients.new_episode') }}
                    </label>
                </div>
            </div>

            <div x-show="episodeType === 'new'" x-transition>
                <div class="form-row-3">
                    <div class="form-group group">
                        <input wire:model="form.episode.name"
                               type="text"
                               name="episodeName"
                               id="episodeName"
                               class="input peer @error('form.episode.name') input-error @enderror"
                               placeholder=" "
                               required
                               autocomplete="off"
                        />
                        <label for="episodeName" class="label">
                            {{ __('patients.episode_name') }}
                        </label>

                        @error('form.episode.name')
                        <p class="text-error">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="form-group group">
                        <label for="episodeType" class="sr-only">
                            {{ __('forms.select') }} {{ __('patients.episode_type') }}
                        </label>
                        <select wire:model="form.episode.type.code"
                                id="episodeType"
                                class="input-select peer @error('form.episode.type.code') input-error @enderror"
                                required
                        >
                            <option selected>{{ __('patients.episode_type') }} *</option>
                            @foreach($this->dictionaries['eHealth/episode_types'] as $key => $episodeType)
                                <option value="{{ $key }}" wire:key="{{ $key }}">{{ $episodeType }}</option>
                            @endforeach
                        </select>

                        @error('form.episode.type.code')
                        <p class="text-error">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-row-3" x-show="episodeType === 'existing'" x-transition>
                <div class="form-group group">
                    <input wire:model="form.encounter.episode.identifier.value"
                           type="text"
                           name="selectEpisode"
                           id="selectEpisode"
                           class="input peer @error('form.encounter.episode.identifier.value') input-error @enderror"
                           placeholder=" "
                           required
                           autocomplete="off"
                    />
                    <label for="selectEpisode" class="label">
                        {{ __('patients.episode_number') }}
                    </label>

                    @error('form.encounter.episode.identifier.value')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="form-group group" x-show="isReferralAvailable" x-cloak>
                    <button wire:click.prevent="searchForEpisode()"
                            class="flex items-center gap-2 default-button"
                    >
                        <svg width="16" height="16">
                            <use xlink:href="#svg-search"></use>
                        </svg>
                        <span>{{ __('Шукати епізод') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</fieldset>
