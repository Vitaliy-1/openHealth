<x-forms.form-row :cols="'flex-col'" class="">

    <x-forms.form-group>
        <x-slot name="label">
            <x-forms.label class="default-label" for="knedp"
                           name="label">
                {{__('forms.knedp')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select class="default-input"
                            wire:model="form.knedp"
                            id="knedp">
                <x-slot name="option">
                    <option value="">{{__('forms.select')}}</option>
                    @foreach($certificateAuthorities as $k => $certificate_type)
                        <option value="{{ $certificate_type['id'] }}">{{ $certificate_type['name'] }}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>
        </x-slot>
        @error('form.knedp')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

    <x-forms.form-group class="">
        <x-slot name="label">
            <x-forms.label class="default-label" for="keyContainerUpload"
                           name="label">
                {{__('forms.key_container_upload')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.file wire:model="form.keyContainerUpload"
                          :id="'keyContainerUpload'"
                          :file="$this->form->keyContainerUpload?->getClientOriginalName()"
            />
        </x-slot>
        @error('form.keyContainerUpload')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="">
        <x-slot name="label">
            <x-forms.label class="default-label" for="password"
                           name="label">
                {{__('forms.password')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.password"
                           type="password" id="password"/>
        </x-slot>
        @error('form.password')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>
