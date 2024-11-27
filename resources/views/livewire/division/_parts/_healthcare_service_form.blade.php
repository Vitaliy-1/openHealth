<x-dialog-modal maxWidth='2xl' class='w-3' wire:model.live='showModal'>
    <x-slot name='title'>
        {{ __('Медична послуга') }}
    </x-slot>

    <x-slot name='content'>
        @php
            $mode = $mode === 'edit' ? 'update' : 'store';
        @endphp

        <x-forms.forms-section-modal submit="{{ $mode }}">
            <x-slot name='form'>
                <div>
                    <div class='grid grid-cols-2 gap-4'>
                        {{-- CATEGORY --}}
                        {{-- <x-forms.form-group class=''>
                            <x-slot name='label'>
                                <x-forms.label for="category" class="default-label">
                                    {{ __('forms.category') }} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name='input'>
                                <x-forms.select
                                    wire:change='changeCategory'
                                    wire:model.defer='formService.healthcare_service.category'
                                    class='default-select'
                                    id='category'
                                >
                                    <x-slot name='option'>
                                        <option value=''>
                                            {{ __('forms.select') }} {{ __('forms.category') }}
                                        </option> --}}
                                        {{-- <option value='MSP'>{{ $this->dictionaries['HEALTHCARE_SERVICE_CATEGORIES']['MSP'] }}</option> --}}
                                       {{-- @foreach ($dictionaries['modal']['HEALTHCARE_SERVICE_CATEGORIES'] as $k => $s_cat)
                                            <option value="{{ $k }}">{{ $s_cat }}</option>
                                        @endforeach
                                    </x-slot>
                                </x-forms.select>
                                @error('formService.healthcare_service.category')
                                    <x-forms.error>
                                        {{ $message }}
                                    </x-forms.error>
                                @enderror
                            </x-slot>
                        </x-forms.form-group> --}}

                        {{-- PROVIDING CONDITION --}}
                        <x-forms.form-group class=''>

                            <x-slot name='label'>
                                <x-forms.label for='providing_condition' class='default-label'>
                                    {{ __('forms.providingConditions') }} *
                                </x-forms.label>
                            </x-slot>

                            <x-slot name='input' >
                                <x-forms.select
                                    x-bind:disabled="{{ $category === 'MSP' }}"
                                    id='providing_condition'
                                    wire:change="changeProvidingCondition($event.target.value)"
                                    wire:model.defer='formService.healthcare_service.providing_condition'
                                    class='default-select'
                                >
                                    <x-slot name='option'>
                                        <option
                                            value=''
                                            disabled
                                        >
                                            {{ __('forms.select') }} {{ __('forms.providingConditions') }}
                                        </option>
                                        @foreach ($dictionaries['modal']['PROVIDING_CONDITION'] as $k_p_con => $p_con)
                                            <option
                                                wire:key="{{ $k_p_con }}"
                                                value="{{ $k_p_con }}"
                                                @if($category == 'MSP' && $k_p_con == 'OUTPATIENT') selected @endif
                                            >
                                                {{ $p_con }}
                                            </option>
                                        @endforeach
                                    </x-slot>
                                </x-forms.select>
                                @error('formService.healthcare_service.providing_condition')
                                <x-forms.error>
                                    {{ $message }}
                                </x-forms.error>
                                @enderror
                            </x-slot>
                        </x-forms.form-group>
                        {{-- SPECIALITY --}}
                        <x-forms.form-group class=''>
                            <x-slot name='label'>
                                <x-forms.label for='speciality_type' class='default-label'>
                                    {{ __('forms.specialityType') }} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name='input'>
                                <x-forms.select
                                    x-bind:disabled="{{ $mode === 'update' ? 'true' : 'false' }}"
                                    wire:model.defer='formService.healthcare_service.speciality_type'
                                    class='default-select'
                                    id='speciality_type'
                                >
                                    <x-slot name='option'>
                                        <option value=''>
                                            {{ __('forms.select') }} {{ __('forms.specialityType') }}
                                        </option>
                                        @foreach ($dictionaries['modal']['SPECIALITY_TYPE'] as $k_type => $s_type)
                                            <option
                                                wire:key="{{ $k_p_con }}"
                                                value="{{ $k_type }}"
                                            >
                                                {{ $s_type }}
                                            </option>
                                        @endforeach
                                    </x-slot>
                                </x-forms.select>
                                @error('formService.healthcare_service.speciality_type')
                                    <x-forms.error>
                                        {{ $message }}
                                    </x-forms.error>
                                @enderror
                            </x-slot>
                        </x-forms.form-group>
                        {{--                        <x-forms.form-group class=""> --}}
                        {{--                            <x-slot name="label"> --}}
                        {{--                                <x-forms.label for="speciality" class="default-label"> --}}
                        {{--                                    {{__('forms.type')}} --}}
                        {{--                                </x-forms.label> --}}
                        {{--                            </x-slot> --}}
                        {{--                            <x-slot name="input"> --}}
                        {{--                                <x-forms.select --}}
                        {{--                                    x-bind:disabled="{{empty($healthcare_service['category'])}}" --}}
                        {{--                                    wire:model.defer="healthcare_service.type" --}}
                        {{--                                    id="speciality" class="default-select"> --}}
                        {{--                                    <x-slot name="option"> --}}
                        {{--                                        <option value=""> {{__('forms.select')}} {{__('forms.type')}} </option> --}}
                        {{--                                        @foreach ($this->dictionaries['HEALTHCARE_SERVICE_PHARMACY_DRUGS_TYPES'] as $k => $h_cat) --}}
                        {{--                                            <option value="{{$k}}">{{$h_cat}}</option> --}}
                        {{--                                        @endforeach --}}
                        {{--                                    </x-slot> --}}
                        {{--                                </x-forms.select> --}}
                        {{--                                @error('healthcare_service.type') --}}
                        {{--                                <x-forms.error> --}}
                        {{--                                    {{$message}} --}}
                        {{--                                </x-forms.error> --}}
                        {{--                                @enderror --}}
                        {{--                            </x-slot> --}}
                        {{--                            @error('healthcare_service.speciality_type') --}}
                        {{--                            <x-slot name="error"> --}}
                        {{--                                <x-forms.error> --}}
                        {{--                                    {{$message}} --}}
                        {{--                                </x-forms.error> --}}
                        {{--                            </x-slot> --}}
                        {{--                            @enderror --}}
                        {{--                        </x-forms.form-group> --}}
                        {{--                        <x-slot name="label"> --}}
                        {{--                            <x-forms.label for="providing_condition" class="default-label"> --}}
                        {{--                                {{__('forms.providing_conditions')}} * --}}
                        {{--                            </x-forms.label> --}}
                        {{--                        </x-slot> --}}

                        {{-- COMMENT --}}
                        <x-forms.form-group class='col-span-2'>
                            <x-slot name='label'>
                                <x-forms.label for='comment' class='default-label'>
                                    {{ __('forms.comment') }}
                                </x-forms.label>
                            </x-slot>
                            <x-slot name='input'>
                                <x-forms.textarea
                                    row='15'
                                    id='comment'
                                    class='default-input'
                                    wire:model='formService.healthcare_service.comment'
                                    type='text'
                                >
                                </x-forms.textarea>
                                @error('formService.healthcare_service.comment')
                                    <x-forms.error>
                                        {{ $message }}
                                    </x-forms.error>
                                @enderror
                            </x-slot>
                        </x-forms.form-group>
                    </>
                </div>
                {{-- TIME PART --}}
                <div class='mb-4 mt-4'>
                    <h3 class='text-sm font-bold dark:text-white mb-5'>{{ __('Час Доступності') }}</h3>

                    @nonempty($this->availableTime)
                        @foreach ($this->availableTime as $k => $a_time)
                            <input type='hidden' wire:model="formService.healthcare_service.available_time.{{ $k }}.days_of_week">

                            <h3 class='text-[16px] font-semibold mt-4 mb-4'>{{ get_day_value($k) }}</h3>

                            <div class='grid grid-cols-4 gap-4 mb-5' x-data='{ disabled : @js($a_time['all_day']) }'>
                                <x-forms.form-group class='col-span-1'>
                                    <x-slot name='input'>
                                        <div class='flex items-center mb-4'>
                                            <x-forms.checkbox
                                                wire:model="formService.healthcare_service.available_time.{{ $k }}.all_day"
                                                id="all_day_{{ $k }}"
                                                class='default-ce'
                                                type='checkbox'
                                                x-bind:checked="disabled"
                                                x-on:click="disabled = !disabled"
                                            />
                                            <label for="all_day_{{ $k }}" class='ms-2 text-sm font-medium text-gray-900 dark:text-gray-300'>
                                                   {{ __('forms.allDay') }}
                                            </label>
                                        </div>
                                    </x-slot>
                                </x-forms.form-group>

                                <x-forms.form-group>
                                    <x-slot name='label'>
                                        <x-forms.label for="start_time-{{ $k }}" class="default-label" x-bind:disabled="disabled">
                                                {{ __('forms.availableStartTime') }} <span x-show="!disabled">*</span>
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name='input'>
                                        <x-forms.input-time
                                            id="start_time-{{ $k }}"
                                            wire:model="formService.healthcare_service.available_time.{{ $k }}.available_start_time"
                                            x-bind:disabled="disabled"
                                            required
                                        />
                                    </x-slot>
                                    @error("formService.healthcare_service.available_time.{{ $k }}.available_start_time")
                                            <x-forms.error>
                                                {{ $message }}
                                            </x-forms.error>
                                    @enderror
                                </x-forms.form-group>

                                <x-forms.form-group>
                                    <x-slot name='label'>
                                        <x-forms.label for="end_time-{{ $k }}" class='default-label' x-bind:disabled="disabled">
                                            {{ __('forms.availableEndTime') }} <span x-show="!disabled">*</span>
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name='input'>
                                        <x-forms.input-time
                                            id="end_time-{{ $k }}"
                                            wire:model="formService.healthcare_service.available_time.{{ $k }}.available_end_time"
                                            x-bind:disabled="disabled"
                                            required
                                        />
                                    </x-slot>
                                    @error('formService.healthcare_service.available_time.{{ $k }}.available_end_time')
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    @enderror
                                </x-forms.form-group>

                                <div class='btn flex items-end justify-end h-full'>
                                    <x-danger-button
                                        class='mb-1'
                                        wire:click="removeAvailableTime({{ $k }})"
                                    >
                                        {{ __('Видалити') }}
                                    </x-danger-button>
                                </div>
                            </div>
                        @endforeach
                    @endnonempty

                    @if (count($this->availableTime) < 7)
                        <button
                            class='flex text-sm text-primary hover:text-black'
                            type='button'
                            wire:click="addAvailableTime({{ max(0, count($this->availableTime)) }})"
                        >
                            {{ __('Додати Час') }}
                        </button>
                    @endif
                </div>

                <div class='mb-4 mt-4'>
                    <h3 class='text-sm font-bold dark:text-white mb-5'>{{ __('Час Не Доступності') }}</h3>

                    @nonempty($this->notAvailable)
                        @foreach ($this->notAvailable as $k => $not_time)
                            <div class='grid grid-cols-2 gap-6 mb-5'>

                                <x-forms.form-group>
                                    <x-slot name='label'>
                                        <x-forms.label for="during_start-{{ $k }}" class='default-label'>
                                            {{ __('forms.notAvailableStart') }} *
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name='input'>
                                        <x-forms.input-date
                                            id="during_start-{{ $k }}"
                                            wire:model="formService.healthcare_service.not_available.{{ $k }}.during.start"
                                            type='date'
                                            required
                                        />
                                    </x-slot>
                                    @error("formService.healthcare_service.not_available.{{ $k }}.during.start")
                                    <x-slot name='error'>
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>

                                <x-forms.form-group>
                                    <x-slot name='label'>
                                        <x-forms.label for="during_end-{{ $k }}" class='default-label'>
                                            {{ __('forms.notAvailableEnd') }} *
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name='input'>
                                        <x-forms.input-date
                                            id="during_end-{{ $k }}"
                                            wire:model="formService.healthcare_service.not_available.{{ $k }}.during.end"
                                            type='date'
                                            required
                                        />
                                    </x-slot>
                                    @error("formService.healthcare_service.not_available.{{ $k }}.during.end")
                                    <x-slot name='error'>
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>

                                <div></div>
                                <div></div>
                                <div></div>

                                <div class='btn flex items-end justify-end h-full'>
                                    <x-danger-button
                                        class='mb-1'
                                        wire:click="removeNotAvailable({{ $k }})"
                                    >
                                        {{ __('Видалити ') }}
                                    </x-danger-button>
                                </div>

                                <x-forms.form-group class='col-span-6'>
                                    <x-slot name='label'>
                                        <x-forms.label for="description_{{ $k }}" class='default-label'>
                                            {{ __('forms.description') }}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name='input'>
                                        <x-forms.textarea
                                            wire:model="formService.healthcare_service.not_available.{{ $k }}.description"
                                            id="description_{{ $k }}"
                                            class='default-input'
                                            type='text'
                                            required
                                        >
                                        </x-forms.textarea>
                                    </x-slot>
                                    @error("formService.healthcare_service.not_available.{{ $k }}.description")
                                    <x-slot name='error'>
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>

                            </div>
                        @endforeach
                    @endnonempty
                    <button
                        class='flex text-sm text-primary hover:text-black'
                        type='button'
                        wire:click='addNotAvailableTime'
                    >
                        {{ __('Додати Час') }}
                    </button>
                </div>

                <div class='mt-6.5 flex flex-col gap-6 xl:flex-row justify-between items-center'>
                    <div class='xl:w-1/4 text-left'>
                        <x-secondary-button wire:click="closeModal()">
                            {{ __('Закрити') }}
                        </x-secondary-button>
                    </div>
                    <div class='xl:w-1/4 text-right'>
                        <x-button
                            x-bind:disabled="{{ (!empty($formService->healthcare_service['status']) && $formService->healthcare_service['status'] !== 'ACTIVE') ? 'true' : 'false' }}"
                            type='submit'
                            class='text-white focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'
                            x-bind:class="'{{ (!empty($formService->healthcare_service['status']) && $formService->healthcare_service['status'] !== 'ACTIVE') ? '' : 'bg-blue-700 hover:bg-blue-800' }}'"
                        >
                        @if($mode === 'store')
                            {{ __('Створити') }}
                        @else
                            {{ __('Оновити') }}
                        @endif
                        </x-button>
                    </div>
                </div>

                <div wire:loading role='status' class='absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2'>
                    <svg
                        aria-hidden='true'
                        class='w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600'
                        viewBox='0 0 100 101' fill='none' xmlns='http://www.w3.org/2000/svg'
                    >
                        <path
                            d='M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z'
                            fill='currentColor'
                        />
                        <path
                            d='M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z'
                            fill='currentFill'
                        />
                    </svg>
                </div>
            </x-slot>
        </x-forms.forms-section-modal>
    </x-slot>
</x-dialog-modal>
