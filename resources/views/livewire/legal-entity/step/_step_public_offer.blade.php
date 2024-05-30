<x-slot name="title">
    {{  __('Згода') }}
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-container">

    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="knedp"
                           name="label">
                {{__('forms.KNEDP')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select   class="default-input"
                            wire:model="knedp"
                            id="knedp">
                <x-slot name="option">
                    @foreach($getCertificateAuthority as $k =>$certificate_type)
                        <option   value="{{$certificate_type['id']}}">{{$certificate_type['name']}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>
        </x-slot>
        @error("knedp")
        <x-forms.error>
            {{$message}}
        </x-forms.error>
        @enderror
    </x-forms.form-group>

    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="keyContainerUpload"
                           name="label">
                {{__('forms.keyContainerUpload')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="keyContainerUpload"
                           type="file" id="keyContainerUpload"/>
        </x-slot>
        @error("keyContainerUpload")
        <x-forms.error>
            {{$message}}
        </x-forms.error>
        @enderror
    </x-forms.form-group>

    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="password"
                           name="label">
                {{__('forms.password')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="password"
                           type="password" id="password"/>
        </x-slot>
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
