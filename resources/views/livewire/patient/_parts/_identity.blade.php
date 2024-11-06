<div>
    <div class="py-4">
        <h3 class="font-medium text-2xl	text-black dark:text-white">
            {{ __('forms.patient_identity_documents') }}
        </h3>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/2 flex items-center gap-3">
            <x-slot name="label">
                <x-forms.label class="default-label" for="no_tax_id">
                    {{ __('forms.rnokpp_not_found') }}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-checkbox class="default-checkbox mb-2"
                            wire:model.live="no_tax_id"
                            id="no_tax_id" name="no_tax_id"/>
            </x-slot>
            @error('no_tax_id')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    @if(!$no_tax_id)
        <div class="mb-4 flex flex-col gap-6 xl:flex-row">
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label class="default-label" for="tax_id">
                        {{ __('forms.number') }} {{ __('forms.RNOCPP') }} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input maxlength="10" class="default-input" checked
                                   wire:model="patient_request.identity.tax_id" type="text" id="tax_id" name="tax_id"/>
                </x-slot>
                @error('patient_request.identity.tax_id')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </div>
    @endif

    <div class="mb-4 flex flex-col xl:flex-row justify-between items-center">
        <div class="xl:w-1/4 text-left"></div>
        <div class="xl:w-1/4 text-right">
            <x-button wire:click="store('identity')" type="submit" class="btn-primary d-flex max-w-[150px]">
                {{ __('forms.save') }}
            </x-button>
        </div>
    </div>
</div>
