@php
    $hasAccreditationCategoryError = $errors->has('legalEntityForm.accreditation.category');
    $hasAccreditationOrderNumberError = $errors->has('legalEntityForm.accreditation.orderNo');
    $hasAccreditationOrderDateError = $errors->has('legalEntityForm.accreditation.orderDate');
@endphp

<fieldset
    class="fieldset"
    xmlns="http://www.w3.org/1999/html"
    x-data="{ title: '{{ __('forms.accreditation') }}', index: 5 }"
    x-init="typeof addHeader !== 'undefined' && addHeader(title, index)"
    x-show="activeStep === index || isEdit"
    x-cloak
    :key="`step-${index}`"
>
    <template x-if="isEdit">
        <legend x-text="title" class="legend"></legend>
    </template>

    <div x-data="{ showAccreditation: $wire.entangle('legalEntityForm.accreditationShow') }">
        <div class='form-row-3'>
            <div class="form-group group">
                <input
                    type="checkbox"
                    id="accreditationShow"
                    class="default-checkbox text-blue-500 focus:ring-blue-300"
                    x-model="showAccreditation"
                    :checked="showAccreditation"
                >

                <label for="accreditationShow" class="ms-2 text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('forms.accreditationShow') }}</label>
            </div>
        </div>

        <div
            class='form-row-3'
            x-show="showAccreditation"
        >
            <div class="form-group group">
                <select
                    required
                    id="accreditationСategory"
                    wire:model="legalEntityForm.accreditation.category"
                    aria-describedby="{{ $hasAccreditationCategoryError ? 'accreditationCategoryErrorHelp' : '' }}"
                    class="input-select text-gray-800 {{ $hasAccreditationCategoryError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                >
                    <option value="_placeholder_" selected hidden>-- {{ __('forms.select') }} --</option>

                    @isset($dictionaries['ACCREDITATION_CATEGORY'])
                            @foreach($dictionaries['ACCREDITATION_CATEGORY'] as $k => $category)
                                <option {{ isset($legalEntityForm->accreditation['category']) == $k ? 'selected': ''}} value="{{ $k }}">
                                    {{ $category }}
                                </option>
                            @endforeach
                        @endif
                </select>

                @if($hasAccreditationCategoryError)
                    <p id="accreditationCategoryErrorHelp" class="text-error">
                        {{ $errors->first('legalEntityForm.accreditation.category') }}
                    </p>
                @endif

                <label for="accreditationСategory" class="label z-10">
                    {{ __('forms.accreditation_category') }}
                </label>
            </div>

            <div class="form-group group">

                <input
                    required
                    type="text"
                    placeholder=" "
                    id="accreditationOrderNumber"
                    wire:model="legalEntityForm.accreditation.orderNo"
                    aria-describedby="{{ $hasAccreditationOrderNumberError ? 'accreditationOrderNumberErrorHelp' : '' }}"
                    class="input {{ $hasAccreditationOrderNumberError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                />

                @if($hasAccreditationOrderNumberError)
                    <p id="accreditationOrderNumberErrorHelp" class="text-error">
                        {{ $errors->first('legalEntityForm.accreditation.orderNo') }}
                    </p>
                @endif

                <label for="accreditationOrderNumber" class="label z-10">
                    {{ __('forms.accreditation_order_no') }}
                </label>
            </div>

            <div class="form-group group">
                <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                </svg>

                <input
                    type="text"
                    placeholder=" "
                    id="accreditationIssuedDate"
                    wire:model="legalEntityForm.accreditation.issuedDate"
                    class="input datepicker-input peer"
                />

                <label for="accreditationIssuedDate" class="label z-10">
                    {{ __('forms.accreditation_issued_date') }}
                </label>
            </div>

            <div class="form-group group">
                <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                </svg>

                <input
                    type="text"
                    placeholder=" "
                    id="accreditationExpiryDate"
                    wire:model="legalEntityForm.accreditation.expiryDate"
                    class="input datepicker-input peer"
                />

                <label for="accreditationExpiryDate" class="label z-10">
                    {{ __('forms.accreditation_expired_date') }}
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
                    id="accreditationOrderDate"
                    wire:model="legalEntityForm.accreditation.orderDate"
                    aria-describedby="{{ $hasAccreditationOrderDateError ? 'accreditationOrderDateErrorHelp' : '' }}"
                    class="input datepicker-input {{ $hasAccreditationOrderDateError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                />

                @if($hasAccreditationOrderDateError)
                    <p id="accreditationOrderDateErrorHelp" class="text-error">
                        {{ $errors->first('legalEntityForm.accreditation.orderDate') }}
                    </p>
                @endif

                <label for="accreditationOrderDate" class="label z-10">
                    {{ __('forms.accreditation_order_date') }}
                </label>
            </div>
        </div>
    </div>
</fieldset>
