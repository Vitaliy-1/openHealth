<fieldset class="fieldset">
    <legend class="legend">
        {{ __('forms.information') }}
    </legend>

    <div class="form-row-modal">
        <div class="form-group group">
            <label for="vaccineCode" class="label-modal">
                {{ __('patients.code_and_name') }}
            </label>
            <select type="text"
                    x-model="modalImmunization.vaccineCode.coding[0].code"
                    id="vaccineCode"
                    class="input-modal"
                    required
            >
                <option selected>{{ __('forms.select') }}</option>
                @foreach($this->dictionaries['eHealth/vaccine_codes'] as $key => $vaccineCode)
                    <option value="{{ $key }}" wire:key="{{ $key }}">
                        {{ $vaccineCode }}
                    </option>
                @endforeach
            </select>

            <p class="text-error text-xs"
               x-show="!Object.keys(vaccineCodesDictionary).includes(modalImmunization.vaccineCode.coding[0].code)"
            >
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>

    <div class="form-row-modal">
        <div class="form-group group">
            <label for="manufacturer" class="label-modal">
                {{ __('patients.manufacturer') }}
            </label>
            <input x-model="modalImmunization.manufacturer"
                   type="text"
                   name="manufacturer"
                   id="manufacturer"
                   class="input-modal"
                   autocomplete="off"
                   required
            >

            <p class="text-error text-xs"
               x-show="(
                   modalImmunization.manufacturer.trim().length < 1 &&
                   (modalImmunization.primarySource === true && modalImmunization.notGiven === false)
               )"
            >
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>

    <div class="form-row-modal">
        <div class="form-group group">
            <label for="lotNumber" class="label-modal">
                {{ __('patients.lot_number') }}
            </label>
            <input x-model="modalImmunization.lotNumber"
                   type="text"
                   name="lotNumber"
                   id="lotNumber"
                   class="input-modal"
                   autocomplete="off"
                   required
            >

            <p class="text-error text-xs"
               x-show="(
                   modalImmunization.lotNumber.trim().length < 1 &&
                   (modalImmunization.primarySource === true && modalImmunization.notGiven === false)
               )"
            >
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>

    <div class="form-row-3">
        <div class="form-group group">
            <label for="immunizationDate" class="label-modal">
                {{ __('patients.expiration_date') }}
            </label>
            <div class="relative flex items-center">
                <svg width="20" height="20" class="svg-input absolute left-2.5 pointer-events-none">
                    <use xlink:href="#svg-calendar-week"></use>
                </svg>
                <input x-model="modalImmunization.expirationDate"
                       type="text"
                       name="expirationDate"
                       id="expirationDate"
                       class="datepicker-input input-modal !pl-10"
                       autocomplete="off"
                       required
                >
            </div>

            <p class="text-error text-xs"
               x-show="(
                   modalImmunization.expirationDate.trim().length < 1 &&
                   (modalImmunization.primarySource === true && modalImmunization.notGiven === false)
               )"
            >
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>

    <div class="form-row-3">
        <div class="form-row-modal">
            <div class="form-group group">
                <label for="amountOfInjected" class="label-modal">
                    {{ __('patients.amount_of_injected') }}
                </label>
                <input x-model="modalImmunization.doseQuantity.value"
                       type="number"
                       name="amountOfInjected"
                       id="amountOfInjected"
                       class="input-modal"
                       autocomplete="off"
                       required
                >

                <p class="text-error text-xs"
                   x-show="(modalImmunization.doseQuantity.value.trim().length < 1 && modalImmunization.notGiven === false)"
                >
                    {{ __('forms.field_empty') }}
                </p>
            </div>

            <div class="form-group group">
                <label for="measurementUnits" class="label-modal">
                    {{ __('patients.measurement_units') }}
                </label>
                <select type="text"
                        x-model="modalImmunization.doseQuantity.code"
                        @change="modalImmunization.doseQuantity.unit = immunizationDosageUnitsDictionary[modalImmunization.doseQuantity.code]"
                        name="measurementUnits"
                        id="measurementUnits"
                        class="input-modal"
                        autocomplete="off"
                        required
                >
                    <option selected>{{ __('forms.select') }}</option>
                    @foreach($this->dictionaries['eHealth/immunization_dosage_units'] as $key => $immunizationDosageUnit)
                        <option value="{{ $key }}" wire:key="{{ $key }}">
                            {{ $immunizationDosageUnit }}
                        </option>
                    @endforeach
                </select>

                <p class="text-error text-xs"
                   x-show="(modalImmunization.doseQuantity.unit.trim().length < 1 && modalImmunization.notGiven === false)"
                >
                    {{ __('forms.field_empty') }}
                </p>
            </div>
        </div>
    </div>

    <div class="form-row-3">
        <div class="form-group group">
            <label for="inputRoute" class="label-modal">
                {{ __('patients.input_route') }}
            </label>
            <select type="text"
                    x-model="modalImmunization.route.coding[0].code"
                    name="inputRoute"
                    id="inputRoute"
                    class="input-modal"
                    autocomplete="off"
                    required
            >
                <option selected>{{ __('forms.select') }}</option>
                @foreach($this->dictionaries['eHealth/vaccination_routes'] as $key => $vaccinationRoute)
                    <option value="{{ $key }}" wire:key="{{ $key }}">
                        {{ $vaccinationRoute }}
                    </option>
                @endforeach
            </select>

            <p class="text-error text-xs"
               x-show="(
                   !Object.keys(vaccinationRoutesDictionary).includes(modalImmunization.route.coding[0].code) &&
                   (modalImmunization.primarySource === true && modalImmunization.notGiven === false)
               )"
            >
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>

    <div class="form-row-3">
        <div class="form-group group">
            <label for="bodyPart" class="label-modal">
                {{ __('patients.body_part') }}
            </label>
            <select type="text"
                    x-model="modalImmunization.site.coding[0].code"
                    name="bodyPart"
                    id="bodyPart"
                    class="input-modal"
                    autocomplete="off"
                    required
            >
                <option selected>{{ __('forms.select') }}</option>
                @foreach($this->dictionaries['eHealth/immunization_body_sites'] as $key => $immunizationBodySite)
                    <option value="{{ $key }}" wire:key="{{ $key }}">
                        {{ $immunizationBodySite }}
                    </option>
                @endforeach
            </select>

            <p class="text-error text-xs"
               x-show="(
                   !Object.keys(immunizationBodySites).includes(modalImmunization.site.coding[0].code) &&
                   (modalImmunization.primarySource === true && modalImmunization.notGiven === false)
               )"
            >
                {{ __('forms.field_empty') }}
            </p>
        </div>
    </div>
</fieldset>
