<fieldset class="fieldset">
    <legend class="legend">
        {{ __('forms.contactData') }}
    </legend>

    {{-- Using Alpine to dynamically add and remove phone input fields --}}
    <div class="mb-4" x-data="{ phones: $wire.entangle('patientRequest.patient.phones') }">
        <template x-for="(phone, index) in phones">
            <div class="form-row-3 md:mb-0">
                <div class="form-group group">
                    <label :for="'phoneType-' + index" class="sr-only">{{ __('forms.typeMobile') }}</label>
                    <select x-model="phone.type" :id="'phoneType-' + index" class="input-select peer" required>
                        <option selected>{{ __('forms.typeMobile') }} *</option>
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

                    <label :for="'phoneNumber-' + index" class="label">
                        {{__('forms.phone_number')}}
                    </label>
                    <input x-model="phone.number"
                           type="tel"
                           name="phoneNumber"
                           :id="'phoneNumber-' + index"
                           class="input peer @error('patientRequest.patient.phones.*.number') input-error @enderror"
                           placeholder=" "
                           required
                    />

                    @error('patientRequest.patient.phones.*.number')
                    <p class="text-error">
                        {{$message}}
                    </p>
                    @enderror
                </div>
                <template x-if="index == phones.length - 1 & index != 0">
                    {{-- Remove a phone if button is clicked --}}
                    <button x-on:click="phones.pop(), index--" class="item-remove">
                        <svg>
                            <use xlink:href="#svg-minus"></use>
                        </svg>
                        {{ __('forms.removePhone') }}
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
                        {{ __('forms.addPhone') }}
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
                {{ __('forms.secret') }}
            </label>

            @error('patientRequest.patient.secret')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>
</fieldset>
