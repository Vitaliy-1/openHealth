<fieldset
    class="fieldset"
    xmlns="http://www.w3.org/1999/html"
    x-data="{ title: '{{ __('forms.address') }}', index: 4 }"
    x-init="typeof addHeader !== 'undefined' && addHeader(title, index)"
    x-show="activeStep === index  || isEdit"
    x-cloak
    :key="`step-${index}`"
>
    <template x-if="isEdit">
        <legend x-text="title" class="legend"></legend>
    </template>

    <div>
        <x-forms.addresses-search
            :address="$address"
            :districts="$districts"
            :settlements="$settlements"
            :streets="$streets"
            class="mb-4 form-row-3"
        />
    </div>
</fieldset>
