<x-slot name="title">
    {{  __('3. Інформаціяпро заклад') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="owner_email" class="default-label">
                {{__('forms.email')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entities.contact.email" type="text" id="owner_email"
                         />
        </x-slot>
        @error('legal_entities.contact.email')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="owner_website" class="default-label">
                {{__('forms.website')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="website" type="text"
                           id="owner_website" />
        </x-slot>
    </x-forms.form-group>
</div>
<div class="mb-4.5  gap-0 gap-6 ">
    <x-forms.label name="label" id="last_name" class="default-label">
        {{__('forms.legal_contact')}} *
    </x-forms.label>
    @if($phones)
        @foreach($phones as $key=>$phone)
            <x-forms.form-group class="mb-2">
                <x-slot name="label">
                    <div class="flex-row flex gap-6 items-center">
                        <div class="w-1/4">
                            <x-forms.select wire:model.defer="legal_entities.contact.phones.{{$key}}.type" class="default-select">
                                <x-slot name="option">
                                    <option>{{__('forms.typeMobile')}}</option>
                                    @foreach($this->dictionaries['PHONE_TYPE'] as $k=>$phone_type)
                                        <option value="{{$k}}">{{$phone_type}}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>
                            @error("legal_entities.contact.phones.{$key}.type")
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                            @enderror
                        </div>
                        <div class="w-1/2">
                            <x-forms.input  x-mask="38099 999 99 99" class="default-input"
                                            wire:model="legal_entities.contact.phones.{{$key}}.phone" type="text"
                                            placeholder="{{__('+ 3(80)00 000 00 00 ')}}"/>
                            @error("legal_entities.contact.phones.{$key}.phone")
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                            @enderror
                        </div>
                        <div class="w-1/4">
                            @if($key != 0)
                                <a wire:click="removePhone({{$key}})"
                                   class="text-primary m-t-5"
                                   href="#">{{__('forms.removePhone')}}</a>
                            @endif

                        </div>
                    </div>
                </x-slot>
            </x-forms.form-group>
        @endforeach
    @endif
    <a wire:click="addRowPhone" class="text-primary m-t-5"
       href="#">{{__('Додати номер')}}</a>
</div>
