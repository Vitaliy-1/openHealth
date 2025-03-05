<fieldset class="fieldset"
          x-data="{
              noTaxId: $wire.form.patient.noTaxId || false,
              init() {
                  $wire.form.patient.noTaxId = this.noTaxId;
              },
              handleNoTaxIdChange() {
                  if (this.noTaxId) {
                      $wire.form.patient.noTaxId = true;
                      delete $wire.form.patient.taxId;
                  } else {
                      $wire.form.patient.noTaxId = false;
                  }
              }
          }"
>

    <legend class="legend">
        {{ __('patients.patient_identity_documents') }}
    </legend>

    <div class="flex items-center gap-2 mb-4">
        <label for="noTaxId" class="default-label">
            {{ __('patients.rnokpp_not_found') }}
        </label>
        <input x-model="noTaxId"
               @change="handleNoTaxIdChange"
               type="checkbox"
               name="noTaxId"
               id="noTaxId"
               class="default-checkbox mb-2"
        />
    </div>

    <template x-if="!noTaxId">
        <div class="form-row-4">
            <div class="form-group group">
                <input wire:model="form.patient.taxId"
                       type="text"
                       name="taxId"
                       id="taxId"
                       class="input peer @error('form.patient.taxId') input-error @enderror"
                       placeholder=" "
                       required
                       maxlength="10"
                       autocomplete="off"
                />
                <label for="taxId" class="label">
                    {{ __('forms.tax_id') }}
                </label>

                @error('form.patient.taxId')
                <p class="text-error">
                    {{ $message }}
                </p>
                @enderror
            </div>
        </div>
    </template>
</fieldset>
