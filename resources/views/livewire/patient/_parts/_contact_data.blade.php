<div>
    <div class="py-4">
        <h3 class="font-medium text-2xl	text-black dark:text-white">
            {{ __('forms.contact_data') }}
        </h3>
    </div>

    <div class="mb-4">
        <x-forms.label name="label" class="default-label">
            {{ __('forms.phones') }}
        </x-forms.label>
        @if($phones)
            @foreach($phones as $key => $phone)
                <x-forms.form-group class="mb-2">
                    <x-slot name="label">
                        <div class="flex-row flex gap-6 items-center">
                            <div class="w-1/4">
                                <x-forms.select wire:model.defer="patient_request.contact_data.phones.{{ $key }}.type"
                                                class="default-select">
                                    <x-slot name="option">
                                        <option>{{ __('forms.phone_type') }}</option>
                                        @foreach($this->dictionaries['PHONE_TYPE'] as $k => $phone_type)
                                            <option value="{{ $k }}">{{ $phone_type }}</option>
                                        @endforeach
                                    </x-slot>
                                </x-forms.select>
                                @error("patient_request.contact_data.phones.{$key}.type")
                                <x-forms.error>
                                    {{ $message }}
                                </x-forms.error>
                                @enderror
                            </div>
                            <div class="w-1/4">
                                <x-forms.input x-mask="+380999999999" class="default-input"
                                               wire:model="patient_request.contact_data.phones.{{ $key }}.number" type="text"
                                               placeholder="{{ __('+ 3(80)00 000 00 00 ') }}"
                                />

                                @error("patient_request.contact_data.phones.{$key}.number")
                                <x-forms.error>
                                    {{ $message }}
                                </x-forms.error>
                                @enderror
                            </div>
                            <div class="w-1/4">
                                @if($key !== 0)
                                    <a wire:click="removePhone({{ $key }})" class="text-primary m-t-5" href="#">
                                        {{__('forms.removePhone')}}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </x-slot>
                </x-forms.form-group>
            @endforeach
        @endif

        <a wire:click="addRowPhone" class="text-primary m-t-5" href="#">
            {{ __('forms.addPhone') }}
        </a>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="email" class="default-label">
                    {{ __('forms.email') }}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.contact_data.email" type="email"
                               id="email" placeholder="{{ __('E-mail') }}"
                />
            </x-slot>
            @error('patient_request.contact_data.email')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="preferred_way_communication" class="default-label">
                    {{ __('forms.preferred_way_communication') }}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.select
                    class="default-input" wire:model="patient_request.contact_data.preferred_way_communication" type="text"
                    id="preferred_way_communication">
                    <x-slot name="option">
                        <option> {{ __('forms.select') }} {{ __('forms.preferred_way_communication') }}</option>
                        @foreach($this->dictionaries['PREFERRED_WAY_COMMUNICATION'] as $k => $preferred_way_communication)
                            <option value="{{ $k }}">{{ $preferred_way_communication }}</option>
                        @endforeach
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('patient_request.contact_data.preferred_way_communication')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/3">
            <x-slot name="label">
                <x-forms.label for="secret" class="default-label">
                    {{ __('forms.secret') }} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="patient_request.contact_data.secret" type="text"
                               id="secret"/>
            </x-slot>
            @error('patient_request.contact_data.secret')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    <div class="mb-4 flex flex-col xl:flex-row justify-between items-center">
        <div class="xl:w-1/4 text-left"></div>
        <div class="xl:w-1/4 text-right">
            <x-button wire:click="store('contact_data')" type="submit" class="btn-primary d-flex max-w-[150px]">
                {{ __('forms.save') }}
            </x-button>
        </div>
    </div>
</div>
