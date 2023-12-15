
<x-slot name="title">
    {{  __('2. Інформація про керівника') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="form_owner_last_name" class="default-label">
                {{__('Прізвище')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.owner.last_name" type="text"
                           id="form_owner_last_name" placeholder="{{__('Прізвище')}}"/>
        </x-slot>
        @error('form.owner.last_name')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="form_owner_first_name" class="default-label">
                {{__('Ім\'я')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.owner.first_name" type="text"
                           id="form_owner_first_name" placeholder="{{__('Ім\'я')}}"/>
        </x-slot>
        @error('form.owner.first_name')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="form_owner_second_name" id="second_name" class="default-label">
                {{__('По батькові')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.owner.second_name" type="text"
                           id="form_owner_second_name" placeholder="{{__('По батькові')}}"/>
        </x-slot>
        @error('form.owner.second_name')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="form_owner_birth_date" class="default-label">
                {{__('Дата народження')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.owner.birth_date" type="date"
                           id="form_owner_birth_date"/>
        </x-slot>
        @error('form.owner.birth_date')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

</div>
<div class="mb-4.5 flex flex-col  gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="form_owner_email" id="form_owner_email" class="default-label">
                {{__('E-mail')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.owner.email" type="text"
                           id="form_owner_email" placeholder="{{__('E-mail')}}"/>
        </x-slot>
        @error('form.owner.email')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="form_owner_position" class="default-label">
                {{__('Посада керівника НМП')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.owner.position" type="text"
                           id="form_owner_position" placeholder="{{__('Посада керівника НМП')}}"/>
        </x-slot>
        @error('form.owner.position')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</div>
<div class="mb-4.5 flex flex-col gap-0 ">
    <x-forms.label class="default-label" name="label" id="second_name">
        {{__('Стать')}}
    </x-forms.label>
    <x-forms.form-group class="flex items-center mb-4 flex-row-reverse	justify-end	">
        <x-slot name="input">
            <x-forms.input wire:model="form.owner.gender" type="radio" value="Ч"
                           id="form_owner_gender_m"/>
        </x-slot>
        <x-slot name="label">
            <x-forms.label class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                           name="label" for="form_owner_gender_m" name="gender">
                {{__('Чоловіча')}}
            </x-forms.label>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="flex items-center mb-4 flex-row-reverse	justify-end	">
        <x-slot name="input">
            <x-forms.input wire:model="form.owner.gender" value="Ж" type="radio"
                           id="form_owner_gender_g" name="gender"/>
        </x-slot>
        <x-slot name="label">
            <x-forms.label class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                           name="label" for="form_owner_gender_g">
                {{__('Жіноча')}}
            </x-forms.label>
        </x-slot>

    </x-forms.form-group>
    @error('form.owner.gender')
    <x-forms.error>
        {{$message}}
    </x-forms.error>
    @enderror
</div>
<div class="mb-4.5  gap-0 gap-6 ">
    <x-forms.label name="label" id="last_name" class="default-label">
        {{__('Контактні телефони керівника НМП')}} *
    </x-forms.label>
    @foreach($form['owner']['phones'] as $key=>$phone)
        <x-forms.form-group class="mb-2">
            <x-slot name="label">
                <div class="flex-row flex gap-6 items-center">
                    <div class="w-1/4">
                        <x-forms.select wire:model.defer="form.owner.phones.{{$key}}.type"
                                        class="default-select">
                            <x-slot name="option">
                                <option>{{__('Обрати тип')}}</option>
                                @foreach($this->dictionaries['PHONE_TYPE'] as $k=>$phone_type)
                                    <option value="{{$k}}">{{$phone_type}}</option>
                                @endforeach
                            </x-slot>
                        </x-forms.select>
                        @error("form.owner.phones.{$key}.type")
                        <x-forms.error>
                            {{$message}}
                        </x-forms.error>
                        @enderror
                    </div>
                    <div class="w-1/2">
                        <x-forms.input x-mask="38099 999 99 99" class="default-input"
                                       wire:model="form.owner.phones.{{$key}}.phone" type="text"
                                       id="second_name" name="second_name"
                                       placeholder="{{__('+ 3(80)00 000 00 00 ')}}"/>
                        @error("form.owner.phones.{$key}.phone")
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                        @enderror
                    </div>
                    <div class="w-1/4">
                        @if($key != 0)
                            <a wire:click="removePhonesForOwner({{$key}})"
                               class="text-primary m-t-5"
                               href="#">{{__('Видалити номер')}}</a>
                        @endif

                    </div>
                </div>
            </x-slot>

        </x-forms.form-group>
    @endforeach

    <a wire:click="addRowPhonesForOwner" class="text-primary m-t-5"
       href="#">{{__('Додати номер')}}</a>

</div>
<div x-data="{ show: false }">
    <div class="mb-4.5 flex flex-col gap-0 gap-6 ">
        <x-forms.form-group class="flex items-center  flex-row-reverse	justify-end	">
            <x-slot name="input">
                <x-forms.input x-bind:checked="!show" @change="show = !show"
                               wire:model="form.owner.invalid_tax_id" type="checkbox"
                               id="invalid_tax_id" name="invalid_tax_id"/>
            </x-slot>
            <x-slot name="label">
                <x-forms.label for="invalid_tax_id"
                               class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{__('РНОКПП')}}
                </x-forms.label>
            </x-slot>
            @error('form.owner.invalid_tax_id')
            <x-slot name="error">
                <x-forms.error>
                    {{$message}}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>
    <div x-show="!show">
        <div class="mb-4.5 flex flex-col gap-0 gap-6 ">
            <x-forms.form-group class="xl:w-1/2">
                <x-slot name="label">
                    <x-forms.label class="default-label" for="tax_id">
                        {{__('Номер РНОКПП')}}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" checked wire:model="form.owner.tax_id"
                                   type="text" id="tax_id" name="tax_id"
                                   placeholder="{{__('Номер РНОКПП')}}"/>
                </x-slot>
                @error('form.owner.tax_id')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </div>
    </div>
    <div x-show="show">
        <div class="mb-4.5 flex flex-col gap-6   xl:flex-row">
            <x-forms.form-group class="xl:w-1/2">
                <x-slot name="label">
                    <x-forms.label for="documents_type" class="default-label">
                        {{__('Тип документа')}} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.select id="documents_type" wire:model.defer="form.owner.documents.type"
                                    class="default-select">
                        <x-slot name="option">
                            <option>{{__('Обрати тип')}}</option>

                            <option value="PASPORT">{{__('Паспорт')}}</option>
                        </x-slot>
                    </x-forms.select>
                </x-slot>
                @error('form.owner.documents.type')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/2">
                <x-slot name="label">
                    <x-forms.label for="documents_number" class="default-label">
                        {{__('Cерія/номер документа')}} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="form.owner.documents.number"
                                   type="text" id="documents_number" name="documents[number]"
                                   placeholder="{{__('Cерія/номер документа')}}"/>
                </x-slot>
                @error('form.owner.documents.number')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </div>
        <div class="mb-4.5 flex flex-col gap-6   xl:flex-row">
            <x-forms.form-group class="xl:w-1/2">
                <x-slot name="label">
                    <x-forms.label for="documents_issued_by" class="default-label">
                        {{__('Орган яким виданий документ')}} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="form.owner.documents.issued_by"
                                   type="text" id="documents_issued_by"
                                   placeholder="{{__('Орган яким виданий документ')}}"/>
                </x-slot>
                @error('form.owner.documents.issued_by')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/2">
                <x-slot name="label">
                    <x-forms.label for="documents_issued_at" class="default-label">
                        {{__('Дата видачі документа')}}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="form.owner.documents.issued_at"
                                   type="text" id="documents_issued_at"
                                   placeholder="{{__('Дата видачі документа')}}"/>
                </x-slot>
                @error('form.owner.documents.issued_at')
                <x-slot name="message">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>

        </div>
    </div>
</div>
