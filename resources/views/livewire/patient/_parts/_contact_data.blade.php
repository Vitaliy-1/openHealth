<fieldset class="fieldset">
    <legend class="legend">
        {{ __('patients.contact_data') }}
    </legend>

    {{-- Using Alpine to dynamically add and remove phone input fields --}}
    <div class="mb-4" x-data="{ phones: $wire.entangle('patientRequest.patient.phones') }">
        <template x-for="(phone, index) in phones">
            <div class="form-row-3 md:mb-0">
                <div class="form-group group">
                    <label :for="'phoneType-' + index" class="sr-only">{{ __('forms.type_mobile') }}</label>
                    <select x-model="phone.type" :id="'phoneType-' + index" class="input-select peer">
                        <option selected>{{ __('forms.type_mobile') }}</option>
                        @foreach($this->dictionaries['PHONE_TYPE'] as $key => $phoneType)
                            <option value="{{ $key }}">{{ $phoneType }}</option>
                        @endforeach
                    </select>

                    @error('patientRequest.patient.phones.*.type')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="form-group group">
                    <svg class="svg-input w-5 top-2.5" height="24">
                        <use xlink:href="#svg-phone"></use>
                    </svg>
                    <input x-model="phone.number"
                           type="tel"
                           name="phoneNumber"
                           :id="'phoneNumber-' + index"
                           class="input peer @error('patientRequest.patient.phones.*.number') input-error @enderror"
                           placeholder=" "
                    />
                    <label :for="'phoneNumber-' + index" class="label">
                        {{ __('forms.phone_number') }}
                    </label>

                    @error('patientRequest.patient.phones.*.number')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                <template x-if="index == phones.length - 1 & index != 0">
                    {{-- Remove a phone if button is clicked --}}
                    <button x-on:click="phones.pop(), index--" class="item-remove">
                        <svg>
                            <use xlink:href="#svg-minus"></use>
                        </svg>
                        {{ __('forms.remove_phone') }}
                    </button>
                </template>
                <template x-if="index == phones.length - 1">
                    {{-- Add new phone if button is clicked --}}
                    <button x-on:click="phones.push({ type: '', number: '' })"
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

    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="patientRequest.patient.email"
                   type="email"
                   name="email"
                   id="email"
                   class="input peer @error('patientRequest.patient.email') input-error @enderror"
                   placeholder=" "
                   autocomplete="off"
            />
            <label for="email" class="label">
                {{ __('forms.email') }}
            </label>

            @error('patientRequest.patient.email')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="patientRequest.patient.secret"
                   type="text"
                   name="secret"
                   id="secret"
                   class="input peer @error('patientRequest.patient.secret') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="secret" class="label">
                {{ __('patients.secret') }}
            </label>

            @error('patientRequest.patient.secret')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>
</fieldset>
