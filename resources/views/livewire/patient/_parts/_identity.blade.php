<fieldset class="fieldset">
    <legend class="legend">
        {{ __('forms.patientIdentityDocuments') }}
    </legend>

    <div class="flex items-center gap-2 mb-4">
        <label for="noTaxId" class="default-label">
            {{ __('forms.rnokppNotFound') }}
        </label>
        <input wire:model.live="noTaxId"
               type="checkbox"
               name="noTaxId"
               id="noTaxId"
               class="default-checkbox mb-2"
        />
    </div>

    @if(!$noTaxId)
        <div class="form-row-4">
            <div class="form-group group">
                <input wire:model="patientRequest.patient.taxId"
                       type="text"
                       name="taxId"
                       id="taxId"
                       class="input peer @error('patientRequest.patient.taxId') input-error @enderror"
                       placeholder=" "
                       required
                       maxlength="10"
                       autocomplete="off"
                />
                <label for="taxId" class="label">
                    {{ __('forms.number') }} {{ __('forms.RNOCPP') }}
                </label>

                @error('patientRequest.patient.taxId')
                <p class="text-error">
                    {{ $message }}
                </p>
                @enderror
            </div>
        </div>
    @endif
</fieldset>
