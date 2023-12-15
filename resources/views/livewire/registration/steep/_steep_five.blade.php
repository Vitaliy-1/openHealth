<x-slot name="title">
    {{  __('4. Акредитація') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_accreditation_category"
                           name="label">                                     {{__('Категорія')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select class="default-input" wire:model="form.accreditation.category"
                            type="text" id="form_accreditation_category">
                <x-slot name="option">
                    <option value="">Вибрати</option>
                    <option value="FIRST">Перша категорія</option>
                    <option value="SECOND">Друга категорія</option>
                    <option value="HIGHEST">Вища категорія</option>
                    <option value="NO_ACCREDITATION">Без аккредитації</option>
                </x-slot>
            </x-forms.select>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_accreditation_order_no"
                           name="label">
                {{__('Номер наказу МОЗ')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.accreditation.order_no"
                           type="text" id="form_accreditation_order_no"/>
        </x-slot>
    </x-forms.form-group>
</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_accreditation_issued_date"
                           name="label">
                {{__('Дата видачі')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.accreditation.issued_date"
                           type="date" id="form_accreditation_issued_date"/>
        </x-slot>
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_accreditation_expiry_date"
                           name="label">
                {{__('Термін придатності')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.accreditation.expiry_date"
                           type="date" id="form_accreditation_expiry_date"/>
        </x-slot>
    </x-forms.form-group>

</div>
<div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label class="default-label" for="form_accreditation_order_date"
                           name="label">
                {{__('Дата наказу МОЗ')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="form.accreditation.order_date"
                           type="date" id="form_accreditation_order_date"/>
        </x-slot>
    </x-forms.form-group>
</div>
