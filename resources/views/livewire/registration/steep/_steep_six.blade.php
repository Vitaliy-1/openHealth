<x-slot name="title">
    {{  __('5. Ліцензія') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_license_type"
                           name="label">
                {{__('Категорія')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select readonly disabled class="default-input"
                            wire:model="form.accreditation.category"
                            type="text" id="form_accreditation_category">
                <x-slot name="option">
                    @foreach($dictionaries['LICENSE_TYPE'] as $k =>$license_type)
                        <option
                            {{$k === 'MSP' ? 'selected' : ''}}  value="{{$k}}">{{$license_type}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_license_license_number"
                           name="label">
                {{__('Cерія та/або номер ліцензії НМП')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.license_license_number"
                           type="text" id="form_license_number"/>
        </x-slot>
        @error('form.license_license_number')
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
            <x-forms.label class="default-label" for="form_license_issued_at"
                           name="label">
                {{__('Ким видано ліцензію НМП')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.license.issued_at"
                           type="text" id="form_license_issued_by"/>
        </x-slot>

    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_license_issued_at"
                           name="label">
                {{__('Дата видачі ліцензії НМП')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.license.issued_at"
                           type="text" id="form_license_license_issued_at"/>
        </x-slot>
    </x-forms.form-group>

</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_license_license_active_from_date"
                           name="label">
                {{__('Дата початку дії ліцензії НМП')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.form_license_active_from_date"
                           type="text" id="form_license_license_active_from_date"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_license_expiry_date"
                           name="label">
                {{__('Дата завершення дії ліцензії НМП')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.license.expiry_date"
                           type="text" id="form_license_expiry_date"/>
        </x-slot>
    </x-forms.form-group>

</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_license_what_licensed"
                           name="label">
                {{__('Напрям діяльності, що ліцензовано')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.form_license_what_licensed"
                           type="text" id="form_license_what_licensed"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_license_expiry_date"
                           name="label">
                {{__('Дата завершення дії ліцензії НМП')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.form_license_expiry_date"
                           type="text" id="form_license_license_expiry_date"/>
        </x-slot>
    </x-forms.form-group>

</div>
