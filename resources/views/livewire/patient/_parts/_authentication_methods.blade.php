@php use App\Enums\Person\AuthenticationMethod; @endphp

<div>
    <div
        class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ __('forms.authentication') }}
        </h5>

        <x-forms.form-row>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="relation_type" class="default-label">
                        {{ __('forms.authentication') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.select class="default-input"
                                    wire:model.live="patientRequest.patient.authenticationMethods.0.type"
                                    type="text"
                                    id="relation_type"
                    >
                        <x-slot name="option">
                            <option>
                                {{ __('forms.select') }} {{ __('forms.authentication') }}
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
                        </x-slot>
                    </x-forms.select>
                </x-slot>

                @error('patientRequest.patient.authenticationMethods.type')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>

        @isset($patientRequest->patient['authenticationMethods'][0]['type'])
            @if($patientRequest->patient['authenticationMethods'][0]['type'] === AuthenticationMethod::OTP->value)
                <x-forms.form-group class="mb-2">
                    <x-slot name="label">
                        <x-forms.form-row class="items-center">
                            <div class="w-1/3">
                                <x-forms.input class="default-input"
                                               x-mask="+380999999999"
                                               wire:model="patientRequest.patient.authenticationMethods.0.phoneNumber"
                                               type="text"
                                               id="phone_number"
                                               placeholder="{{ __('+ 3(80)00 000 00 00 ') }}"
                                />

                                @error("patientRequest.patient.authenticationMethods.phoneNumber")
                                <x-forms.error>
                                    {{ $message }}
                                </x-forms.error>
                                @enderror
                            </div>
                        </x-forms.form-row>
                    </x-slot>
                </x-forms.form-group>

            @elseif($patientRequest->patient['authenticationMethods'][0]['type'] === AuthenticationMethod::THIRD_PERSON->value)
                <x-forms.form-group class="xl:w-1/3">
                    <x-slot name="label">
                        <x-forms.label for="alias" class="default-label">
                            {{ __('forms.alias') }} *
                        </x-forms.label>
                    </x-slot>

                    <x-slot name="input">
                        <x-forms.input class="default-input"
                                       wire:model="patientRequest.patient.authenticationMethods.0.alias"
                                       type="text"
                                       id="alias"
                        />
                    </x-slot>

                    @error('patientRequest.patient.authenticationMethods.alias')
                    <x-slot name="error">
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                    </x-slot>
                    @enderror
                </x-forms.form-group>
            @endif
        @endisset
    </div>
</div>
