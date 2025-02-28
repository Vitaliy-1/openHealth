<fieldset class="fieldset">
    <legend class="legend">
        {{ __('patients.emergency_contact') }}
    </legend>

    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="form.patient.emergencyContact.firstName"
                   type="text"
                   name="patientFirstName"
                   id="emergencyContactFirstName"
                   class="input peer @error('form.patient.emergencyContact.firstName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="emergencyContactFirstName" class="label">
                {{ __('forms.first_name') }}
            </label>

            @error('form.patient.emergencyContact.firstName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="form.patient.emergencyContact.lastName"
                   type="text"
                   name="patientFirstName"
                   id="emergencyContactLastName"
                   class="input peer @error('form.patient.emergencyContact.lastName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="emergencyContactLastName" class="label">
                {{ __('forms.last_name') }}
            </label>

            @error('form.patient.emergencyContact.lastName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="form.patient.emergencyContact.secondName"
                   type="text"
                   name="emergencyContactSecondName"
                   id="emergencyContactSecondName"
                   class="input peer @error('form.patient.secondName') input-error @enderror"
                   placeholder=" "
                   autocomplete="off"
            />
            <label for="emergencyContactSecondName" class="label">
                {{ __('forms.second_name') }}
            </label>

            @error('form.patient.emergencyContact.secondName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>

    {{-- Using Alpine to dynamically add and remove phone input fields --}}
    <div class="mb-4" x-data="{ emergencyContactPhones: $wire.entangle('form.patient.emergencyContact.phones') }">
        <template x-for="(phone, index) in emergencyContactPhones">
            <div class="form-row-3 md:mb-0">
                <div class="form-group group">
                    <label :for="'emergencyContactPhoneType-' + index" class="sr-only">{{ __('forms.type_mobile') }}</label>
                    <select x-model="phone.type" :id="'emergencyContactPhoneType-' + index" class="input-select peer" required>
                        <option selected>{{ __('forms.type_mobile') }} *</option>
                        @foreach($this->dictionaries['PHONE_TYPE'] as $key => $phoneType)
                            <option value="{{ $key }}">{{ $phoneType }}</option>
                        @endforeach
                    </select>

                    @error('form.patient.emergencyContact.phones.*.type')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="form-group group">
                    <svg class="svg-input w-5 top-2.5" width="24" height="24">
                        <use xlink:href="#svg-phone"></use>
                    </svg>
                    <input x-model="phone.number"
                           type="tel"
                           name="emergencyContactPhone"
                           :id="'emergencyContactPhone-' + index"
                           class="input peer @error('form.patient.emergencyContact.phones.*.number') input-error @enderror"
                           placeholder=" "
                           required
                    />
                    <label :for="'emergencyContactPhone-' + index" class="label">
                        {{ __('forms.phone_number') }}
                    </label>

                    @error('form.patient.emergencyContact.phones.*.number')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                <template x-if="index == emergencyContactPhones.length - 1 & index != 0">
                    {{-- Remove a phone if button is clicked --}}
                    <button x-on:click="emergencyContactPhones.pop(), index--" class="item-remove">
                        <svg>
                            <use xlink:href="#svg-minus"></use>
                        </svg>
                        {{ __('forms.remove_phone') }}
                    </button>
                </template>
                <template x-if="index == emergencyContactPhones.length - 1">
                    {{-- Add new phone if button is clicked --}}
                    <button x-on:click="emergencyContactPhones.push({ type: '', number: '' })"
                            class="item-add lg:justify-self-start"
                            :class="{ 'lg:justify-self-start': index > 0 }" {{-- Apply this style only if it's not a first phone group --}}
                    >
                        <svg>
                            <use xlink:href="#svg-plus"></use>
                        </svg>
                        {{ __('forms.add_phone') }}
                    </button>
                </template>
            </div>
        </template>
    </div>

    @include('livewire.patient._parts._addresses')

</fieldset>
