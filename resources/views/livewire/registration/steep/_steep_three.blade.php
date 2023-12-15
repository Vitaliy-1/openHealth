<x-slot name="title">
    {{  __('3. Інформаціяпро заклад') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="form_email" class="default-label">
                {{__('E-mail')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.email" type="text" id="form_email"
                           placeholder="{{__('E-mail')}}"/>
        </x-slot>
        @error('form.email')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="form_website" class="default-label">
                {{__('Вебсайт')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.website" type="text"
                           id="form_website" placeholder="{{__('Вебсайт')}}"/>
        </x-slot>

    </x-forms.form-group>
</div>
<div class="mb-4.5  gap-0 gap-6 ">
    <x-forms.label name="label" id="last_name" class="default-label">
        {{__('Контактні телефони керівника НМП')}} *
    </x-forms.label>
    @foreach($form['phones'] as $keyOwner=>$phoneOwner)
        <x-forms.form-group class="mb-2">
            <x-slot name="label">
                <div class="flex-row flex gap-6 items-center">
                    <div class="w-1/4">
                        <x-forms.select wire:model.defer="form.phones.{{$keyOwner}}.type"
                                        class="default-select">
                            <x-slot name="option">
                                <option>{{__('Обрати тип')}}</option>
                                @foreach($dictionaries['PHONE_TYPE'] as $k=>$phone_type)
                                    <option value="{{$k}}">{{$phone_type}}</option>
                                @endforeach

                            </x-slot>
                        </x-forms.select>
                        @error("form.phones.{$keyOwner}.type")
                        <x-forms.error>
                            {{$message}}
                        </x-forms.error>
                        @enderror
                    </div>
                    <div class="w-1/2">
                        <x-forms.input x-mask="38099 999 99 99" class="default-input"
                                       wire:model="form.phones.{{$keyOwner}}.phone" type="text"
                                       name="second_name"
                                       placeholder="{{__('+ 3(80)00 000 00 00 ')}}"/>
                        @error("form.phones.{$keyOwner}.phone")
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                        @enderror
                    </div>
                    <div class="w-1/4">
                        @if($keyOwner != 0)
                            <a wire:click="removePhonesForGeneral({{$keyOwner}})"
                               class="text-primary m-t-5"
                               href="#">{{__('Видалити номер')}}</a>
                        @endif
                    </div>
                </div>
            </x-slot>

        </x-forms.form-group>
    @endforeach
    <a wire:click="addRowPhonesForGeneral" class="text-primary m-t-5"
       href="#">{{__('Додати номер')}}</a>

</div>
