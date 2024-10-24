
<x-forms.form-row >
    <x-forms.form-group class="xl:w-1/4 ">
        <x-slot name="label">
            <x-forms.label class="default-label" for="edrpou" name="label" >
                {{__('forms.edrpou_rnokpp')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input disabled="{{!empty(auth()->user()->legal_entity_id )? 'disabled' : ''}}"  maxlength="10" class="default-input" value="{{$edrpou ?? ''}}" wire:model="legal_entity_form.edrpou" type="text" id="edrpou"/>
        </x-slot>
        @error('legal_entity_form.edrpou')
        <x-slot name="error">
            <x-forms.error name="message">
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>
