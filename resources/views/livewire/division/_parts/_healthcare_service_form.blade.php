<x-dialog-modal   maxWidth="2xl" class="w-3 " wire:model.live="showModal">
    <x-slot name="title">
        {{__('Медична послуга')}}
    </x-slot>
    <x-slot name="content">
        @php
            $mode = $mode === 'edit' ? 'update' : 'store';
        @endphp
        <x-forms.forms-section-modal  submit="{{$mode}}">
            <x-slot name="form">
                <div>
                    <div class="grid grid-cols-2 gap-4">
                        <x-forms.form-group  class="">
                            <x-slot name="label">
                                <x-forms.label for="name" class="default-label">
                                    {{__('forms.category')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.select wire:change="changeCategory" wire:model.defer="healthcare_service.category"
                                                class="default-select">
                                    <x-slot name="option">
                                        <option value="">  {{__('forms.select')}}  {{__('forms.category')}} </option>
                                        @foreach($this->dictionaries['HEALTHCARE_SERVICE_CATEGORIES'] as $k=>$h_cat)
                                            <option  value="{{$k}}">{{$h_cat}}</option>
                                        @endforeach
                                    </x-slot>
                                </x-forms.select>
                                @error("healthcare_service.category")
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                                @enderror
                            </x-slot>

                        </x-forms.form-group>
                        <x-forms.form-group class="" >
                            <x-slot name="label">
                                <x-forms.label for="speciality" class="default-label">
                                      {{__('forms.speciality')}}
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.select
                                    x-bind:disabled="{{empty($healthcare_service['category'])}}"
                                    wire:model.defer="healthcare_service.speciality_type"
                                    class="default-select" id="speciality">
                                    <x-slot name="option">
                                        <option value=""> {{__('forms.select')}} {{__('forms.speciality')}}</option>
                                        @foreach($this->dictionaries['SPECIALITY_TYPE'] as $k=>$h_cat)
                                            <option  value="{{$k}}">{{$h_cat}}</option>
                                        @endforeach
                                    </x-slot>
                                </x-forms.select>
                                @error("healthcare_service.category")
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                                @enderror
                            </x-slot>
                            @error('healthcare_service.speciality_type')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                        <x-forms.form-group class="" >
                                <x-slot name="label">
                                    <x-forms.label for="speciality" class="default-label">
                                        {{__('forms.type')}}
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name="input">
                                    <x-forms.select
                                        x-bind:disabled="{{empty($healthcare_service['category'])}}"
                                        wire:model.defer="healthcare_service.type"
                                        id="speciality" class="default-select">
                                        <x-slot name="option">
                                            <option value=""> {{__('forms.select')}} {{__('forms.type')}} </option>
                                            @foreach($this->dictionaries['SPECIALITY_TYPE'] as $k=>$h_cat)
                                                <option  value="{{$k}}">{{$h_cat}}</option>
                                            @endforeach
                                        </x-slot>
                                    </x-forms.select>
                                    @error("healthcare_service.type")
                                    <x-forms.error>
                                        {{$message}}
                                    </x-forms.error>
                                    @enderror
                                </x-slot>
                                @error('healthcare_service.speciality_type')
                                <x-slot name="error">
                                    <x-forms.error>
                                        {{$message}}
                                    </x-forms.error>
                                </x-slot>
                                @enderror
                            </x-forms.form-group>
                        <x-forms.form-group class="">
                            <x-slot name="label">
                                <x-forms.label for="license_id" class="default-label">
                                  {{__('forms.providing_conditions')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input
                                    id="license_id"
                                    class="default-input"
                                    wire:model="healthcare_service.providing_condition" type="text"
                                />

                                @error("healthcare_service.providing_condition")
                                <x-forms.error>
                                    {{ $message }}
                                </x-forms.error>
                                @enderror
                            </x-slot>
                        </x-forms.form-group>
                        <x-forms.form-group class="col-span-2" >
                            <x-slot name="label">
                                <x-forms.label for="comment" class="default-label">
                                    {{__('forms.comment')}}
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.textarea
                                    row="15"
                                    id="comment"
                                    class="default-textarea"
                                    wire:model="healthcare_service.comment" type="text"
                                >
                                </x-forms.textarea>
                                @error("healthcare_service.license_id")
                                <x-forms.error>
                                    {{ $message }}
                                </x-forms.error>
                                @enderror
                            </x-slot>
                        </x-forms.form-group>
                    </div>
                </div>
                <div class="mb-4 mt-4 ">
                    <h3 class="text-sm font-bold dark:text-white mb-5">{{__('Час Доступності')}}</h3>
                   @if(isset($healthcare_service['available_time']) && !empty($healthcare_service['available_time']) )
                            @foreach($healthcare_service['available_time'] as $k=>$a_time)
                            <input type="hidden"  wire:model="healthcare_service.available_time.{{$k}}.days_of_week">
                            <h3 class="text-[14px] mt-4 mb-4">{{get_day_value($k)}}</h3>
                            <div class="grid grid-cols-4 gap-4 mb-5">
                            <x-forms.form-group class="col-span-1">
                                <x-slot name="label">
                                    <x-forms.label for="all_day" class="default-label">
                                        {{__('forms.all_day')}}
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name="input">
                                    <x-forms.input
                                        wire:model="healthcare_service.available_time.{{$k}}.all_day"
                                        id="all_day_{{$k}}" class="default-ce" type="checkbox"/>
                                </x-slot>
                            </x-forms.form-group>
                            <x-forms.form-group>
                                <x-slot name="label">
                                    <x-forms.label for="all_day" class="default-label">
                                        {{__('forms.available_start_time')}}
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name="input">
                                    <x-forms.input
                                        id="license_id"
                                        class="default-input"
                                        wire:model="healthcare_service.available_time.{{$k}}.available_start_time" type="time"
                                    />
                                    @error("healthcare_service.license_id")
                                    <x-forms.error>
                                        {{ $message }}
                                    </x-forms.error>
                                    @enderror
                                </x-slot>
                            </x-forms.form-group>
                            <x-forms.form-group>
                                <x-slot name="label">
                                    <x-forms.label for="all_day" class="default-label">
                                        {{__('forms.available_end_time')}}
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name="input">
                                    <x-forms.input
                                        id="license_id"
                                        class="default-input"
                                        wire:model="healthcare_service.available_time.{{$k}}.available_end_time" type="time"
                                    />
                                    @error("healthcare_service.license_id")
                                    <x-forms.error>
                                        {{ $message }}
                                    </x-forms.error>
                                    @enderror
                                </x-slot>
                            </x-forms.form-group>
                            <div class="btn flex items-center	 h-full">
                                <button type="button" class="flex text-sm text-primary" wire:click="removeAvailableTime({{$k}})">
                                    {{__('Видалити ')}}
                                </button>
                            </div>
                            </div>

                        @endforeach
                        @endif
                    @if(count($available_time) < 7)
                    <button class=" flex text-sm text-primary" type="button" wire:click="addAvailableTime({{ max(0, count($available_time) - 1) }})"> Додати Час</button>
                    @endif
                </div>
                <div class="mb-4 mt-4 ">
                    <h3 class="text-sm font-bold dark:text-white mb-5">{{__('Час Не Доступності')}}</h3>
                    @if(isset($healthcare_service['not_available']) && !empty($healthcare_service['not_available']) )
                        @foreach($healthcare_service['not_available'] as $k=>$not_time)
                            <div class="grid grid-cols-2 gap-6 mb-5">

                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <x-forms.label for="during_start-{{$k}}" class="default-label">
                                            {{__('forms.not_available_start')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input
                                            id="during_start-{{$k}}"
                                            class="default-input"
                                            wire:model="healthcare_service.available_time.{{$k}}.during.start" type="date"
                                        />
                                    </x-slot>
                                </x-forms.form-group>
                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <x-forms.label for="during_end-{{$k}}" class="default-label">
                                            {{__('forms.not_available_end')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input
                                            id="during_end-{{$k}}"
                                            class="default-input"
                                            wire:model="healthcare_service.available_time.{{$k}}.during.end" type="date"
                                        />
                                    </x-slot>                                </x-forms.form-group>
                                <div class="btn flex items-center	 h-full">
                                    <button type="button" class="flex text-sm text-primary" wire:click="removeAvailableTime({{$k}})">
                                        {{__('Видалити ')}}
                                    </button>
                                </div>
                                <x-forms.form-group class="col-span-6">
                                    <x-slot name="label">
                                        <x-forms.label for="description_{{$k}}" class="default-label">
                                            {{__('forms.description')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.textarea
                                            wire:model="healthcare_service.not_available.{{$k}}.description"
                                            id="description_{{$k}}" class="default-input" type="text">
                                        </x-forms.textarea>
                                    </x-slot>
                                </x-forms.form-group>

                            </div>

                        @endforeach
                    @endif
                        <button class=" flex text-sm text-primary" type="button" wire:click="addNotAvailableTime"> Додати Час</button>
                </div>

                <div class="mt-6.5 flex flex-col gap-6 xl:flex-row justify-between items-center ">
                    <div class="xl:w-1/4 text-left">
                        <x-secondary-button wire:click="closeModal()">
                            {{__('Закрити ')}}
                        </x-secondary-button>
                    </div>

                    <div class="xl:w-1/4 text-right">
                        <x-button type="submit" class="btn-primary" >
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

</x-dialog-modal>





