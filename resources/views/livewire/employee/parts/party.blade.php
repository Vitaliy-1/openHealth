<fieldset class="fieldset"
          x-data="{ party: $wire.entangle('form.party') }"
          :disabled="$wire.isPersonalDataLocked">
    <legend class="legend">
        <h2>{{__('forms.personal_data')}}</h2>
    </legend>
    <div class="form">
        <div class="form-row-3">
            <div class="form-group">
                <input wire:model="form.party.lastName" type="text" name="lastName" id="lastName" class="peer input text-gray-500" placeholder=" " required
                       :disabled="$wire.isPersonalDataLocked || $wire.isPartyDataPartiallyLocked"/>
                <label for="lastName" class="label">{{__('forms.last_name')}}</label>
                @error('form.party.lastName') <p class="text-error">{{$message}}</p> @enderror
            </div>
            <div class="form-group">
                <input wire:model="form.party.firstName"  type="text" name="firstName" id="firstName" class="peer input text-gray-500" placeholder=" " required
                       :disabled="$wire.isPersonalDataLocked || $wire.isPartyDataPartiallyLocked"/>
                <label for="firstName" class="label">{{__('forms.first_name')}}</label>
                @error('form.party.firstName') <p class="text-error">{{$message}}</p> @enderror
            </div>
        </div>
        <div class="form-row-3">
            <div class="form-group">
                <input wire:model="form.party.secondName" type="text" name="secondName" id="secondName" class="peer input text-gray-500" placeholder=" "/>
                <label for="secondName" class="label">{{ __('forms.second_name') }}</label>
                @error('form.party.secondName') <p class="text-error">{{$message}}</p> @enderror
            </div>
            <div class="form-group">
                <select wire:model="form.party.gender" name="employeeGender" id="employeeGender" class="input-select @error('form.party.gender') input-error @enderror" required
                    @disabled($isPersonalDataLocked)>
                    <option value="" disabled selected hidden>{{ __('forms.select') }} {{ __('forms.gender') }}</option>
                    @foreach($this->dictionaries['GENDER'] as $k => $gender)
                        <option value="{{ $k }}">{{ $gender }}</option>
                    @endforeach
                </select>
                <label for="employeeGender" class="label">{{ __('forms.gender') }}</label>
                @error('form.party.gender')
                <p class="text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="form-row-3 items-start">
            <div class="form-group datepicker-wrapper relative w-full">
                <input wire:model="form.party.birthDate"
                       datepicker-format="{{ frontendDateFormat() }}"
                       type="text"
                       name="birthDate"
                       id="birthDate"
                       class="peer input pl-10 appearance-none datepicker-input text-gray-500 dark:text-gray-400"
                       placeholder=" "
                       required
                       :disabled="$wire.isPersonalDataLocked || $wire.isPartyDataPartiallyLocked"/>
                <label for="birthDate" class="wrapped-label">{{__('forms.birth_date')}}</label>
                @error('form.party.birthDate') <p class="text-error">{{$message}}</p> @enderror
            </div>

            {{-- Working Experience --}}
            <div class="form-group">
                <input
                    wire:model="form.party.workingExperience"
                    type="number"
                    id="workingExperience"
                    name="workingExperience"
                    {{-- EHealth rule: strictly greater than 0 --}}
                    min="1"
                    step="1"
                    placeholder=" "
                    class="peer input text-gray-500"
                />
                <label for="workingExperience" class="label">{{__('forms.working_experience')}}</label>
                <p class="text-xs text-blue-500 mt-1">{{ __('forms.workingExperience_hint') }}</p>
                @error('form.party.workingExperience') <p class="text-error">{{$message}}</p> @enderror
            </div>
        </div>

        {{-- Tax ID Section --}}
        <div class
             ="form-row-3"
             x-data="{
        noTaxId: $wire.entangle('form.party.noTaxId'),
        taxId: $wire.entangle('form.party.taxId'),
     }"
             x-init="
        if (noTaxId) { $wire.syncTaxIdFromDocument(); }

        $watch('noTaxId', (value) => {
            if (value === false) { taxId = ''; }
        });
     "
        >
            <div class="form-group group relative z-0">
                <input
                    x-model="taxId"
                    required
                    id="taxId"
                    type="text"
                    maxlength="10"
                    placeholder=" "
                    class="input peer text-gray-500 @error('form.party.taxId') input-error @enderror"
                    :disabled="noTaxId || $wire.isPersonalDataLocked || $wire.isPartyDataPartiallyLocked"
                />
                <label for="taxId" class="label z-10" x-text="noTaxId ? '{{ __('forms.document_no_tax_id') }}' : '{{ __('forms.tax_id') }}'"></label>
                @error('form.party.taxId') <p class="text-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group group">
                <div class="mt-3">
                    <input
                        wire:model="form.party.noTaxId"
                        @click.prevent="$wire.toggleNoTaxId()"
                        type="checkbox"
                        id="noTaxId"
                        class="default-checkbox text-blue-500 focus:ring-blue-300"
                        :disabled="$wire.isPersonalDataLocked || $wire.isPartyDataPartiallyLocked"
                    >
                    <label for="noTaxId" class="ms-2 text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('forms.no_tax_id') }}</label>
                </div>
            </div>
        </div>

        {{-- Phones Section (НЕ МАЄ часткового блокування) --}}
        <div
            class="space-y-2"
            x-data="{ phones: $wire.entangle('form.party.phones') }"
            x-init="if (!Array.isArray(phones) || phones.length === 0) { phones = [{ type: 'MOBILE', number: '' }] }"
        >
            <template x-for="(phone, index) in phones" :key="index">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">

                    {{-- Phone Type Select --}}
                    <div class="form-group">
                        <select x-model="phones[index].type" class="input-select @error('form.party.phones.*.type') input-error @enderror" required>
                            <option value="" disabled>{{__('forms.type_mobile')}} *</option>
                            @foreach($this->dictionaries['PHONE_TYPE'] as $key => $phoneType)
                                <option
                                    value="{{$key}}"
                                    :disabled="phones.some((p, i) => i !== index && p.type === '{{$key}}')">
                                    {{$phoneType}}
                                </option>

                            @endforeach
                        </select>
                        <label class="label">{{ __('forms.phone_type') }}</label>
                        @error('form.party.phones.*.type') <p class="text-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone Number Input --}}
                    <div class="form-group phone-wrapper">
                        <input
                            required
                            type="tel"
                            placeholder=" "
                            class="peer input pl-10 with-leading-icon text-gray-500"
                            x-model="phone.number"
                            x-mask="+380999999999"
                        />
                        <label class="wrapped-label">{{ __('forms.phone') }}</label>
                        @error('form.party.phones.*.number') <p class="text-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center space-x-4 justify-start">
                        <template x-if="phones.length > 1">
                            <button type="button" @click="phones.splice(index, 1)" class="item-remove text-red-600 hover:text-red-800 justify-self-start">
                                <span>{{__('forms.remove_phone')}}</span>
                            </button>
                        </template>

                        <template x-if="index === phones.length - 1 && phones.length < 2">
                            <button type="button" @click="phones.push({ type: 'MOBILE', number: '' })" class="item-add">
                                <span>{{__('forms.add_phone')}}</span>
                            </button>
                        </template>
                    </div>

                </div>
            </template>
        </div>

        {{-- Email & About Myself --}}
        <div class="form-row-3">
            <div class="form-group">
                <input wire:model="form.party.email" type="email" id="email" name="email" class="peer input text-gray-500" placeholder=" "/>
                <label for="email" class="label">{{ __('forms.email') }}</label>
                @error('form.party.email') <p class="text-error">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label for="aboutMyself"
                       class="peer appearance-none bg-white text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ __('forms.about_myself') }}</label>
                <textarea
                    id="aboutMyself"
                    wire:model="form.party.aboutMyself"
                    class="textarea !text-gray-500 dark:!text-gray-400 mt-1"
                    placeholder="{{ __('forms.comment') }}">
                </textarea>
                @error('form.party.aboutMyself') <p class="text-error">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</fieldset>
