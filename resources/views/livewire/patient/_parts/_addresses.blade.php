<fieldset class="fieldset">
    <legend class="legend">
        {{ __('forms.address') }}
    </legend>
    <x-forms.addresses-search
        :address="$address"
        :districts="$districts"
        :settlements="$settlements"
        :streets="$streets"
        class="mt-8 form-row-3"
    />
</fieldset>
