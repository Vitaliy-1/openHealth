<fieldset class="fieldset">
    <legend class="legend">
        {{ __('forms.patientInformation') }}
    </legend>

    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="patientRequest.patient.firstName"
                   type="text"
                   name="patientFirstName"
                   id="patientFirstName"
                   class="input peer @error('patientRequest.patient.firstName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="patientFirstName" class="label">
                {{ __('forms.firstName') }}
            </label>

            @error('patientRequest.patient.firstName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="patientRequest.patient.lastName"
                   type="text"
                   name="patientLastName"
                   id="patientLastName"
                   class="input peer @error('patientRequest.patient.lastName') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="patientLastName" class="label">
                {{ __('forms.lastName') }}
            </label>

            @error('patientRequest.patient.lastName')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="patientRequest.patient.secondName"
                   type="text"
                   name="patientSecondName"
                   id="patientSecondName"
                   class="input peer @error('patientRequest.patient.secondName') input-error @enderror"
                   placeholder=" "
                   autocomplete="off"
            />
            <label for="patientSecondName" class="label">
                {{ __('forms.secondName') }}
            </label>

            @error('patientRequest.patient.secondName')
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

            <input wire:model="patientRequest.patient.birthDate"
                   type="text"
                   name="birthDate"
                   id="birthDate"
                   class="datepicker-input input peer @error('patientRequest.patient.birthDate') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />

            <label for="birthDate" class="label">
                {{__('forms.birthDate')}}
            </label>

            @error('patientRequest.patient.birthDate')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="patientRequest.patient.birthCountry"
                   type="text"
                   name="birthCountry"
                   id="birthCountry"
                   class="input peer @error('patientRequest.patient.birthCountry') input-error @enderror"
                   placeholder=" "
                   required
                   autocomplete="off"
            />
            <label for="birthCountry" class="label">
                {{ __('forms.birthCountry') }}
            </label>

            @error('patientRequest.patient.birthCountry')
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
                {{ __('forms.birthSettlement') }}
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
            <label for="patientGender" class="sr-only">
                {{__('forms.select')}} {{__('forms.gender')}}
            </label>
            <select wire:model="patientRequest.patient.gender"
                    id="patientGender"
                    class="input-select peer @error('patientRequest.patient.gender') input-error @enderror"
                    required
            >
                <option selected>{{__('forms.gender')}} *</option>
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
                {{ __('forms.UNZR') }}
            </label>

            @error('patientRequest.patient.unzr')
            <p class="text-error">
                {{ $message }}
            </p>
            @enderror
        </div>
    </div>
</fieldset>
