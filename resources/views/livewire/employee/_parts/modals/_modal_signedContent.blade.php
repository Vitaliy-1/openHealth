<x-forms.form-row :cols="'flex-col'" class="">

    <x-forms.form-group>
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

    <x-forms.form-group class="">
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
    <x-forms.form-group class="">
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
</x-forms.form-row>
<x-forms.form-row class="">
    <div class="xl:w-1/2 text-left">
        <x-secondary-button wire:click="closeModalModel()">
            {{__('Назад')}}
        </x-secondary-button>
    </div>
    <div class="xl:w-1/2 text-right">
        <button wire:click="sendApiRequest()" type="button" class="default-button">
            {{__('Відправити на затвердження ')}}
        </button>
    </div>
</x-forms.form-row>

