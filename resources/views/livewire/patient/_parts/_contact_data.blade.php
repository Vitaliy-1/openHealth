<fieldset class="fieldset">
    <legend class="legend">
        {{ __('forms.contactData') }}
    </legend>

    <x-forms.form-row cols="flex-col" gap="gap-0">
        <x-forms.form-phone :phones="$patientRequest->patient['phones'] ?? []"
                            :property="'patientRequest.patient'"
        />

        <label for="phones" class="label">
            {{ __('forms.phones') }}
        </label>
    </x-forms.form-row>

    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="patientRequest.patient.email"
                   type="email"
                   name="email"
                   id="email"
                   class="input peer @error('patientRequest.patient.email') input-error @enderror"
                   placeholder=" "
                   autocomplete="off"
            />
            <label for="email" class="label">
                {{ __('forms.email') }}
            </label>

            @error('patientRequest.patient.email')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="patientRequest.patient.secret"
                   type="text"
                   name="secret"
                   id="secret"
                   class="input peer @error('patientRequest.patient.secret') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="secret" class="label">
                {{ __('forms.secret') }}
            </label>

            @error('patientRequest.patient.secret')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>
</fieldset>
