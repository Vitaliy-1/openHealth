<x-forms.form-row :cols="'flex-col'">
    <x-forms.form-group class="xl:w-1/3">
        <x-slot name="label">
            <x-forms.label class="default-label" for="knedp"
                           name="label">
                {{__('forms.KNEDP')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select class="default-input"
                            wire:model="knedp"
                            id="knedp">
                <x-slot name="option">
                    <option value="">{{__('forms.select')}}</option>
                    @foreach($getCertificateAuthority as $k =>$certificate_type)
                        <option value="{{$certificate_type['id']}}">{{$certificate_type['name']}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>
        </x-slot>

        @error('knedp')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

    <x-forms.form-group class="xl:w-1/3">
        <x-slot name="label">
            <x-forms.label class="default-label" for="keyContainerUpload"
                           name="label">
                {{__('forms.keyContainerUpload')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.file wire:model="file"
                          :id="'keyContainerUpload'"/>
        </x-slot>
        @error('keyContainerUpload')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

    <x-forms.form-group class="xl:w-1/3">
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
        @error('password')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/3">
        <x-slot name="input">
            <div class="flex items-center mb-4">
                <x-forms.checkbox wire:model="legal_entity_form.public_offer.consent" value="true" type="checkbox"
                                  id="public_offer_consent"/>
                <label for="public_offer_consent"
                       class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{__('forms.agree')}}
                </label>
            </div>
        </x-slot>
        @error('legal_entity_form.public_offer.consent')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

</x-forms.form-row>
