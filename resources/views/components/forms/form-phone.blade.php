@props(['phones' => ['number' => '', 'type' => ''], 'property' => ''])
@php
    $phones = !empty($phones) ? $phones : [['number' => '', 'type' => '']];
@endphp
<div>
    @if($phones && !empty($property))
        @foreach($phones as $key => $phone)
            <x-forms.form-row cols="flex-col xl:flex-row" gap="gap-4">
                <x-forms.form-group class="w-1/3">
                    <x-slot name="input">
                        <x-forms.select wire:model="{{ $property }}.phones.{{ $key }}.type"
                                        class="default-select">
                            <x-slot name="option">
                                <option>{{__('forms.typeMobile')}}</option>
                                @foreach($this->dictionaries['PHONE_TYPE'] as $k => $phoneType)
                                    <option value="{{ $k }}">{{ $phoneType }}</option>
                                @endforeach
                            </x-slot>
                        </x-forms.select>

                        @error("$property.phones.$key.type")
                        <x-slot name="error">
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-slot>
                </x-forms.form-group>

                <x-forms.form-group class="w-1/3">
                    <x-slot name="input">
                        <x-forms.input x-mask="+380999999999" class="default-input"
                                       wire:model="{{ $property }}.phones.{{ $key }}.number"
                                       type="text"
                                       placeholder="{{ __('+ 3(80)00 000 00 00 ') }}"/>
                    </x-slot>

                    @error("$property.phones.$key.type")
                    <x-slot name="error">
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                    </x-slot>
                    @enderror
                </x-forms.form-group>

                <x-forms.form-group class="w-1/4 flex items-center">
                    <x-slot name="input">
                        @if($key != 0)
                            <button wire:click="removePhone({{ $key }}, '{{ $property }}')"
                                    class="text-red-600 text-xs cursor-pointer">
                                {{__('forms.removePhone')}}
                            </button>
                        @endif
                    </x-slot>
                </x-forms.form-group>
            </x-forms.form-row>
        @endforeach
    @endif
    <button wire:click="addRowPhone('{{ $property }}')"
            class="text-xs inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline"
    >
        {{__('forms.addPhone')}}
    </button>
</div>
