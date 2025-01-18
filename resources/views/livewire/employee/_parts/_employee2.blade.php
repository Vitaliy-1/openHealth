<fieldset class="fieldset">
    <legend class="legend">
        {{__('forms.personalData')}}
    </legend>

    <div class="form-row-3">
        <div class="form-group group relative">
            <input
                wire:model="employeeRequest.party.lastName"
                type="text"
                name="lastName"
                id="lastName"
                class="input peer @error('employeeRequest.party.lastName') input-error @enderror"
                placeholder=" "
                required
            />

            <label for="lastName" class="label">
                {{__('forms.lastName')}}
            </label>

            @error('employeeRequest.party.lastName')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="employeeRequest.party.firstName"
                   type="text"
                   name="firstName"
                   id="firstName"
                   class="input peer @error('employeeRequest.party.firstName') input-error @enderror"
                   placeholder=" "
                   required
            />
            <label for="firstName" class="label">
                {{__('forms.firstName')}}
            </label>

            @error('employeeRequest.party.firstName')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>

        <div class="form-group group">
            <input
                wire:model="employeeRequest.party.secondName"
                type="text"
                name="secondName"
                id="secondName"
                class="input peer @error('employeeRequest.party.secondName') input-error @enderror"
                placeholder=" "
                required
            />
            <label for="secondName" class="label">
                {{__('forms.secondName')}}
            </label>

            @error('employeeRequest.party.secondName')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>
    </div>

    <div class="form-row-3">
        <div class="form-group group">
            <label for="employeeGender" class="sr-only">{{__('forms.select')}} {{__('forms.gender')}}</label>
            <select wire:model="employeeRequest.party.gender" id="employeeGender" class="input-select peer" required>
                <option selected>{{__('forms.gender')}} *</option>
                @foreach($this->dictionaries['GENDER'] as $k=>$gender )
                    <option value="{{$k}}">{{$gender}}</option>
                @endforeach
            </select>

            @error('employeeRequest.party.gender')
            <p class="text-error">
                {{$message}}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
            </svg>

            <input wire:model="employeeRequest.party.birthDate"
                   datepicker
                   type="text"
                   name="birthDate"
                   id="birthDate"
                   class="input default-datepicker peer @error('employeeRequest.party.birthDate') input-error @enderror"
                   placeholder=" "
                   required
            />

            <label for="birthDate" class="label">
                {{__('forms.birthDate')}}
            </label>

            @error('employeeRequest.party.birthDate')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="employeeRequest.party.taxId"
                   type="text"
                   id="taxId"
                   name="taxId"
                   class="input peer @error('employeeRequest.party.taxId') input-error @enderror"
                   placeholder=" "
                   required
            />

            <label for="taxId" class="label">
                {{__('forms.number')}} {{__('forms.RNOCPP')}}
            </label>

            @error('employeeRequest.party.taxId')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>
    </div>

    <div class="form-row-3">
        <div class="form-group group">
            <svg class="svg-input w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2.038 5.61A2.01 2.01 0 0 0 2 6v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6c0-.12-.01-.238-.03-.352l-.866.65-7.89 6.032a2 2 0 0 1-2.429 0L2.884 6.288l-.846-.677Z"/>
                <path d="M20.677 4.117A1.996 1.996 0 0 0 20 4H4c-.225 0-.44.037-.642.105l.758.607L12 10.742 19.9 4.7l.777-.583Z"/>
            </svg>

            <input
                wire:model="employeeRequest.party.email"
                type="text"
                name="email"
                id="email"
                class="input peer @error('employeeRequest.party.email') input-error @enderror"
                placeholder=" "
                required
            />
            <label for="email" class="label">
                {{__('forms.email')}}
            </label>

            @error('employeeRequest.party.email')
            <p class="text-error">
                {{$message}}
            </p>
            @enderror
        </div>
    </div>
</fieldset>
