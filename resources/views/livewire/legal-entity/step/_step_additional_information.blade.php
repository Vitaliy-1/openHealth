@php
    $hasAdditionalInformationArchiveDateError = $errors->has('legalEntityForm.archive.date');
    $hasAdditionalInformationArchivePlaceError = $errors->has('legalEntityForm.archive.place');
    $hasAdditionalInformationReceiverFundsCodeError = $errors->has('legalEntityForm.receiverFundsCode');
    $hasAdditionalInformationBeneficiaryError = $errors->has('legalEntityForm.beneficiary');
@endphp

<fieldset
    class="fieldset"
    xmlns="http://www.w3.org/1999/html"
    x-data="{ title: '{{ __('forms.information') }}', index: 7 }"
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
            <input
            type="text"
            placeholder=" "
            id="additionalInformationReceiverFundsCode"
            wire:model="legalEntityForm.receiverFundsCode"
            aria-describedby="{{ $hasAdditionalInformationReceiverFundsCodeError ? 'additionalInformationReceiverFundsCodeErrorHelp' : '' }}"
            class="input peer"
        />

            @if($hasAdditionalInformationReceiverFundsCodeError)
                <p id="additionalInformationReceiverFundsCodeErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.receiverFundsCode') }}
                </p>
            @endif

            <p id="additionalInformationReceiverFundsCoderHelp" class="text-note">
                {{ __('forms.receiver_funds_code') }}
            </p>

            <label for="additionalInformationReceiverFundsCode" class="label z-10">
                {{ __('forms.treasury_registration_code') }}
            </label>
        </div>

        <div class="form-group group">
            <input
                type="text"
                placeholder=" "
                id="additionalInformationBeneficiary"
                wire:model="legalEntityForm.beneficiary"
                aria-describedby="{{ $hasAdditionalInformationBeneficiaryError ? 'additionalInformationBeneficiaryErrorHelp' : '' }}"
                class="input peer"
            />

            @if($hasAdditionalInformationBeneficiaryError)
                <p id="additionalInformationBeneficiaryErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.beneficiary') }}
                </p>
            @endif

            <p id="additionalInformationBeneficiaryHelp" class="text-note">
                {{ __('forms.beneficiary_info') }}
            </p>

            <label for="additionalInformationBeneficiary" class="label z-10">
                {{ __('forms.beneficiary') }}
            </label>
        </div>
    </div>

    <div x-data="{ showArchivation: $wire.entangle('legalEntityForm.archivationShow') }">
        <div class='form-row-3'>
            <div class="form-group group">
                <input
                    type="checkbox"
                    id="archivationShow"
                    class="default-checkbox text-blue-500 focus:ring-blue-300"
                    x-model="showArchivation"
                    :checked="showArchivation"
                >
                <label for="archivationShow" class="ms-2 text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('forms.archivation') }}</label>
            </div>
        </div>

         <template x-if="showArchivation">
            <div class='form-row-3'>
                <div class="form-group group">
                    <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                    </svg>

                    <input
                        required
                        type="text"
                        placeholder=" "
                        id="additionalInformationArchiveDate"
                        wire:model="legalEntityForm.archive.date"
                        aria-describedby="{{ $hasAdditionalInformationArchiveDateError ? 'additionalInformationArchiveDateErrorHelp' : '' }}"
                        class="input datepicker-input {{ $hasAdditionalInformationArchiveDateError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                    />

                    @if($hasAdditionalInformationArchiveDateError)
                        <p id="additionalInformationArchiveDateErrorHelp" class="text-error">
                            {{ $errors->first('legalEntityForm.archive.date') }}
                        </p>
                    @endif

                    <label for="additionalInformationArchiveDate" class="label z-10">
                        {{ __("forms.archive_date") }}
                    </label>
                </div>

                <div class="form-group group">
                    <input
                        required
                        type="text"
                        placeholder=" "
                        id="additionalInformationArchivePlace"
                        wire:model="legalEntityForm.archive.place"
                        aria-describedby="{{ $hasAdditionalInformationArchivePlaceError ? 'additionalInformationArchivePlaceErrorHelp' : '' }}"
                        class="input {{ $hasAdditionalInformationArchivePlaceError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                    />

                    @if($hasAdditionalInformationArchivePlaceError)
                        <p id="additionalInformationArchivePlaceErrorHelp" class="text-error">
                            {{ $errors->first('legalEntityForm.archive.place') }}
                        </p>
                    @else
                        <p id="additionalInformationArchivePlaceHelp" class="text-note">
                            {{ __('forms.archive_place') }}
                        </p>
                    @endif

                    <label for="additionalInformationArchivePlace" class="label z-10">
                        {{ __('forms.address') }}
                    </label>
                </div>
            </div>
        </template>
    </div>
</fieldset>
