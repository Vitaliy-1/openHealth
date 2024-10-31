<div>
    <div class="py-4">
        <h3 class="font-medium text-2xl	text-black dark:text-white">
            {{ __('forms.patient_identity_documents') }}
        </h3>
    </div>

    <div class="mb-4 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label class="default-label" for="tax_id_missing">
                    {{ __('forms.rnokpp_not_found') }}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-checkbox class="default-checkbox"
                            wire:model.live="tax_id_missing"
                            id="tax_id_missing" name="tax_id_missing"/>
            </x-slot>
            @error('tax_id_missing')
            <x-slot name="error">
                <x-forms.error>
                    {{ $message }}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>

    @if(!$tax_id_missing)
        <div class="mb-4 flex flex-col gap-6 xl:flex-row">
            <x-forms.form-group class="xl:w-1/2">
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
