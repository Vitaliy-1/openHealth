@php use App\Enums\Person\AuthenticationMethod; @endphp

<fieldset class="fieldset">
    <legend class="legend">
        {{ __('forms.authentication') }}
    </legend>

    <div class="form-row-3">
        <div class="form-group group">
            <label for="relationType" class="sr-only">
                {{ __('forms.authentication') }}
            </label>
            <select wire:model.live="patientRequest.patient.authenticationMethods.0.type"
                    id="relationType"
                    class="input-select peer"
                    required
            >
                <option selected>
                    {{ __('forms.select') }} {{ __('forms.authentication') }} *
                </option>
                @if($isIncapable)
                    <option value="{{ AuthenticationMethod::THIRD_PERSON->value }}">
                        {{ __('forms.authentication') }} {{ AuthenticationMethod::THIRD_PERSON->label() }}
                    </option>
                @else
                    <option value="{{ AuthenticationMethod::OTP->value }}">
                        {{ __('forms.authentication') }} {{ AuthenticationMethod::OTP->label() }}
                    </option>
                    <option value="{{ AuthenticationMethod::OFFLINE->value }}">
                        {{ __('forms.authentication') }} {{ AuthenticationMethod::OFFLINE->label() }}
                    </option>
                @endif
            </select>

            @error('patientRequest.patient.authenticationMethods.type')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>

    @isset($patientRequest->patient['authenticationMethods'][0]['type'])
        @if($patientRequest->patient['authenticationMethods'][0]['type'] === AuthenticationMethod::OTP->value)
            <div class="form-row-3">
                <div class="form-group group">
                    <input wire:model="patientRequest.patient.authenticationMethods.0.phoneNumber"
                           type="text"
                           name="phoneNumber"
                           id="phoneNumber"
                           class="input peer @error('patientRequest.patient.authenticationMethods.0.phoneNumber') input-error @enderror"
                           placeholder=" "
                           required
                           autocomplete="off"
                    />
                    <label for="phoneNumber" class="label">
                        {{ __('forms.phone') }}
                    </label>

                    @error("patientRequest.patient.authenticationMethods.0.phoneNumber")
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

        @elseif($patientRequest->patient['authenticationMethods'][0]['type'] === AuthenticationMethod::THIRD_PERSON->value)
            <div class="form-row-3">
                <div class="form-group group">
                    <input wire:model="patientRequest.patient.authenticationMethods.0.alias"
                           type="text"
                           name="alias"
                           id="alias"
                           class="input peer @error('patientRequest.patient.authenticationMethods.0.alias') input-error @enderror"
                           placeholder=" "
                           required
                           autocomplete="off"
                    />
                    <label for="alias" class="label">
                        {{ __('forms.alias') }}
                    </label>

                    @error('patientRequest.patient.authenticationMethods.alias')
                    <p class="text-error">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>
        @endif
    @endisset
</fieldset>
