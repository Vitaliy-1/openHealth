<x-dialog-modal maxWidth="3xl" class="w-3 h-full" wire:model.live="showModal">

    <x-slot name="title">
        {{__('Додати місце надання послуг')}}
    </x-slot>
    <x-slot name="content">

        <x-forms.forms-section-modal submit="{{$mode === 'edit' ? 'update' : 'store'}}">
            <x-slot name="form">
                <div class="mb-4">
                    <div class="grid grid-cols-2 gap-6 ">
                        <div>
                        <div class="grid grid-cols-2 gap-6">
                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <x-forms.label for="name" class="default-label">
                                            {{__('forms.full_name_division')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input class="default-input"
                                                       wire:model="division.name" type="text"
                                                       id="name"/>
                                    </x-slot>
                                    @error('division.name')
                                    <x-slot name="error">
                                        <x-forms.error>
                                            {{$message}}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>
                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <x-forms.label for="email" class="default-label">
                                            {{__('forms.email')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input class="default-input"
                                                       wire:model="division.email" type="text"
                                                       id="email"/>
                                    </x-slot>
                                    @error('division.email')
                                    <x-slot name="error">
                                        <x-forms.error>
                                            {{$message}}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>
                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <x-forms.label for="type" class="default-label">
                                            {{__('forms.type')}}*
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.select
                                            class="default-input" wire:model="division.type" type="text"
                                            id="type"
                                        >
                                            <x-slot name="option">
                                                <option>{{__('forms.type')}}</option>
                                                @foreach($this->dictionaries['DIVISION_TYPE'] as $k=>$position)
                                                    <option value="{{$k}}">{{$position}}</option>
                                                @endforeach
                                            </x-slot>
                                        </x-forms.select>

                                    </x-slot>
                                    @error('division.type')
                                    <x-slot name="error">
                                        <x-forms.error>
                                            {{$message}}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>
                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <x-forms.label for="email" class="default-label">
                                            {{__('forms.external_id')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input class="default-input"
                                                       wire:model="division.external_id" type="text"
                                                       id="email"/>
                                    </x-slot>
                                    @error('division.external_id')
                                    <x-slot name="error">
                                        <x-forms.error>
                                            {{$message}}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>
                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <x-forms.label for="type_phone" class="default-label">

                                            {{__('forms.typeMobile')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.select wire:model.defer="division.phones.type"
                                                        class="default-select">
                                            <x-slot name="option">
                                                <option>{{__('forms.typeMobile')}}</option>
                                                @foreach($this->dictionaries['PHONE_TYPE'] as $k=>$phone_type)
                                                    <option
                                                        {{ isset ($phone['type']) === $phone_type ? 'selected': ''}} value="{{$k}}">{{$phone_type}}</option>
                                                @endforeach
                                            </x-slot>
                                        </x-forms.select>
                                        @error("division.phones.type")
                                        <x-forms.error>
                                            {{$message}}
                                        </x-forms.error>
                                        @enderror
                                    </x-slot>

                                </x-forms.form-group>
                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <x-forms.label for="phone" class="default-label">

                                            {{__('forms.phone')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">

                                        <x-forms.input
                                            id="phone"
                                            class="default-input"
                                            x-mask="+380999999999"
                                            wire:model="division.phones.number" type="text"
                                        />

                                        @error("division.phones.number")
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                        @enderror
                                    </x-slot>
                                </x-forms.form-group>
                        </div>
                        </div>
                        <div  class="grid grid-cols-1 gap-6">
                            <div class="grid grid-cols-2 gap-6">
                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <x-forms.label for="phone" class="default-label">
                                            {{__('forms.longitude')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input
                                            id="phone"
                                            class="default-input"
                                            x-mask="99.999999"
                                            wire:model="division.location.longitude" type="text"
                                        />
                                        @error("division.location.longitude")
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                        @enderror
                                    </x-slot>
                                </x-forms.form-group>
                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <x-forms.label for="phone" class="default-label">
                                            {{__('forms.latitude')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input
                                            id="phone"
                                            class="default-input"
                                            x-mask="99.999999"
                                            wire:model="division.location.latitude" type="text"
                                        />
                                        @error("division.location.latitude")
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                        @enderror
                                    </x-slot>
                                </x-forms.form-group>

                            </div>
                            <livewire:components.koatuu-search :addresses="$division['addresses'] ?? []" :class="'grid grid-cols-2 gap-6'" />
                        </div>
                    </div>

                </div>
                <div x-data="{ working: false }" class="mb-4">
                    <h3 class="text-lg  mb-6 font-bold dark:text-white">{{__('Графік роботи')}}
                        <button class="flex text-sm text-primary" type="button" @click.prevent="working = !working"
                                x-text="working ? 'Закрити' : 'Відкрити'">Відкрити
                        </button>
                    </h3>
                    @if($working_hours)
                        <div x-show="working" class="grid grid-cols-2 gap-6 w-full">
                            @foreach($working_hours as $key=>$working_hour)
                                <div
                                    x-data="{show_work: {{isset($division['working_hour'][$key]['not_working']) ?? true ? 'true':'false'}}}"
                                    class="col-6">
                                    <label class="text-lg w-full text-black-2 mb-2 ">{{$working_hour}}</label>
                                    <div class=" flex mb-6 flex-col  gap-1 xl:flex-row align-center"
                                         x-data="{ {{$key}}: false }">
                                        <x-forms.form-group class="w-1/4">
                                            <x-slot name="label">
                                                <x-forms.label class="default-label">
                                                    {{__('forms.does_not_work')}}
                                                </x-forms.label>
                                            </x-slot>
                                            <x-slot name="input">
                                                <x-forms.input
                                                    wire:model="division.working_hours.{{$key}}.not_working"
                                                    wire:click="notWorking('{{$key}}');show_work = !show_work;"
                                                    type="checkbox"
                                                />
                                            </x-slot>
                                        </x-forms.form-group>
                                        <x-forms.form-group x-show="!show_work" class="w-1/4">
                                            <x-slot name="label">
                                                <x-forms.label class="default-label">
                                                    {{__('forms.opened_by')}}
                                                </x-forms.label>
                                            </x-slot>
                                            <x-slot name="input">
                                                <x-forms.input class="default-input"
                                                               wire:model="division.working_hours.{{$key}}.0"
                                                               type="time"
                                                />
                                            </x-slot>
                                        </x-forms.form-group>
                                        <x-forms.form-group x-show="!show_work" class="w-1/4">
                                            <x-slot name="label">
                                                <x-forms.label class="default-label">
                                                    {{__('forms.closed_by')}}
                                                </x-forms.label>
                                            </x-slot>
                                            <x-slot name="input">
                                                <x-forms.input class="default-input"
                                                               wire:model="division.working_hours.{{$key}}.1"
                                                               type="time"
                                                               id="email"/>
                                            </x-slot>
                                        </x-forms.form-group>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @endif
                </div>
                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center ">
                    <div class="xl:w-1/4 text-left">
                        <x-secondary-button wire:click="closeModal()">
                            {{__('Закрити ')}}
                        </x-secondary-button>
                    </div>
                    <div class="xl:w-1/4 text-right">
                        <x-button type="submit" class="btn-primary">
                            {{__('Створити ')}}
                        </x-button>
                    </div>
                </div>
                <div wire:loading role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                         viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"/>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"/>
                    </svg>
                </div>
            </x-slot>
        </x-forms.forms-section-modal>
    </x-slot>
    <x-slot name="footer">
    </x-slot>

</x-dialog-modal>



