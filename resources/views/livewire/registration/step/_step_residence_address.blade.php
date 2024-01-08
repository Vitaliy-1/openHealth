<x-slot name="title">
    {{  __('4. Адресса ') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>

</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_area"
                           name="label">
                {{__('forms.region')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
                <x-forms.input class="default-input"
                               wire:model="legal_entities.residence_address.area" type="text"
                               id="residence_address_settlement_type"/>
        </x-slot>
        @error('legal_entities.residence_address.area')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_region"
                           name="label">
                {{__('forms.area')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entities.residence_address.region"
                            type="text" id="residence_address_region" />

        </x-slot>
        @error('legal_entities.residence_address.region')
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
            <x-forms.label class="default-label" for="residence_address_settlement_type"
                           name="label">
                {{__('forms.settlement_type')}} *

            </x-forms.label>
        </x-slot>
        <x-slot name="input">

            <x-forms.select
                class="default-input"
                wire:model="legal_entities.residence_address.settlement_type" type="text"
                id="residence_address_settlement_type"
            >
                <x-slot name="option">
                    <option value="">{{__('forms.select')}}</option>
                    @isset($dictionaries['SETTLEMENT_TYPE'])
                        @foreach($dictionaries['SETTLEMENT_TYPE'] as $k=>$type)
                            <option  {{ isset($legal_entities->residence_address['settlement_type']) == $k ? 'selected': ''}} value="{{$k}}">{{$type}}</option>
                        @endforeach
                    @endif
                </x-slot>
            </x-forms.select>
        </x-slot>


        @error('legal_entities.residence_address.settlement_type')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_settlement"
                           name="label">
                {{__('forms.settlement')}}
                *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entities.residence_address.settlement"
                            type="text" id="residence_address_settlement" />
        </x-slot>
        @error('legal_entities.residence_address.settlement')
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
            <x-forms.label class="default-label" for="residence_address_street"
                           name="label">
                {{__('forms.street')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="legal_entities.residence_address.street" type="text"
                           id="residence_address_settlement_type"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_building"
                           name="label">
                {{__('forms.building')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entities.residence_address.building"
                           type="text" id="residence_address_building"/>
        </x-slot>
    </x-forms.form-group>
</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_apartment"
                           name="label">
                {{__('forms.apartment')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="legal_entities.residence_address.apartment"
                           type="text" id="residence_address_settlement_apartment"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="residence_address_zip"
                           name="label">
                {{__('forms.zip_code')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input x-mask="99999"
                           class="default-input" wire:model="legal_entities.residence_address.zip"
                           type="text" id="residence_address_settlement_zip"/>
        </x-slot>
    </x-forms.form-group>
</div>
