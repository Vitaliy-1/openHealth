
<x-forms.form-row >
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label for="email" class="default-label">
                {{__('forms.email')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="legal_entity_form.email" type="text" id="email"
            />
        </x-slot>
        @error('legal_entity_form.email')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/4">
        <x-slot name="label">
            <x-forms.label for="website" class="default-label">
                {{__('forms.website')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="legal_entity_form.website" type="text"
                           id="website"/>
        </x-slot>
        @error('legal_entity_form.website')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

</x-forms.form-row>

<x-forms.form-row  :cols="'flex-col'">
    @if(isset($legal_entity_form->phones) && count($legal_entity_form->phones) > 0)
        @foreach($legal_entity_form->phones as $key=>$phone)
            <x-forms.form-group >
                <x-slot name="label">
                    <div name="label" id="last_name" class="default-label">
                        {{__('forms.legal_contact')}} *
                    </div>
                    <div class="flex-row flex gap-6 items-center">
                        <div class="w-1/5">
                            <x-forms.select wire:model.defer="legal_entity_form.phones.{{$key}}.type"
                                            class="default-select">
                                <x-slot name="option">
                                    <option>{{__('forms.typeMobile')}}</option>
                                    @foreach($this->dictionaries['PHONE_TYPE'] as $k=>$phone_type)
                                        <option
                                            {{ isset ($phone['type']) === $phone_type ? 'selected': ''}} value="{{$k}}">{{$phone_type}}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>
                            @error("legal_entity_form.phones.{$key}.type")
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                            @enderror
                        </div>
                        <div class="w-1/5">
                            <x-forms.input value="{{$phone['number'] ?? ''}}"
                                           class="default-input"
                                           x-mask="+380999999999"
                                           wire:model="legal_entity_form.phones.{{$key}}.number" type="text"
                            />
                            @error("legal_entity_form.phones.{$key}.number")
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                            @enderror
                        </div>
                        <div class="w-1/5">
                            {{--                            @if($key != 0)--}}
                            {{--                                <a wire:click="removePhone({{$key}},'phones')"--}}
                            {{--                                   class="text-primary m-t-5"--}}
                            {{--                                   href="#">{{__('forms.removePhone')}}</a>--}}
                            {{--                            @endif--}}

                        </div>
                    </div>
                </x-slot>
            </x-forms.form-group>
        @endforeach
    @else
        <x-forms.form-group >
            <x-slot name="label">
                <div class="flex-row flex gap-6 items-center">
                    <div class="w-1/5">
                        <x-forms.select wire:model.defer="legal_entity_form.phones.0.type"
                                        class="default-select">
                            <x-slot name="option">
                                <option>{{__('forms.typeMobile')}}</option>
                                @foreach($this->dictionaries['PHONE_TYPE'] as $k=>$phone_type)
                                    <option
                                        {{ isset ($phone['type']) === $phone_type ? 'selected': ''}} value="{{$k}}">{{$phone_type}}</option>
                                @endforeach
                            </x-slot>
                        </x-forms.select>
                        @error("legal_entity_form.phones.0.type")
                        <x-forms.error>
                            {{$message}}
                        </x-forms.error>
                        @enderror
                    </div>
                    <div class="w-1/5">
                        <x-forms.input value=""
                                       class="default-input"
                                       x-mask="+380999999999"
                                       wire:model="legal_entity_form.phones.0.number" type="text"
                        />
                        @error("legal_entity_form.phones.0.number")
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                        @enderror
                    </div>
                </div>
            </x-slot>
        </x-forms.form-group>
    @endif


</x-forms.form-row>
