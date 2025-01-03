<div>
    <div
        class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ __('forms.patientIdentityDocuments') }}
        </h5>

        <x-forms.form-row>
            <x-forms.form-group class="flex items-center gap-3">
                <x-slot name="label">
                    <x-forms.label for="no_tax_id" class="default-label">
                        {{ __('forms.rnokppNotFound') }}
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-checkbox class="default-checkbox mb-2"
                                wire:model.live="noTaxId"
                                id="no_tax_id"
                    />
                </x-slot>
            </x-forms.form-group>
        </x-forms.form-row>

        @if(!$noTaxId)
            <x-forms.form-row class="flex-col">
                <x-forms.form-group class="xl:w-1/3">
                    <x-slot name="label">
                        <x-forms.label for="tax_id" class="default-label">
                            {{ __('forms.number') }} {{ __('forms.RNOCPP') }} *
                        </x-forms.label>
                    </x-slot>

                    <x-slot name="input">
                        <x-forms.input class="default-input"
                                       checked
                                       maxlength="10"
                                       wire:model="patientRequest.patient.taxId"
                                       type="text"
                                       id="tax_id"
                        />
                    </x-slot>

                    @error('patientRequest.patient.taxId')
                    <x-slot name="error">
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                    </x-slot>
                    @enderror
                </x-forms.form-group>
            </x-forms.form-row>
        @endif
    </div>
</div>
