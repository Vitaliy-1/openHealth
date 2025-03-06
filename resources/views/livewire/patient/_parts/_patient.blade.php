<fieldset class="fieldset">
    <legend class="legend">
        {{ __('patients.patient_information') }}
    </legend>

    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="form.patient.firstName"
                   type="text"
                   name="patientFirstName"
                   id="patientFirstName"
                   class="input peer @error('form.patient.firstName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="patientFirstName" class="label">
                {{ __('forms.first_name') }}
            </label>

            @error('form.patient.firstName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="form.patient.lastName"
                   type="text"
                   name="patientLastName"
                   id="patientLastName"
                   class="input peer @error('form.patient.lastName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="patientLastName" class="label">
                {{ __('forms.last_name') }}
            </label>

            @error('form.patient.lastName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="form.patient.secondName"
                   type="text"
                   name="patientSecondName"
                   id="patientSecondName"
                   class="input peer @error('form.patient.secondName') input-error @enderror"
                   placeholder=" "
                   autocomplete="off"
            />
            <label for="patientSecondName" class="label">
                {{ __('forms.second_name') }}
            </label>

            @error('form.patient.secondName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>

    <div class="form-row-3">
        <div class="form-group group">
            <svg class="svg-input" width="20" height="20">
                <use xlink:href="#svg-calendar-week"></use>
            </svg>
            <input wire:model="form.patient.birthDate"
                   datepicker-max-date="{{ now()->format('Y-m-d') }}"
                   type="text"
                   name="birthDate"
                   id="birthDate"
                   class="datepicker-input input peer @error('form.patient.birthDate') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="birthDate" class="label">
                {{ __('forms.birth_date') }}
            </label>

            @error('form.patient.birthDate')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="form.patient.birthCountry"
                   type="text"
                   name="birthCountry"
                   id="birthCountry"
                   class="input peer @error('form.patient.birthCountry') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="birthCountry" class="label">
                {{ __('forms.birth_country') }}
            </label>

            @error('form.patient.birthCountry')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="patientRequest.patient.birthSettlement"
                   type="text"
                   name="birthSettlement"
                   id="birthSettlement"
                   class="input peer @error('patientRequest.patient.birthSettlement') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="birthSettlement" class="label">
                {{ __('forms.birth_settlement') }}
            </label>

            @error('patientRequest.patient.birthSettlement')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>

    <div class="form-row-3">
        <div class="form-group group">
            <select wire:model="patientRequest.patient.gender"
                    id="patientGender"
                    name="patientGender"
                    class="input-select peer @error('patientRequest.patient.gender') input-error @enderror"
                    required
            >
                <option selected>{{ __('forms.gender') }} *</option>
                @foreach($this->dictionaries['GENDER'] as $key => $gender)
                    <option value="{{ $key }}" wire:key="{{ $key }}">{{ $gender }}</option>
                @endforeach
            </select>

            @error('patientRequest.patient.gender')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="patientRequest.patient.unzr"
                   type="text"
                   name="unzr"
                   id="unzr"
                   class="input peer @error('patientRequest.patient.unzr') input-error @enderror"
                   placeholder=" "
                   maxlength="14"
                   autocomplete="off"
            />
            <label for="unzr" class="label">
                {{ __('patients.unzr') }}
            </label>

            @error('patientRequest.patient.unzr')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>
</fieldset>
