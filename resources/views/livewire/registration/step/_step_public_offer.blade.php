<x-slot name="title">
    {{  __('Згода') }}
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-container">
    <x-forms.form-group class="flex items-center mb-4 flex-row-reverse	justify-end	">


        <x-slot name="label">
            <x-forms.label class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                           name="label" for="digital_signature">
                <div>
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        {{__('forms.digital_signature')}}
                    </label>
                    <x-forms.input class="w-full rounded-md border border-stroke p-3 outline-none transition file:mr-4 file:rounded file:border-[0.5px] file:border-stroke file:bg-[#EEEEEE] file:py-1 file:px-2.5 file:text-sm file:font-medium focus:border-primary file:focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:file:border-strokedark dark:file:bg-white/30 dark:file:text-white"
                                   wire:model="legal_entity_form.public_offer.digital_signature" value="true" type="file"
                                   id="digital_signature" />
                </div>
            </x-forms.label>

        </x-slot>
{{--        <x-slot name="error">--}}
{{--        @error("legal_entity_form.public_offer.digital_signature")--}}
{{--        <x-forms.error>--}}
{{--            {{$message}}--}}
{{--        </x-forms.error>--}}
{{--        @enderror--}}
{{--        </x-slot>--}}
    </x-forms.form-group>

    <x-forms.form-group class="flex items-center mb-4 flex-row-reverse	justify-end	">
        <x-slot name="input">
            <x-forms.input wire:model="legal_entity_form.public_offer.consent" value="true" type="checkbox"
                           id="public_offer_consent" name="gender"/>
        </x-slot>
        <x-slot name="label">
            <x-forms.label class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                           name="label" for="public_offer_consent">
                <a href="#">{{__('forms.agree')}}</a>
            </x-forms.label>
        </x-slot>
        @error("legal_entity_form.public_offer.consent")
        <x-forms.error>
            {{$message}}
        </x-forms.error>
        @enderror
    </x-forms.form-group>

</div>
