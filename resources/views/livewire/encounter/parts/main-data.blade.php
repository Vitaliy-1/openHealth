<fieldset class="fieldset" id="patient-data-section">
    <legend class="legend">
        {{ __('patients.main_data') }}
    </legend>

    <div>
        <div x-data="{ isReferralAvailable: false }">
            <div class="form-row-3">
                <div class="form-group group">
                    <input @click="isReferralAvailable = !isReferralAvailable"
                           type="checkbox"
                           name="isReferralAvailable"
                           id="isReferralAvailable"
                           class="default-checkbox mb-1"
                    />
                    <label class="default-p" for="isReferralAvailable">
                        {{ __('patients.referral_available') }}
                    </label>
                </div>

                <div x-show="isReferralAvailable" class="form-group group" x-cloak>
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

                <div x-show="isReferralAvailable" class="form-group group" x-cloak>
                    <button wire:click.prevent="searchForReferralNumber"
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
                <select wire:model="form.encounter.class.code"
                        id="interactionClass"
                        class="input-select peer @error('form.encounter.class.code') input-error @enderror"
                        required
                >
                    <option selected>{{ __('forms.select') }} {{ mb_strtolower(__('patients.interaction_class')) }}*
                    </option>
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
                <select wire:model="form.encounter.type.coding.0.code"
                        id="interactionType"
                        class="input-select peer @error('form.encounter.type.coding.code') input-error @enderror"
                        required
                >
                    <option selected>{{ __('forms.select') }} {{ mb_strtolower(__('patients.interaction_type'))  }}*
                    </option>
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

        <div x-data="{ episodeType: 'new' }" class="mt-8">
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
                        <select wire:model="form.episode.type.code"
                                id="episodeType"
                                class="input-select peer @error('form.episode.type.code') input-error @enderror"
                                required
                        >
                            <option selected>{{ __('forms.select') }} {{ mb_strtolower(__('patients.episode_type')) }} *</option>
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
