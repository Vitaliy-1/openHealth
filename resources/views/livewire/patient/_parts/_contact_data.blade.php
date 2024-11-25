<div>
    <div
        class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ __('forms.contact_data') }}
        </h5>

        <x-forms.form-row cols="flex-col" gap="gap-0">
            <x-forms.label name="label" class="default-label">
                {{ __('forms.phones') }}
            </x-forms.label>
            <x-forms.form-group class="mb-2">
                <x-slot name="label">
                    <div class="flex-row flex gap-6 items-center">
                        <div class="w-1/4">
                            <x-forms.select wire:model.defer="patientRequest.patient.phones.type"
                                            class="default-select">
                                <x-slot name="option">
                                    <option>{{ __('forms.phone_type') }}</option>
                                    @foreach($this->dictionaries['PHONE_TYPE'] as $key => $phone_type)
                                        <option value="{{ $key }}">{{ $phone_type }}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>

                            @error("patientRequest.patient.phones.type")
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                            @enderror
                        </div>
                        <div class="w-1/4">
                            <x-forms.input class="default-input"
                                           x-mask="+380999999999"
                                           wire:model="patientRequest.patient.phones.number"
                                           type="text"
                                           placeholder="{{ __('+ 3(80)00 000 00 00 ') }}"
                            />

                            @error("patientRequest.patient.phones.number")
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                            @enderror
                        </div>
                    </div>
                </x-slot>
            </x-forms.form-group>
        </x-forms.form-row>

        <x-forms.form-row>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="email" class="default-label">
                        {{ __('forms.email') }}
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.email"
                                   type="email"
                                   id="email"
                                   placeholder="{{ __('E-mail') }}"
                    />
                </x-slot>

                @error('patientRequest.patient.email')
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
                    <x-forms.select class="default-input"
                                    wire:model="patientRequest.patient.preferredWayCommunication"
                                    type="text"
                                    id="preferred_way_communication"
                    >
                        <x-slot name="option">
                            <option> {{ __('forms.select') }} {{ __('forms.preferred_way_communication') }}</option>
                            @foreach($this->dictionaries['PREFERRED_WAY_COMMUNICATION'] as $key => $preferred_way_communication)
                                <option value="{{ $key }}">{{ $preferred_way_communication }}</option>
                            @endforeach
                        </x-slot>
                    </x-forms.select>
                </x-slot>

                @error('patientRequest.patient.preferredWayCommunication')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>

        <x-forms.form-row>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="secret" class="default-label">
                        {{ __('forms.secret') }} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input class="default-input"
                                   wire:model="patientRequest.patient.secret"
                                   type="text"
                                   id="secret"
                    />
                </x-slot>

                @error('patientRequest.patient.secret')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>
    </div>
</div>
