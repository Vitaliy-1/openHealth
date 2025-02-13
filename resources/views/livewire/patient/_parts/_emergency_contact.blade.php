<fieldset class="fieldset">
    <legend class="legend">
        {{ __('forms.emergencyContact') }}
    </legend>

    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="patientRequest.patient.emergencyContact.firstName"
                   type="text"
                   name="patientFirstName"
                   id="emergencyContactFirstName"
                   class="input peer @error('patientRequest.patient.emergencyContact.firstName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="emergencyContactFirstName" class="label">
                {{ __('forms.firstName') }}
            </label>

            @error('patientRequest.patient.emergencyContact.firstName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="patientRequest.patient.emergencyContact.lastName"
                   type="text"
                   name="patientFirstName"
                   id="emergencyContactLastName"
                   class="input peer @error('patientRequest.patient.emergencyContact.lastName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="emergencyContactLastName" class="label">
                {{ __('forms.lastName') }}
            </label>

            @error('patientRequest.patient.emergencyContact.lastName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="patientRequest.patient.emergencyContact.secondName"
                   type="text"
                   name="emergencyContactSecondName"
                   id="emergencyContactSecondName"
                   class="input peer @error('patientRequest.patient.secondName') input-error @enderror"
                   placeholder=" "
                   autocomplete="off"
            />
            <label for="emergencyContactSecondName" class="label">
                {{ __('forms.secondName') }}
            </label>

            @error('patientRequest.patient.emergencyContact.secondName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>

    <x-forms.form-row cols="flex-col" gap="gap-0">
        <x-forms.label name="label" class="default-label">
            {{ __('forms.phones') }} *
        </x-forms.label>

        <x-forms.form-phone :phones="$patientRequest->patient['emergencyContact']['phones'] ?? []"
                            :property="'patientRequest.patient.emergencyContact'"
        />
    </x-forms.form-row>

    @include('livewire.patient._parts._addresses')

</fieldset>
