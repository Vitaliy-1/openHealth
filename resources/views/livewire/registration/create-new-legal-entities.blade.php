<div>
    <x-section-title>
        <x-slot name="title">{{ __('Зарееструвати заклад ') }}</x-slot>
        <x-slot name="description">{{ __('Зарееструвати заклад ') }}</x-slot>
    </x-section-title>
    <x-forms.forms-section class="max-w-4xl m-auto" submit="register">


        <x-slot name="title">
            {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}
        </x-slot>


        <x-slot name="form">
            <div class="p-6.5">
                @if ($currentStep == 1)
                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label class="default-label" for="edrpou" name="label" id="edrpou">
                                    {{__('Єдрпоу')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input" wire:model="form.edrpou" type="text" id="edrpou" name="edrpou" placeholder="{{__('Єдрпоу')}}"/>
                            </x-slot>
                            @error('owner.form.edrpou')
                            <x-slot name="error">
                                <x-forms.error name="message">
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                    </div>
                @endif
                @if ($currentStep == 2)

                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <x-forms.form-group class="xl:w-1/2">
                            <x-slot name="label">
                                <x-forms.label for="last_name"  class="default-label">
                                    {{__('Прізвище')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input" wire:model="form.owner.last_name" type="text" id="last_name" name="last_name" placeholder="{{__('Прізвище')}}"/>
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
                                <x-forms.label  for="first_name" class="default-label">
                                    {{__('Ім\'я')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input" wire:model="form.owner.first_name" type="text" id="first_name" name="first_name" placeholder="{{__('Ім\'я')}}"/>
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
                                <x-forms.label  name="label" id="second_name" class="default-label">
                                    {{__('По батькові')}}
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input" wire:model="form.owner.second_name" type="text" id="second_name" name="second_name" placeholder="{{__('По батькові')}}"/>
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
                                <x-forms.label   id="birth_date" class="default-label">
                                    {{__('Дата народження')}}
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input" wire:model="form.owner.birth_date" type="date" id="birth_date" name="birth_date" />
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
                                <x-forms.label name="email" id="last_name" class="default-label">
                                    {{__('E-mail')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input" wire:model="form.email" type="text" id="email" name="email" placeholder="{{__('E-mail')}}"/>
                            </x-slot>
                            @error('owner.form.email')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                        <x-forms.form-group class="xl:w-1/2">
                            <x-slot name="label">
                                <x-forms.label name="label" id="last_name" class="default-label">
                                    {{__('Посада керівника НМП')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input" wire:model="form.owner.position" type="text" id="position" name="position" placeholder="{{__('Посада керівника НМП')}}"/>
                            </x-slot>
                            @error('form.owner..position')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                    </div>

                    <div class="mb-4.5 flex flex-col gap-0 ">
                        <x-forms.label class="default-label" name="label" id="second_name" >
                            {{__('Стать')}}
                        </x-forms.label>
                        <x-forms.form-group class="flex items-center mb-4 flex-row-reverse	justify-end	" >
                            <x-slot name="input">
                                <x-forms.input wire:model="form.owner.gender" type="radio" value="Ч" id="gender" name="gender" />
                            </x-slot>
                            <x-slot name="label">
                                <x-forms.label class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300" name="label" id="second_name" >
                                    {{__('Чоловіча')}}
                                </x-forms.label>
                            </x-slot>
                        </x-forms.form-group>
                        <x-forms.form-group class="flex items-center mb-4 flex-row-reverse	justify-end	" >
                            <x-slot name="input">
                                <x-forms.input wire:model="form.owner.gender" value="Ж"  type="radio" id="gender" name="gender"/>
                            </x-slot>
                            <x-slot name="label">
                                <x-forms.label class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                                               name="label" id="second_name">
                                    {{__('Жіноча')}}
                                </x-forms.label>
                            </x-slot>

                        </x-forms.form-group>
                        @error('form.owner..gender')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </div>
                    <div class="mb-4.5  gap-0 gap-6 ">
                        <x-forms.label name="label" id="last_name" class="default-label">
                            {{__('Контактні телефони керівника НМП')}} *
                        </x-forms.label>
                        @foreach($form['owner']['phones'] as $key=>$phone)
                            <x-forms.form-group class="mb-2" >
                                <x-slot name="label">
                                    <div class="flex-row flex gap-6 items-center">
                                        <div class="w-1/4">
                                            <x-forms.select  wire:model.defer="form.owner.phones.{{$key}}.type"  class="default-select">
                                                <x-slot name="option">
                                                    <option >{{__('Обрати тип')}}</option>
                                                    <option value="MOBILE_PHONE">{{__('Мобільний')}}</option>
                                                    <option value="PHONE">{{__('Стаціонарний')}}</option>
                                                </x-slot>
                                            </x-forms.select >
                                            @error("form.owner.phones.{$key}.type")
                                                <x-forms.error>
                                                    {{$message}}
                                                </x-forms.error>
                                            @enderror
                                        </div>
                                        <div class="w-1/2">
                                            <x-forms.input x-mask="38099 999 99 99" class="default-input" wire:model="form.owner.phones.{{$key}}.phone" type="text" id="second_name" name="second_name" placeholder="{{__('+ 3(80)00 000 00 00 ')}}"/>
                                            @error("form.owner.phones.{$key}.phone")
                                            <x-forms.error>
                                                {{ $message }}
                                            </x-forms.error>
                                            @enderror
                                        </div>
                                        <div class="w-1/4">
                                            @if($key != 0)
                                            <a wire:click="removeRowPhones({{$key}})" class="text-primary m-t-5"  href="#">{{__('Видалити номер')}}</a>
                                            @endif

                                        </div>
                                    </div>
                                </x-slot>

                            </x-forms.form-group>
                        @endforeach

                        <a wire:click="addRowPhones" class="text-primary m-t-5"  href="#">{{__('Додати номер')}}</a>

                    </div>


                    <div x-data="{ show: false }"  >
                        <div class="mb-4.5 flex flex-col gap-0 gap-6 ">
                            <x-forms.form-group  class="flex items-center  flex-row-reverse	justify-end	" >
                                <x-slot name="input">
                                    <x-forms.input x-bind:checked="!show"  @change="show = !show" wire:model="form.owner.invalid_tax_id" type="checkbox" id="invalid_tax_id" name="invalid_tax_id" />
                                </x-slot>
                                <x-slot name="label">
                                    <x-forms.label for="invalid_tax_id"  class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"   >
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
                        <div x-show="!show" >
                            <div class="mb-4.5 flex flex-col gap-0 gap-6 ">
                                <x-forms.form-group   class="xl:w-1/2">
                                    <x-slot name="label">
                                        <x-forms.label class="default-label" for="tax_id" >
                                            {{__('Номер РНОКПП')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input  class="default-input" checked wire:model="form.owner.tax_id" type="text" id="tax_id" name="tax_id" placeholder="{{__('Номер РНОКПП')}}"/>
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
                        <div x-show="show" >
                            <div class="mb-4.5 flex flex-col gap-6   xl:flex-row">
                                <x-forms.form-group class="xl:w-1/2">
                                    <x-slot name="label">
                                        <x-forms.label  for="documents_type" class="default-label">
                                            {{__('Тип документа')}} *
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.select id="documents_type"  wire:model.defer="form.owner.documents.type"  class="default-select">
                                            <x-slot name="option">
                                                <option >{{__('Обрати тип')}}</option>

                                                <option value="PASPORT">{{__('Паспорт')}}</option>
                                            </x-slot>
                                        </x-forms.select >
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
                                        <x-forms.label  for="documents_number"  class="default-label">
                                            {{__('Cерія/номер документа')}} *
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input class="default-input" wire:model="form.owner.documents.number" type="text" id="documents_number" name="documents[number]" placeholder="{{__('Cерія/номер документа')}}"/>
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
                                        <x-forms.label  for="documents_issued_by"  class="default-label">
                                            {{__('Орган яким виданий документ')}} *
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input class="default-input" wire:model="form.owner.documents.issued_by" type="text" id="documents_issued_by" placeholder="{{__('Орган яким виданий документ')}}"/>
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
                                        <x-forms.label  for="documents_issued_at"  class="default-label">
                                            {{__('Дата видачі документа')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input class="default-input" wire:model="form.owner.documents.issued_at" type="text" id="documents_issued_at"  placeholder="{{__('Дата видачі документа')}}"/>
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

                @endif
                @if ($currentStep == 3)
                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                        <x-forms.form-group class="">
                            <x-slot name="label">
                                <x-forms.label class="default-label" for="edrpou" name="label" id="edrpou">
                                    {{__('Єдрпоу')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input" wire:model="form.edrpou" type="text" id="edrpou" name="edrpou" placeholder="{{__('Єдрпоу')}}"/>
                            </x-slot>
                            @error('form.edrpou')
                            <x-slot name="error">
                                <x-forms.error name="message">
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                    </div>

                @endif
                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row justify-between	items-center ">

                    <div class="xl:w-1/4">
                        @if ($currentStep != 1 )
                            <x-button wire:click="decreaseStep()">
                                {{__('Назад')}}
                            </x-button>
                        @endif

                    </div>
                    <div class="xl:w-1/4">
                        <x-button class="btn-primary" wire:click="increaseStep()">
                            {{__('Далі')}}
                        </x-button>
                    </div>

                </div>
            </div>
        </x-slot>
    </x-forms.forms-section>
</div>

<script>

</script>
