<fieldset class="fieldset">
    <legend class="legend">
        {{__('forms.personalData')}}
    </legend>

    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="employeeRequest.party.lastName" type="text" name="lastName" id="lastName" class="input peer" placeholder=" " required/>
            <label for="lastName" class="label">
                {{__('forms.lastName')}}
            </label>
        </div>

        <div class="form-group group">
            <input wire:model="employeeRequest.party.firstName" type="text" name="firstName" id="firstName" class="input peer" placeholder=" " required/>
            <label for="firstName" class="label">
                {{__('forms.firstName')}}
            </label>
        </div>

        <div class="form-group group">
            <input wire:model="employeeRequest.party.secondName" type="text" name="secondName" id="secondName" class="input peer" placeholder=" " required/>
            <label for="secondName" class="label">
                {{__('forms.secondName')}}
            </label>
        </div>
    </div>

    <div class="form-row-3">
        <div class="form-group group">
            <input wire:model="employeeRequest.party.birthDate" datepicker type="text" name="birthDate" id="birthDate" class="input default-datepicker peer" placeholder=" " required/>
            <label for="birthDate" class="label">
                {{__('forms.birthDate')}}
            </label>
        </div>
    </div>
</fieldset>
