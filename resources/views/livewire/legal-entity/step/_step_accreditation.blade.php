<x-slot name="title">
    {{  __('4. Акредитація') }}
    <h3>  {{  __('Крок :currentSteep з :totalSteps', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}</h3>
</x-slot>
<div x-data="{ show_accreditation: false }">

    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
        <x-forms.form-group class="xl:w-1/2">
            <x-slot name="label">
                <x-forms.label class="default-label flex items-center" for="accreditation_show"

                               name="label">
                        <x-forms.input class="mr-1" x-bind:checked="show_accreditation"
                                       @change="show_accreditation = !show_accreditation"
                                       type="checkbox"
                                       id="accreditation_show"/>
                    {{__('forms.accreditation_show')}}
                </x-forms.label>
            </x-slot>

        </x-forms.form-group>
    </div>

    <div x-show="show_accreditation">
        <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
            <x-forms.form-group class="xl:w-1/2">
                <x-slot name="label">
                    <x-forms.label class="default-label" for="accreditation_category"
                                   name="label">
                        {{__('forms.accreditation_category')}}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.select class="default-input" wire:model="legal_entity_form.accreditation.category"
                                    type="text" id="accreditation_category">
                        <x-slot name="option">
                            <option value="">{{__('forms.select')}}</option>
                            @isset($dictionaries['ACCREDITATION_CATEGORY'])
                                @foreach($dictionaries['ACCREDITATION_CATEGORY'] as $k=>$category)
                                    <option {{isset($legal_entity_form->accreditation['category'] ) == $k ? 'selected': ''}} value="{{$k}}">{{$category}}</option>
                                @endforeach
                            @endif
                        </x-slot>
                    </x-forms.select>
                </x-slot>
                @error('legal_entity_form.accreditation.category')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/2">
                <x-slot name="label">
                    <x-forms.label class="default-label" for="accreditation_order_no"
                                   name="label">
                        {{ __('forms.accreditation_order_no')}}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="legal_entity_form.accreditation.order_no"
                                   type="text" id="accreditation_order_no"/>
                </x-slot>
                @error('legal_entity_form.accreditation.order_no')
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
                    <x-forms.label class="default-label" for="accreditation_issued_date"
                                   name="label">
                        {{__('forms.accreditation_issued_date')}}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="legal_entity_form.accreditation.issued_date"
                                   type="date" id="accreditation_issued_date"/>
                </x-slot>
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/2">
                <x-slot name="label">
                    <x-forms.label class="default-label" for="accreditation_expiry_date"
                                   name="label">
                        {{__('forms.accreditation_expired_date')}}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="legal_entity_form.accreditation.expiry_date"
                                   type="date" id="accreditation_expiry_date"/>
                </x-slot>
            </x-forms.form-group>
        </div>
        <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
            <x-forms.form-group class="xl:w-1/2">
                <x-slot name="label">
                    <x-forms.label class="default-label" for="accreditation_order_date"
                                   name="label">
                        {{__('forms.accreditation_order_date')}}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="legal_entity_form.accreditation.order_date"
                                   type="date" id="accreditation_order_date"/>
                </x-slot>
                @error('legal_entity_form.accreditation.order_date')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror

            </x-forms.form-group>
        </div>
    </div>
</div>
