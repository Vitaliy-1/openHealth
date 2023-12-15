<x-slot name="title">
    {{  __('4.Адресса ') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_residence_address_area"
                           name="label">                                     {{__('Область')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select class="default-input" wire:model="form.residence_address.area"
                            type="text" id="form_residence_address_area">
                <x-slot name="option">
                    <option value="">Вибрати</option>
                    <option value="Київська">Київська</option>
                </x-slot>
            </x-forms.select>
        </x-slot> @error('form.residence_address.area')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_residence_address_region"
                           name="label">                                     {{__('Район')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select class="default-input" wire:model="form.residence_address.region"
                            type="text" id="form_residence_address_region">
                <x-slot name="option">
                    <option value="">Вибрати</option>
                    <option value="Київська">Київ</option>
                </x-slot>
            </x-forms.select>
        </x-slot>
        @error('form.residence_address.region')
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
            <x-forms.label class="default-label" for="form_residence_address_settlement_type"
                           name="label">
                {{__('Тип населеного пункту')}}
                *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select class="default-input"
                            wire:model="form.residence_address.settlement_type" type="text"
                            id="form_residence_address_settlement_type">
                <x-slot name="option">
                    <option value="">Вибрати</option>
                    <option value="Місто">Місто</option>
                    <option value="Село">Село</option>
                </x-slot>
            </x-forms.select>
        </x-slot>
        @error('form.residence_address.settlement_type')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_residence_address_settlement"
                           name="label">                                     {{__('Населений пунтк')}}
                *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select class="default-input" wire:model="form.residence_address.settlement"
                            type="text" id="form_residence_address_settlement">
                <x-slot name="option">
                    <option value="">Вибрати</option>
                    <option value="Київська">Київ</option>
                </x-slot>
            </x-forms.select>
        </x-slot> @error('form.residence_address.settlement')
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
            <x-forms.label class="default-label" for="form_residence_address_street"
                           name="label">                                     {{__('Вулиця')}}                                 </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="form.residence_address.street" type="text"
                           id="form_residence_address_settlement_type"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_residence_address_building"
                           name="label">                                     {{__('Будинок')}}                                 </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.residence_address.building"
                           type="text" id="form_residence_address_building"/>
        </x-slot>
    </x-forms.form-group>
</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_residence_address_apartment"
                           name="label">                                         {{__('Номер Квартири')}}                                     </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.residence_address.apartment"
                           type="text" id="form_residence_address_settlement_apartment"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_residence_address_zip"
                           name="label">                                         {{__('Поштовий індекс')}}                                     </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input x-mask="99999"
                           class="default-input" wire:model="form.residence_address.zip"
                           type="text" id="form_residence_address_settlement_zip"/>
        </x-slot>
    </x-forms.form-group>
</div>
