@php
    $hasLicenseTypeError = $errors->has('legalEntityForm.license.type');
    $hasLicenseIssuedByError = $errors->has('legalEntityForm.license.issued_by');
    $hasLicenseIssuedDateError = $errors->has('legalEntityForm.license.issued_date');
    $hasLicenseActiveFromDateError = $errors->has('legalEntityForm.license.active_from_date');
    $hasLicenseOrderNumberError = $errors->has('legalEntityForm.license.order_no');
@endphp

<fieldset
    class="fieldset"
    xmlns="http://www.w3.org/1999/html"
    x-data="{ title: '{{ __('forms.licenses') }}', index: 6 }"
    x-init="typeof addHeader !== 'undefined' && addHeader(title, index)"
    x-show="activeStep === index || isEdit"
    x-cloak
    :key="`step-${index}`"
>
    <template x-if="isEdit">
        <legend x-text="title" class="legend"></legend>
    </template>

    <div class='form-row-3'>
        <div class="form-group group">
            <select
                required
                id="licenseType"
                wire:model.defer="legalEntityForm.license.type"
                aria-describedby="{{ $hasLicenseTypeError ? 'licenseTypeErrorHelp' : '' }}"
                class="input-select text-gray-800 {{ $hasLicenseTypeError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            >
                <option value="_placeholder_" selected hidden>-- {{ __('forms.select') }} --</option>

                @foreach($dictionaries['LICENSE_TYPE'] as $k => $license_type)
                    <option value="{{ $k }}" {{ $k === 'MSP' ? 'selected' : '' }}>
                        {{ $license_type }}
                    </option>
                @endforeach
            </select>

            @if($hasLicenseTypeError)
                <p id="licenseTypeErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.license.type') }}
                </p>
            @endif

            <label for="licenseType" class="label z-10">
                {{ __('forms.license_type') }}
            </label>
        </div>

        <div class="form-group group">
            <input
                type="text"
                placeholder=" "
                id="licenseNumber"
                wire:model="legalEntityForm.license.licenseNumber"
                class="input peer"
            />

            <label for="licenseNumber" class="label z-10">
                {{ __('forms.license_number') }}
            </label>
        </div>

        <div class="form-group group">
            <input
                required
                type="text"
                placeholder=" "
                id="licenseIssuedBy"
                wire:model="legalEntityForm.license.issuedBy"
                aria-describedby="{{ $hasLicenseIssuedByError ? 'licenseIssuedByErrorHelp' : '' }}"
                class="input {{ $hasLicenseIssuedByError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasLicenseIssuedByError)
                <p id="licenseIssuedByErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.license.issuedBy') }}
                </p>
            @endif

            <label for="licenseIssuedBy" class="label z-10">
                {{ __('forms.license_issued_by') }}
            </label>
        </div>

        <div class="form-group group">
            <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
            </svg>

            <input
                required
                type="text"
                placeholder=" "
                id="licenseIssuedDate"
                wire:model="legalEntityForm.license.issuedDate"
                aria-describedby="{{ $hasLicenseIssuedDateError ? 'licenseIssuedDateErrorHelp' : '' }}"
                class="input datepicker-input {{ $hasLicenseIssuedDateError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasLicenseIssuedDateError)
                <p id="licenseIssuedDateErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.license.issuedDate') }}
                </p>
            @endif

            <label for="licenseIssuedDate" class="label z-10">
                {{ __('forms.license_issued_date') }}
            </label>
        </div>

        <div class="form-group group">
            <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
            </svg>

            <input
                required
                type="text"
                placeholder=" "
                id="licenseActiveFromDate"
                wire:model="legalEntityForm.license.activeFromDate"
                aria-describedby="{{ $hasLicenseActiveFromDateError ? 'licenseActiveFromDateErrorHelp' : '' }}"
                class="input datepicker-input {{ $hasLicenseActiveFromDateError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasLicenseActiveFromDateError)
                <p id="licenseActiveFromDateErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.license.activeFromDate') }}
                </p>
            @endif

            <label for="licenseActiveFromDate" class="label z-10">
                {{ __('forms.license_active_from_date') }}
            </label>
        </div>

        <div class="form-group group">
            <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
            </svg>

            <input
                type="text"
                placeholder=" "
                id="licenseExpiryDate"
                wire:model="legalEntityForm.license.expiryDate"
                class="input datepicker-input peer"
            />

            <label for="licenseExpiryDate" class="label z-10">
                {{ __('forms.license_expiry_date') }}
            </label>
        </div>

        <div class="form-group group">
            <input
                type="text"
                placeholder=" "
                id="licenseWhatLicensed"
                wire:model="legalEntityForm.license.whatLicensed"
                class="input peer"
            />

            <label for="licenseWhatLicensed" class="label z-10">
                {{ __('forms.license_what_licensed') }}
            </label>
        </div>

        <div class="form-group group">
            <input
                required
                type="text"
                placeholder=" "
                id="licenseOrderNumber"
                wire:model="legalEntityForm.license.orderNo"
                aria-describedby="{{ $hasLicenseOrderNumberError ? 'licenseOrderNumberErrorHelp' : '' }}"
                class="input {{ $hasLicenseOrderNumberError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasLicenseOrderNumberError)
                <p id="licenseOrderNumberErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.license.orderNo') }}
                </p>
            @endif

            <label for="licenseOrderNumber" class="label z-10">
                {{ __('forms.license_order_no') }}
            </label>
        </div>
    </div>
</fieldset>
