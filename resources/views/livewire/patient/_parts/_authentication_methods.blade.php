@php use App\Enums\Person\AuthenticationMethod; @endphp
<div>
    <div class="py-4">
        <h3 class="font-medium text-2xl	text-black dark:text-white">
            {{ __('forms.authentication') }}
        </h3>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="relation_type" class="default-label">
                    {{ __('forms.authentication') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.select
                    class="default-input"
                    wire:model.live="patient_request.authentication_methods.type"
                    type="text"
                    id="authentication_methods.type">
                    <x-slot name="option">
                        <option>{{ __('forms.select') }} {{ __('forms.authentication') }}</option>
                        @if($is_incapable)
                            <option value="{{ AuthenticationMethod::THIRD_PERSON->value }}">
                                {{ __('forms.authentication') }} {{ AuthenticationMethod::THIRD_PERSON->label() }}
                            </option>
                        @else
                            @foreach(AuthenticationMethod::cases() as $case)
                                <option value="{{ $case->value }}">
                                    {{ __('forms.authentication') }} {{ $case->label() }}
                                </option>
                            @endforeach
                        @endif
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('patient_request.authentication_methods.type')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    @empty(!$patient_request->authentication_methods)
        @if($patient_request->authentication_methods['type'] === AuthenticationMethod::OTP->value)
            <x-forms.form-group class="mb-2">
                <x-slot name="label">
                    <div class="flex-row flex gap-6 items-center">
                        <div class="w-1/3">
                            <x-forms.input x-mask="+380999999999" class="default-input"
                                           wire:model="patient_request.authentication_methods.phone_number" type="text"
                                           placeholder="{{ __('+ 3(80)00 000 00 00 ') }}"
                            />

                            @error("patient_request.authentication_methods.phone_number")
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                            @enderror
                        </div>
                    </div>
                </x-slot>
            </x-forms.form-group>
        @endif
    @endisset

    <div class="mb-4 flex flex-col xl:flex-row justify-between items-center">
        <div class="xl:w-1/4 text-left"></div>
        <div class="xl:w-1/4 text-right">
            <x-button wire:click="store('authentication_methods')" type="submit"
                      class="btn-primary d-flex max-w-[150px]">
                {{ __('forms.save') }}
            </x-button>
        </div>
    </div>
</div>
