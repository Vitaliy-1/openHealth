@php
    $hasEdrpouError = $errors->has('legalEntityForm.edrpou');
@endphp

<fieldset
    class="fieldset"
    xmlns="http://www.w3.org/1999/html"
    x-data="{
        title: '{{ __('forms.edrpou') }}',
        index: 1,
        isDisabled: @json(!empty(auth()->user()->legal_entity_id))
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
        <div class="form-group group" x-id="['edrpou']">
            <input
                required
                type="text"
                :id="$id('edrpou')"
                maxlength="10"
                placeholder=" "
                value="{{ $edrpou ?? '' }}"
                wire:model="legalEntityForm.edrpou"
                {{-- aria-describedby="{{ $hasEdrpouError ? 'edrpou_error_help' : '' }}" --}}
                class="input {{ $hasEdrpouError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"
                :class="isDisabled ? 'text-gray-400 border-gray-200 dark:text-gray-500' : 'text-gray-900 border-gray-300'"
                :disabled="isDisabled"
            />

            @if($hasEdrpouError)
                <p id="edrpou_error_help" class="text-error">
                    {{ $errors->first('legalEntityForm.edrpou') }}
                </p>
            @endif

            <label :for="$id('edrpou')" class="label z-10">
                {{__('forms.edrpouRnokpp')}}
            </label>
        </div>
    </div>
</fieldset>
