<div>
    <div x-show="showFilter"
         x-transition
         class="mt-6">
        <div class="w-[60%]">
            <h2 class="text-[20px] mb-[10px] font-bold dark:text-white">{{ __('forms.patient') }}</h2>
            <div class="grid gap-6 mb-6 md:grid-cols-4">
                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="declarations_filter_first_name">
                            {{__('forms.first_name')}}
                        </x-forms.label>
                    </x-slot>
                    <x-slot name="input">
                        <x-forms.input id="declarations_filter_first_name"
                                       wire:model.live="declarations_filter.first_name" type="text"
                                       autocomplete="off"/>
                    </x-slot>
                </x-forms.form-group>
                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="declarations_filter_last_name">
                            {{__('forms.last_name')}}
                        </x-forms.label>
                    </x-slot>
                    <x-slot name="input">
                        <x-forms.input id="declarations_filter_last_name"
                                       wire:model.live="declarations_filter.last_name" type="text"
                                       autocomplete="off"/>
                    </x-slot>
                </x-forms.form-group>
                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="declarations_filter_second_name">
                            {{__('По батькові')}}
                        </x-forms.label>
                    </x-slot>
                    <x-slot name="input">
                        <x-forms.input id="declarations_filter_second_name"
                                       wire:model.live="declarations_filter.second_name" type="text"
                                       autocomplete="off"/>
                    </x-slot>
                </x-forms.form-group>
                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="declarations_filter_declaration_number">
                            {{__('Номер декларації')}}
                        </x-forms.label>
                    </x-slot>
                    <x-slot name="input">
                        <x-forms.input id="declarations_filter_declaration_number"
                                       wire:model.live="declarations_filter.declaration_number" type="text"
                                       autocomplete="off"/>
                    </x-slot>
                </x-forms.form-group>
                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="declarations_filter_phone">
                            {{__('Телефон')}}
                        </x-forms.label>
                    </x-slot>
                    <x-slot name="input">

                        <x-forms.input id="declarations_filter_phone"
                                       wire:model.live="declarations_filter.phone" type="text"
                                       autocomplete="off"/>
                    </x-slot>
                </x-forms.form-group>
                <x-forms.form-group>
                    <x-slot name="label">
                        <x-forms.label for="declarations_filter_birth_date">
                            {{__('Дата народження')}}
                        </x-forms.label>
                    </x-slot>
                    <x-slot name="input">
                        <x-forms.input-date id="declarations_filter_birth_date"
                                            wire:model.live="declarations_filter.birth_date" type="text"
                                            autocomplete="off"/>
                    </x-slot>
                </x-forms.form-group>
            </div>
        </div>
    </div>
</div>
