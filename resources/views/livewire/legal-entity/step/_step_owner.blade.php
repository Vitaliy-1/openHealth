<x-slot name="title">
    {{  __('2. Інформація про керівника') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="owner_last_name" class="default-label">
                {{__('forms.last_name')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.owner.last_name" type="text"
                           id="owner_last_name"/>
        </x-slot>
        @error('legal_entity_form.owner.last_name')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="owner_first_name" class="default-label">
                {{__('forms.first_name')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.owner.first_name" type="text"
                           id="owner_first_name"/>
        </x-slot>
        @error('legal_entity_form.owner.first_name')
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
            <x-forms.label for="owner_second_name" class="default-label">
                {{__('forms.second_name')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.owner.second_name" type="text"
                           id="owner_second_name"/>
        </x-slot>
        @error('legal_entity_form.owner.second_name')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="owner_birth_date" class="default-label">
                {{__('forms.birth_date')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.owner.birth_date" type="date"
                           id="owner_birth_date"/>
        </x-slot>
        @error('legal_entity_form.owner.birth_date')
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
            <x-forms.label for="owner_email" id="owner_email" class="default-label">
                {{__('forms.email')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entity_form.owner.email" type="text"
                           id="owner_email" placeholder="{{__('E-mail')}}"/>
        </x-slot>
        @error('legal_entity_form.owner.email')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="owner_position" class="default-label">
                {{__('forms.owner_position')}}*
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="legal_entity_form.owner.position" type="text"
                id="owner_position"
            >
                <x-slot name="option">
                    <option>{{__('forms.select_position')}}</option>
                    @foreach($this->dictionaries['POSITION'] as $k=>$position)
                        <option value="{{$k}}">{{$position}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>

        </x-slot>
        @error('legal_entity_form.owner.position')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</div>
<div class="mb-4.5 flex flex-col gap-0 ">
    <x-forms.label class="default-label" name="label">
        {{__('forms.gender')}} *
    </x-forms.label>
    @isset($this->dictionaries['GENDER'])
        @foreach($this->dictionaries['GENDER'] as $k=>$gender)
            <x-forms.form-group class="flex items-center mb-4 flex-row-reverse	justify-end	">
                <x-slot name="input">
                    <x-forms.input name="gender" wire:model="legal_entity_form.owner.gender" type="radio" value="{{$k}}"
                                   id="owner_gender_{{$k}}"/>
                </x-slot>
                <x-slot name="label">
                    <x-forms.label class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                                   name="label" for="owner_gender_{{$k}}">
                        {{$gender}}
                    </x-forms.label>
                </x-slot>
            </x-forms.form-group>
        @endforeach
    @endisset
    @error('legal_entity_form.owner.gender')
    <x-forms.error>
        {{$message}}
    </x-forms.error>
    @enderror
</div>
<div class="mb-4.5  gap-0 gap-6 ">
    <x-forms.label name="label" class="default-label">
        {{__('forms.phonesOwner')}} *
    </x-forms.label>
    @if(isset($legal_entity_form->owner['phones']))
        @foreach($legal_entity_form->owner['phones'] as $key=>$phone)
            <x-forms.form-group class="mb-2">
                <x-slot name="label">
                    <div class="flex-row flex gap-6 items-center">
                        <div class="w-1/4">
                            <x-forms.select wire:model.defer="legal_entity_form.owner.phones.{{$key}}.type"
                                            class="default-select">
                                <x-slot name="option">
                                    <option>{{__('forms.typeMobile')}}</option>
                                    @foreach($this->dictionaries['PHONE_TYPE'] as $k=>$phone_type)
                                        <option value="{{$k}}">{{$phone_type}}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>
                            @error("legal_entity_form.owner.phones.{$key}.type")
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                            @enderror
                        </div>
                        <div class="w-1/2">
                            <x-forms.input x-mask="+380999999999" class="default-input"
                                           wire:model="legal_entity_form.owner.phones.{{$key}}.number" type="text"
                                           placeholder="{{__('+ 3(80)00 000 00 00 ')}}"/>
                            @error("legal_entity_form.owner.phones.{$key}.number")
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                            @enderror
                        </div>
                        <div class="w-1/4">
                            @if($key != 0)
                                <a wire:click="removePhone({{$key}},'owner')"
                                   class="text-primary m-t-5"
                                   href="#">{{__('forms.removePhone')}}</a>
                            @endif

                        </div>
                    </div>
                </x-slot>
            </x-forms.form-group>
        @endforeach
    @else
        <x-forms.form-group class="mb-2">
            <x-slot name="label">
                <div class="flex-row flex gap-6 items-center">
                    <div class="w-1/4">
                        <x-forms.select wire:model.defer="legal_entity_form.owner.phones.0.type"
                                        class="default-select">
                            <x-slot name="option">
                                <option>{{__('forms.typeMobile')}}</option>
                                @foreach($this->dictionaries['PHONE_TYPE'] as $k=>$phone_type)
                                    <option value="{{$k}}">{{$phone_type}}</option>
                                @endforeach
                            </x-slot>
                        </x-forms.select>
                        @error("legal_entity_form.owner.phones.0.type")
                        <x-forms.error>
                            {{$message}}
                        </x-forms.error>
                        @enderror
                    </div>
                    <div class="w-1/2">
                        <x-forms.input x-mask="+380999999999" class="default-input"
                                       wire:model="legal_entity_form.owner.phones.0.number" type="text"
                                       placeholder="{{__('+ 3(80)00 000 00 00 ')}}"/>
                        @error("legal_entity_form.owner.phones.0.number")
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                        @enderror
                    </div>

                </div>
            </x-slot>
        </x-forms.form-group>

    @endif

    <a wire:click="addRowPhone('owner')" class="text-primary m-t-5"
       href="#">{{__('forms.addPhone')}}</a>
</div>
<div x-data="{ show: false }">
{{--    <div class="mb-4.5 flex flex-col gap-0 gap-6 ">--}}
{{--        <x-forms.form-group class="flex items-center  flex-row-reverse	justify-end	">--}}
{{--            <x-slot name="input">--}}
{{--                <x-forms.input--}}
{{--                    x-bind:checked="show"--}}
{{--                    @change="show = !show; $wire.set('legal_entity_form.owner.tax_id', show ? '' : $wire.legal_entity_form.owner.tax_id)"--}}
{{--                    wire:model="legal_entity_form.owner.no_tax_id"--}}
{{--                    type="checkbox"--}}
{{--                    id="owner_no_tax_id"--}}
{{--                />--}}
{{--            </x-slot>--}}
{{--            <x-slot name="label">--}}
{{--                <x-forms.label for="owner_no_tax_id"--}}
{{--                               class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">--}}
{{--                    {{__('forms.other_documents')}}--}}
{{--                </x-forms.label>--}}
{{--            </x-slot>--}}
{{--            @error('legal_entity_form.owner.no_tax_id')--}}
{{--            <x-slot name="error">--}}
{{--                <x-forms.error>--}}
{{--                    {{$message}}--}}
{{--                </x-forms.error>--}}
{{--            </x-slot>--}}
{{--            @enderror--}}
{{--        </x-forms.form-group>--}}
{{--    </div>--}}
    <div x-show="!show" class="mb-4.5 flex flex-col gap-0 gap-6 ">
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label class="default-label" for="tax_id">
                    {{__('forms.number')}} {{__('forms.ipn_rnokpp')}}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input
                    maxlength="10"
                    class="default-input"
                    x-bind:value="show ? '' : @entangle('legal_entity_form.owner.tax_id')"
                    wire:model="legal_entity_form.owner.tax_id"
                    type="text"
                    id="tax_id"
                    name="tax_id"
                />
            </x-slot>
            @error('legal_entity_form.owner.tax_id')
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
                <x-forms.label for="documents_type" class="default-label">
                    {{__('forms.document_type')}} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.select id="documents_type" wire:model.defer="legal_entity_form.owner.documents.type"
                                class="default-select">
                    <x-slot name="option">
                        <option>{{__('Обрати тип')}}</option>
                        @foreach($this->dictionaries['DOCUMENT_TYPE'] as $k_d=>$document_type)
                            <option value="{{$k_d}}">{{$document_type}}</option>
                        @endforeach
                    </x-slot>
                </x-forms.select>
            </x-slot>
            @error('legal_entity_form.owner.documents.type')
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
                    {{__('forms.document_number')}} *
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="legal_entity_form.owner.documents.number"
                               type="text" id="documents_number"
                />
            </x-slot>
            @error('legal_entity_form.owner.documents.number')
            <x-slot name="error">
                <x-forms.error>
                    {{$message}}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
    </div>
    <div  class="mb-4.5 flex flex-col gap-6   xl:flex-row">
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="documents_issued_by" class="default-label">
                    {{__('forms.document_issued_by')}}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" wire:model="legal_entity_form.owner.documents.issued_by"
                               type="text" id="documents_issued_by"
                               placeholder="{{__('Орган яким виданий документ')}}"/>
            </x-slot>
            @error('legal_entity_form.owner.documents.issued_by')
            <x-slot name="error">
                <x-forms.error>
                    {{$message}}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label for="owner_documents_issued_at" class="default-label">
                    {{__('forms.document_issued_at')}}
                </x-forms.label>
            </x-slot>
            <x-slot name="input">
                <x-forms.input class="default-input" type="date" wire:model="legal_entity_form.owner.documents.issued_at"
                               id="owner_documents_issued_at"
                              />
            </x-slot>
            @error('legal_entity_form.owner.documents.issued_at')
            <x-slot name="message">
                <x-forms.error>
                    {{$message}}
                </x-forms.error>
            </x-slot>
            @enderror
        </x-forms.form-group>

    </div>
</div>

