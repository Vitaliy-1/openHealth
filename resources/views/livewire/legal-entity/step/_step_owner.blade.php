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
    class="fieldset"
    xmlns="http://www.w3.org/1999/html"
    x-data="{ title: '{{ __('forms.owner') }}', index: 2 }"
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
                {{-- aria-describedby="{{ $hasOwnerLastName ? 'ownerLastNameErrorHelp' : '' }}" --}}
                class="input {{ $hasOwnerLastName ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
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
                {{-- aria-describedby="{{ $hasOwnerFirstName ? 'ownerFirstNameErrorHelp' : '' }}" --}}
                class="input {{ $hasOwnerFirstName ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
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
                {{-- aria-describedby="{{ $hasOwnerSecondName ? 'ownerSecondNameErrorHelp' : '' }}" --}}
                class="input {{ $hasOwnerSecondName ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
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
                wire:model="legalEntityForm.owner.birthDate"
                {{-- aria-describedby="{{ $hasOwnerBirthDate ? 'ownerBirthDateErrorHelp' : '' }}" --}}
                class="input datepicker-input {{ $hasOwnerBirthDate ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
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
            <label
                for="ownerGender"
                class='label z-10'
            >
                {{ __('forms.gender') }} *
            </label>

            <ul
                id="ownerGender"
                {{-- aria-describedby="{{ $hasOwnerGender ? 'ownerGenderErrorHelp' : '' }}" --}}
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
                {{-- aria-describedby="{{ $hasOwnerEmail ? 'ownerEmailErrorHelp' : '' }}" --}}
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
                {{-- aria-describedby="{{ $hasOwnerPosition ? 'ownerPositionErrorHelp' : '' }}" --}}
                class="input-select text-gray-800 {{ $hasOwnerPosition ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
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
                {{ __('forms.ownerPosition') }}
            </label>
        </div>
    </div>

    {{-- Owner Phones --}}
    <div
        class='form-row mt-4'
        x-data="{ phones: $wire.entangle('legalEntityForm.owner.phones') }"
        x-init="phones = phones.length > 0 ? phones : [{ type: '', number: '' }]"
        x-id="['phone']"
     >
        <h3 class="legend text-sm text-gray-600 mb-6">{{ __('forms.phonesOwner') }} *</h3>

        <template x-for="(phone, index) in phones" :key="index">
            <div
                class="form-row-3"
                x-data="{errors: [] }"
                x-init="errors =@js($errors->getMessages())"
                :class="{ 'mb-2': index == phones.length - 1 }"
            >
                <div class="form-group group">
                    <select
                        required
                        x-model="phones[index].type"
                        class="input-select text-gray-800 peer";
                        :id="$id('phone', '_type' + index)"
                        :class="{ 'input-error border-red-500': errors[`legalEntityForm.owner.phones.${index}.type`] }"
                    >
                        <option value="_placeholder_" selected hidden>-- {{ __('forms.type_mobile') }} --</option>

                        @foreach($dictionaries['PHONE_TYPE'] as $k => $phoneType)
                            <option value="{{ $k }}">{{ $phoneType }}</option>
                        @endforeach
                    </select>

                    <template x-if="errors[`legalEntityForm.owner.phones.${index}.type`]">
                        <p class="text-error" x-text="errors[`legalEntityForm.owner.phones.${index}.type`]"></p>
                    </template>

                    <label :for="$id('phone', '_type' + index)" class="label z-10">
                        {{ __('forms.phoneType') }}
                    </label>
                </div>

                <div class="form-group group">
                    <svg class="svg-input w-5 top-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7.978 4a2.553 2.553 0 0 0-1.926.877C4.233 6.7 3.699 8.751 4.153 10.814c.44 1.995 1.778 3.893 3.456 5.572 1.68 1.679 3.577 3.018 5.57 3.459 2.062.456 4.115-.073 5.94-1.885a2.556 2.556 0 0 0 .001-3.861l-1.21-1.21a2.689 2.689 0 0 0-3.802 0l-.617.618a.806.806 0 0 1-1.14 0l-1.854-1.855a.807.807 0 0 1 0-1.14l.618-.62a2.692 2.692 0 0 0 0-3.803l-1.21-1.211A2.555 2.555 0 0 0 7.978 4Z"/>
                    </svg>

                    <input
                        required
                        type="tel"
                        placeholder=" "
                        class="input peer"
                        x-model="phones[index].number"
                        x-mask="+380999999999"
                        :id="$id('phone', '_number' + index)"
                        :class="{ 'input-error border-red-500': errors[`legalEntityForm.owner.phones.${index}.number`] }"
                    />

                    <template x-if="errors[`legalEntityForm.owner.phones.${index}.number`]">
                        <p class="text-error" x-text="errors[`legalEntityForm.owner.phones.${index}.number`]"></p>
                    </template>

                    <label :for="$id('phone', '_number' + index)" class="label z-10">
                        {{ __('forms.phone') }}
                    </label>
                </div>

                <template x-if="phones.length > 1 && index > 0">
                    <button x-on:click.prevent="phones.splice(index, 1)" {{-- Remove a phone if button is clicked --}}
                        class="item-remove justify-self-start text-xs"
                    >
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
                        </svg>

                        {{__('forms.remove_phone')}}
                    </button>
                </template>
            </div>
        </template>

        <button x-on:click.prevent="phones.push({ type: '', number: '' })" {{-- Add new phone if button is clicked --}}
                class="item-add mb-4"
                :class="{ 'lg:justify-self-start': index > 0 }" {{-- Apply this style only if it's not a first phone group --}}
        >
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
            </svg>

            {{__('forms.add_phone')}}
        </button>
    </div>

    {{-- Owner IPN --}}
    <div class='form-row-3'>
        <div class="form-group group relative z-0">
            <input
                type="text"
                id="taxId"
                name="taxId"
                maxlength="10"
                placeholder=" "
                wire:model="legalEntityForm.owner.taxId"
                {{-- aria-describedby="{{ $hasOwnerTaxId ? 'ownerTaxIdErrorHelp' : '' }}" --}}
                class="input {{ $hasOwnerTaxId ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasOwnerTaxId)
                <p id="ownerTaxIdErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.owner.taxId') }}
                </p>
            @endif

            <label for="taxId" class="label z-10">
                {{ __('forms.number') }} {{ __('forms.ipn') }} / {{ __('forms.rnokpp') }}
            </label>
        </div>

    </div>

    <div class='form-row-3'>
        {{-- Owner Document Type --}}
        <div class="form-group group relative z-0">
            <select
                required
                id="documentType"
                wire:model.defer="legalEntityForm.owner.documents.type"
                {{-- aria-describedby="{{ $hasOwnerDocumentType ? 'ownerDocumentTypeErrorHelp' : '' }}" --}}
                class="input-select text-gray-800 {{ $hasOwnerDocumentType ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            >
                <option value="_placeholder_" selected hidden>-- {{ __('Обрати тип') }} --</option>

                @foreach($dictionaries['DOCUMENT_TYPE'] as $k_d => $document_type)
                    <option value="{{ $k_d }}">{{ $document_type }}</option>
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
                {{-- aria-describedby="{{ $hasOwnerDocumentNumber ? 'ownerDocumentNumberErrorHelp' : '' }}" --}}
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

        {{-- Owner Document Issued By --}}
        <div class="form-group group relative z-0">
            <input
                type="text"
                placeholder=" "
                id="documentsIssuedBy"
                wire:model="legalEntityForm.owner.documents.issuedBy"
                {{-- aria-describedby="{{ $hasOwnerDocumentIssuedBy ? 'ownerDocumentIssuedByErrorHelp' : '' }}" --}}
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
                wire:model="legalEntityForm.owner.documents.issuedAt"
                {{-- aria-describedby="{{ $hasOwnerDocumentIssuedAt ? 'ownerDocumentIssuedByErrorHelp' : '' }}" --}}
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
