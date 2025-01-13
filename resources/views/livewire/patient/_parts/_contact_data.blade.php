<div>
    <div
        class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ __('forms.contactData') }}
        </h5>

        <x-forms.form-row cols="flex-col" gap="gap-0">
            <x-forms.label name="label" class="default-label">
                {{ __('forms.phones') }}
            </x-forms.label>

            <x-forms.form-phone :phones="$patientRequest->patient['phones'] ?? []"
                                :property="'patientRequest.patient'"
            />
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
