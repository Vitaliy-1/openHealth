@php
    $hasContactEmailError = $errors->has('legalEntityForm.email');
    $hasWebsiteError = $errors->has('legalEntityForm.website');
@endphp

<fieldset
    class="fieldset"
    xmlns="http://www.w3.org/1999/html"
    x-data="{ title: '{{ __('forms.contacts') }}', index: 3 }"
    x-init="typeof addHeader !== 'undefined' && addHeader(title, index)"
    x-show="activeStep === index || isEdit"
    x-cloak
    :key="`step-${index}`"
>
    <template x-if="isEdit">
        <legend x-text="title" class="legend"></legend>
    </template>

    <div class='form-row-3'>
        {{-- Email --}}
        <div class="form-group group">
            <svg class="svg-input w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2.038 5.61A2.01 2.01 0 0 0 2 6v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6c0-.12-.01-.238-.03-.352l-.866.65-7.89 6.032a2 2 0 0 1-2.429 0L2.884 6.288l-.846-.677Z"/>
                <path d="M20.677 4.117A1.996 1.996 0 0 0 20 4H4c-.225 0-.44.037-.642.105l.758.607L12 10.742 19.9 4.7l.777-.583Z"/>
            </svg>

            <input
                required
                type="text"
                placeholder=" "
                id="contact_email"
                wire:model="legalEntityForm.email"
                value="{{ $legalEntityForm->email ?? '' }}"
                aria-describedby="{{ $hasContactEmailError ? 'contactEmailErrorHelp' : '' }}"
                class="input {{ $hasContactEmailError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasContactEmailError)
                <p id="contactEmailErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.email') }}
                </p>
            @endif

            <label for="contact_email" class="label z-10">
                {{ __('forms.email') }}
            </label>
        </div>

        {{-- Web Site --}}
        <div class="form-group group">
            <input
                type="text"
                placeholder=" "
                id="website"
                value="{{ $legalEntityForm->website ?? '' }}"
                wire:model="legalEntityForm.website"
                aria-describedby="{{ $hasWebsiteError ? 'websiteErrorHelp' : '' }}"
                class="input {{ $hasWebsiteError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
            />

            @if($hasWebsiteError)
                <p id="websiteErrorHelp" class="text-error">
                    {{ $errors->first('legalEntityForm.website') }}
                </p>
            @endif

            <label for="website" class="label z-10">
                {{ __('forms.website') }}
            </label>
        </div>
    </div>

    {{-- P H O N E --}}
    <div
        class='form-row mt-6'
        x-data="{ phones: $wire.entangle('legalEntityForm.phones') }"
        x-init="phones = phones.length > 0 ? phones : [{ type: '', number: '' }]"
        x-id="['phone']"
    >
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
                        :class="{ 'input-error border-red-500': errors[`legalEntityForm.phones.${index}.type`] }"
                    >
                        <option value="_placeholder_" selected hidden>-- {{ __('forms.type_mobile') }} --</option>

                        @foreach($dictionaries['PHONE_TYPE'] as $k => $phoneType)
                            <option value="{{ $k }}">{{ $phoneType }}</option>
                        @endforeach
                    </select>

                    <template x-if="errors[`legalEntityForm.phones.${index}.type`]">
                        <p class="text-error" x-text="errors[`legalEntityForm.phones.${index}.type`]"></p>
                    </template>

                    <label :for="$id('phone', '_type' + index)" class="label z-10">
                        {{ __('forms.phone_type') }}
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
                        :class="{ 'input-error border-red-500': errors[`legalEntityForm.phones.${index}.number`] }"
                    />

                    <template x-if="errors[`legalEntityForm.phones.${index}.number`]">
                        <p class="text-error" x-text="errors[`legalEntityForm.phones.${index}.number`]"></p>
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
                class="item-add"
                :class="{ 'lg:justify-self-start': index > 0 }" {{-- Apply this style only if it's not a first phone group --}}
        >
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
            </svg>

            {{__('forms.add_phone')}}
        </button>
    </div>

</fieldset>
