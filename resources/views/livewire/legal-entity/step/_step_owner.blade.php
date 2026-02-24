@php
    $hasOwnerLastName = $errors->has('legalEntityForm.owner.lastName');
    $hasOwnerFirstName = $errors->has('legalEntityForm.owner.firstName');
    $hasOwnerSecondName = $errors->has('legalEntityForm.owner.secondName');
    $hasOwnerBirthDate = $errors->has('legalEntityForm.owner.birthDate');
    $hasOwnerGender = $errors->has('legalEntityForm.owner.gender');
    $hasOwnerEmail = $errors->has('legalEntityForm.owner.email');
    $hasOwnerPosition = $errors->has('legalEntityForm.owner.position');
    $hasOwnerTaxId = $errors->has('legalEntityForm.owner.taxId');
    $hasOwnerDocumentType = $errors->has('legalEntityForm.owner.documents.type');
    $hasOwnerDocumentNumber = $errors->has('legalEntityForm.owner.documents.number');
    $hasOwnerDocumentIssuedBy = $errors->has('legalEntityForm.owner.documents.issuedBy');
    $hasOwnerDocumentIssuedAt = $errors->has('legalEntityForm.owner.documents.issuedAt');
@endphp

<fieldset
    class="p-4 sm:p-8 sm:pb-10 mb-16 mt-6 border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 max-w-[1280px]"
    xmlns="http://www.w3.org/1999/html"
    x-data="{
        title: '{{ __('forms.owner') }}',
        index: 2,
        isDisabled: @json($isEdit)
    }"
    x-init="typeof addHeader !== 'undefined' && addHeader(title, index)"
    x-show="activeStep === index || isEdit"
    x-cloak
    :key="`step-${index}`"
>
    <template x-if="isEdit">
        <legend x-text="title" class="legend"></legend>
    </template>

    <div class='form-row-3'>
        {{-- Owner Last Name --}}
        <div class="form-group group">
            <input
                required
                type="text"
                placeholder=" "
                id="ownerLastName"
                wire:model="legalEntityForm.owner.lastName"
                aria-describedby="{{ $hasOwnerLastName ? 'ownerLastNameErrorHelp' : '' }}"
                class="input {{ $hasOwnerLastName ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                :class="isDisabled ? 'text-gray-400 border-gray-200 dark:text-gray-500' : 'text-gray-900 border-gray-300'"
                :disabled="isDisabled"
            />

            @if($hasOwnerLastName)
                <p id="ownerLastNameErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.lastName') }}
                </p>
            @endif

            <label for="ownerLastName" class="label z-10">
                {{ __('forms.last_name') }}
            </label>
        </div>

        {{-- Owner First Name --}}
        <div class="form-group group">
            <input
                required
                type="text"
                placeholder=" "
                id="ownerFirstName"
                wire:model="legalEntityForm.owner.firstName"
                aria-describedby="{{ $hasOwnerFirstName ? 'ownerFirstNameErrorHelp' : '' }}"
                class="input {{ $hasOwnerFirstName ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                :class='text-gray-900 border-gray-300'
            />

            @if($hasOwnerFirstName)
                <p id="ownerFirstNameErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.firstName') }}
                </p>
            @endif

            <label for="ownerFirstName" class="label z-10">
                {{ __('forms.first_name') }}
            </label>
        </div>

        {{-- Owner Second Name --}}
        <div class="form-group group">
            <input
                type="text"
                placeholder=" "
                id="ownerSecondName"
                wire:model="legalEntityForm.owner.secondName"
                aria-describedby="{{ $hasOwnerSecondName ? 'ownerSecondNameErrorHelp' : '' }}"
                class="input {{ $hasOwnerSecondName ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                :class='text-gray-900 border-gray-300'
            />

            @if($hasOwnerSecondName)
                <p id="ownerSecondNameErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.secondName') }}
                </p>
            @endif

            <label for="ownerSecondName" class="label z-10">
                {{ __('forms.second_name') }}
            </label>
        </div>

        {{-- Owner Birth Date --}}
        <div class="form-group group">
            <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
            </svg>

            <input
                required
                type="text"
                placeholder=" "
                id="ownerBirthDate"
                datepicker-format="{{ frontendDateFormat() }}"
                wire:model="legalEntityForm.owner.birthDate"
                aria-describedby="{{ $hasOwnerBirthDate ? 'ownerBirthDateErrorHelp' : '' }}"
                class="input datepicker-input {{ $hasOwnerBirthDate ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                :class="isDisabled ? 'text-gray-400 border-gray-200 dark:text-gray-500' : 'text-gray-900 border-gray-300'"
                :disabled="isDisabled"
            />

            @if($hasOwnerBirthDate)
                <p id="ownerBirthDateErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.birthDate') }}
                </p>
            @endif

            <label for="ownerBirthDate" class="label z-10">
                {{__('forms.birth_date')}}
            </label>
        </div>

        {{-- Owner Gender --}}
        <div class="form-group group">
            <div
                for="ownerGender"
                class='label z-10'
            >
                {{ __('forms.gender') }} *
            </div>

            <ul
                aria-describedby="{{ $hasOwnerGender ? 'ownerGenderErrorHelp' : '' }}"
                class="steps-owner_gender_list {{ $hasOwnerGender ? 'text-error border-red-500 focus:border-red-500' : ''}}"
            >
                @isset($dictionaries['GENDER'])
                    @foreach($dictionaries['GENDER'] as $k => $gender)
                        <li class="w-content me-3">
                            <div class="flex items-center">
                                <input
                                    type="radio"
                                    name="gender"
                                    value="{{ $k }}"
                                    class="steps-owner_radio"
                                    id="owner_gender_{{ $k }}"
                                    wire:model="legalEntityForm.owner.gender"
                                    :class='text-gray-900 border-gray-300'
                                >
                                <label
                                    name="label"
                                    for="owner_gender_{{ $k }}"
                                    class="steps-owner_radio_label"
                                >
                                    {{ $gender }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                @endisset
            </ul>

            @if($hasOwnerGender)
                <p id="ownerGenderErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.gender') }}
                </p>
            @endif
        </div>
    </div>

    {{-- Email --}}
    <div class='form-row-3'>
        <div class="form-group group">
            <svg class="svg-input w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2.038 5.61A2.01 2.01 0 0 0 2 6v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6c0-.12-.01-.238-.03-.352l-.866.65-7.89 6.032a2 2 0 0 1-2.429 0L2.884 6.288l-.846-.677Z"/>
                <path d="M20.677 4.117A1.996 1.996 0 0 0 20 4H4c-.225 0-.44.037-.642.105l.758.607L12 10.742 19.9 4.7l.777-.583Z"/>
            </svg>

            <input
                required
                type="text"
                placeholder=" "
                id="ownerEmail"
                wire:model="legalEntityForm.owner.email"
                aria-describedby="{{ $hasOwnerEmail ? 'ownerEmailErrorHelp' : '' }}"
                class="input {{ $hasOwnerEmail ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasOwnerEmail)
                <p id="ownerEmailErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.email') }}
                </p>
            @endif

            <label for="ownerEmail" class="label z-10">
                {{ __('forms.email') }}
            </label>
        </div>

        {{-- Owner Position --}}
        <div class="form-group group">
            <select
                required
                id="ownerPosition"
                wire:model="legalEntityForm.owner.position"
                aria-describedby="{{ $hasOwnerPosition ? 'ownerPositionErrorHelp' : '' }}"
                class="input-select {{ $hasOwnerPosition ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                :class="isDisabled ? 'text-gray-400 border-gray-200 dark:text-gray-500' : 'text-gray-900 border-gray-300'"
                :disabled="isDisabled"
            >
                <option value="_placeholder_" selected hidden>-- {{ __('forms.select_position') }} --</option>

                @foreach($dictionaries['POSITION'] as $k => $position)
                    <option value="{{ $k }}">{{ $position }}</option>
                @endforeach
            </select>

            @if($hasOwnerPosition)
                <p id="ownerPositionErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.position') }}
                </p>
            @endif

            <label for="ownerPosition" class="label z-10">
                {{ __('forms.owner_position') }}
            </label>
        </div>
    </div>

    {{-- Owner Phones --}}
    <div
        class="space-y-2"
        x-data="{ phones: $wire.entangle('legalEntityForm.owner.phones') }"
        x-init="if (!Array.isArray(phones) || phones.length === 0) { phones = [{ type: '', number: '' }] }"
        x-id="['phone']"
    >
        <h3 class="font-bold text-sm text-gray-600 mb-6">{{ __('forms.phones_owner') }} *</h3>

        <template x-for="(phone, index) in phones" :key="index">
            <div
                class="form-row-3"
                x-data="{errors: [] }"
                x-init="errors =@js($errors->getMessages())"
                :class="{ 'mb-2': index == phones.length - 1 }"
            >
                {{-- Phone Type Select --}}
                <div class="form-group">
                    <select
                        required
                        x-model="phones[index].type"
                        class="input-select"
                        :class="{ 'input-error': errors[`legalEntityForm.owner.phones.${index}.type`] }"
                        :id="$id('phone', '_type_' + index)"
                    >
                        <option value="_placeholder_" selected hidden>-- {{ __('forms.type_mobile') }} --</option>
                        <template x-for="(phoneType, key) in $wire.dictionaries.PHONE_TYPE" :key="key">
                            <option
                                x-text="phoneType"
                                :value="key"
                                :disabled="phones.some((p) => p.type === key)"
                                :selected="phone.type === key"
                            ></option>
                        </template>
                    </select>

                    <template x-if="errors[`legalEntityForm.owner.phones.${index}.type`]">
                        <p class="text-error" x-text="errors[`legalEntityForm.owner.phones.${index}.type`]"></p>
                    </template>

                    <label :for="$id('phone', '_type_' + index)" class="label">{{ __('forms.phone_type') }}</label>
                </div>

                {{-- Phone Number Input --}}
                <div class="form-group phone-wrapper">
                    <input
                        required
                        type="tel"
                        placeholder=" "
                        class="peer input pl-10 with-leading-icon text-gray-500 "
                        x-model="phones[index].number"
                        x-mask="+380999999999"
                        :id="$id('phone', '_number' + index)"
                        :class="{ 'input-error border-red-500': errors[`legalEntityForm.owner.phones.${index}.number`] }"
                    />

                    <template x-if="errors[`legalEntityForm.owner.phones.${index}.number`]">
                        <p class="text-error" x-text="errors[`legalEntityForm.owner.phones.${index}.number`]"></p>
                    </template>

                    <label :for="$id('phone', '_number' + index)" class="wrapped-label">
                        {{ __('forms.phone') }}
                    </label>
                </div>

                <!-- Action Phone Buttons -->
                <div
                    x-cloak
                    x-show="!@json($isDetails ?? false)"
                    class="flex items-center space-x-4 justify-start"
                >
                    <!-- Add phone -->
                    <template x-if="phones.length > 1">
                        <button type="button" @click.prevent="phones.splice(index, 1)" class="item-remove text-red-600 hover:text-red-800 justify-self-start">
                            <span>{{__('forms.remove_phone')}}</span>
                        </button>
                    </template>

                    <!-- Remove Phone -->
                    <template x-if="index === phones.length - 1 && phones.length < 2">
                        <button type="button" @click.prevent="phones.push({ type: '', number: '' })" class="item-add">
                            <span>{{__('forms.add_phone')}}</span>
                        </button>
                    </template>
                </div>
            </div>
        </template>
    </div>

    {{-- Owner IPN --}}
    <div
        class='form-row-3'
        x-data="{
            showNoTaxId: $wire.entangle('legalEntityForm.owner.noTaxId'),
            taxId: $wire.entangle('legalEntityForm.owner.taxId'),
            initialShowNoTaxId: null,

            updateTaxIdInput() {
                if (this.showNoTaxId) {
                    this.$refs.taxIdInput.value = '';
                } else {
                    this.$refs.taxIdInput.value = this.showNoTaxId && this.initialShowNoTaxId ? '' : this.taxId;
                }
            }
        }"
        x-init="
            initialShowNoTaxId = showNoTaxId;
            taxId = taxId ?? null;
            updateTaxIdInput();
        "
    >
        <div class="form-group group relative z-0">
            <input
                required
                id="taxId"
                type="text"
                name="taxId"
                maxlength="10"
                placeholder=" "
                x-model="taxId"
                aria-describedby="{{ $hasOwnerTaxId ? 'ownerTaxIdErrorHelp' : '' }}"
                class="input {{ $hasOwnerTaxId ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                :class="{ 'border-gray-200 dark:border-gray-700': showNoTaxId }"
                :disabled="showNoTaxId || isDisabled"
                x-ref="taxIdInput"
                x-effect="updateTaxIdInput()"
            />

            @if($hasOwnerTaxId)
                <p id="ownerTaxIdErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.taxId') }}
                </p>
            @endif

            <label
                for="taxId"
                class="label z-10"
                :class="{ 'text-gray-200 dark:text-gray-700': showNoTaxId }"
                x-text="'{{ __('forms.number') . ' ' . __('forms.ipn') . ' / ' . __('forms.rnokpp') }}'"
            ></label>
        </div>

        <div class="form-group group">
            <div class="mt-3">
                <input
                    type="checkbox"
                    id="noTaxId"
                    class="default-checkbox text-blue-500 focus:ring-blue-300"
                    x-model="showNoTaxId"
                    :checked="showNoTaxId"
                    :disabled="isDisabled"
                >

                <label for="noTaxId" class="ms-2 text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('forms.no_tax_id') }}</label>
            </div>
        </div>
    </div>

    <div class='form-row-3'>
        {{-- Owner Document Type --}}
        <div class="form-group group relative z-0">
            <select
                required
                id="documentType"
                wire:model.defer="legalEntityForm.owner.documents.type"
                aria-describedby="{{ $hasOwnerDocumentType ? 'ownerDocumentTypeErrorHelp' : '' }}"
                class="input-select {{ $hasOwnerDocumentType ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            >
                <option value="_placeholder_" selected hidden>-- {{ __('Обрати тип') }} --</option>

                @foreach($dictionaries['DOCUMENT_TYPE'] as $k_d => $documentType)
                    <option value="{{ $k_d }}">{{ $documentType }}</option>
                @endforeach
            </select>

            @if($hasOwnerDocumentType)
                <p id="ownerDocumentTypeErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.documents.type') }}
                </p>
            @endif

            <label for="documentType" class="label z-10">
                {{ __('forms.document_type') }}
            </label>
        </div>

        {{-- Owner Document Number --}}
        <div class="form-group group relative z-0">
            <input
                required
                type="text"
                placeholder=" "
                id="documentNumber"
                wire:model="legalEntityForm.owner.documents.number"
                aria-describedby="{{ $hasOwnerDocumentNumber ? 'ownerDocumentNumberErrorHelp' : '' }}"
                class="input {{ $hasOwnerDocumentNumber ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasOwnerDocumentNumber)
                <p id="ownerDocumentNumberErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.documents.number') }}
                </p>
            @endif

            <label for="documentNumber" class="label z-10">
                {{ __('forms.document_number') }}
            </label>
        </div>
    </div>

    <div class='form-row-3'>
        {{-- Owner Document Issued By --}}
        <div class="form-group group relative z-0">
            <input
                type="text"
                placeholder=" "
                id="documentsIssuedBy"
                wire:model="legalEntityForm.owner.documents.issuedBy"
                aria-describedby="{{ $hasOwnerDocumentIssuedBy ? 'ownerDocumentIssuedByErrorHelp' : '' }}"
                class="input {{ $hasOwnerDocumentIssuedBy ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasOwnerDocumentIssuedBy)
                <p id="ownerDocumentIssuedByErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.documents.issuedBy') }}
                </p>
            @endif

            <label for="documentsIssuedBy" class="label z-10">
                {{__('forms.document_issued_by')}}
            </label>
        </div>

        {{-- Owner Document Issued At --}}
        <div class="form-group group relative z-0">
            <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
            </svg>

            <input
                type="text"
                placeholder=" "
                id="documentsIssuedAt"
                datepicker-format="{{ frontendDateFormat() }}"
                wire:model="legalEntityForm.owner.documents.issuedAt"
                aria-describedby="{{ $hasOwnerDocumentIssuedAt ? 'ownerDocumentIssuedAtErrorHelp' : '' }}"
                class="input datepicker-input {{ $hasOwnerDocumentIssuedAt ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasOwnerDocumentIssuedAt)
                <p id="ownerDocumentIssuedAtErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.documents.issuedAt') }}
                </p>
            @endif

            <label for="documentsIssuedAt" class="label z-10">
                {{ __('forms.document_issued_at') }}
            </label>
        </div>
    </div>
</fieldset>
