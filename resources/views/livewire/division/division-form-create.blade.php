<div>
    @php
        $mode = $mode === 'edit' ? 'update' : 'store';
    @endphp

    <x-section-navigation x-data="{ showFilter: false }" class=''>
        <x-slot name='title'>
            {{ $mode === 'store' ? __('Додати місце надання послуг') : __('Редагувати місце надання послуг') }}
        </x-slot>
        @nonempty($currentDivision)
        <x-slot name="description">{{ $currentDivision['type'] }} '{{ $currentDivision['name'] }}'</x-slot>
        @endnonempty
    </x-section-navigation>

    <div class='inline-block min-w-full align-middle'>
        <x-forms.forms-section submit="{{ $mode }}">
            <x-slot name='form'>
                <div class='flex bg-white p-6 flex-col'>
                    <div class='w-full mb-4 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700'>
                        <div class='py-4'>
                            <h3 class='font-medium text-2xl	text-black dark:text-white'>
                                {{ __('Особисті дані') }}
                            </h3>
                        </div>
                        <div class='grid grid-cols-4 gap-6'>
                            <x-forms.form-group>
                                <x-slot name='label'>
                                    <x-forms.label for='name' class='default-label'>
                                        {{ __('forms.fullNameDivision') }} *
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name='input'>
                                    <x-forms.input
                                        class='default-input'
                                        wire:model='formService.division.name'
                                        type='text'
                                        id='name'
                                    />
                                </x-slot>
                                @error('formService.division.name')
                                    <x-slot name='error'>
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    </x-slot>
                                @enderror
                            </x-forms.form-group>

                            <x-forms.form-group>
                                <x-slot name='label'>
                                    <x-forms.label for='email' class='default-label'>
                                        {{ __('forms.email') }} *
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name='input'>
                                    <x-forms.input
                                        class='default-input'
                                        wire:model='formService.division.email'
                                        type='text'
                                        id='email'
                                    />
                                </x-slot>
                                @error('formService.division.email')
                                    <x-slot name='error'>
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    </x-slot>
                                @enderror
                            </x-forms.form-group>

                            <x-forms.form-group>
                                <x-slot name='label'>
                                    <x-forms.label for='type' class='default-label'>
                                        {{ __('forms.type') }} *
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name='input'>
                                    <x-forms.select
                                        class='default-input'
                                        wire:model='formService.division.type'
                                        type='text'
                                        id='type'
                                        x-bind:disabled="{{ $mode === 'update' ? 'true' : 'false' }}"
                                    >
                                        <x-slot name='option'>
                                            <option>{{ __('forms.type') }}</option>
                                            @foreach ($dictionaries['DIVISION_TYPE'] as $k => $type)
                                                <option value="{{ $k }}">{{ $type }}</option>
                                            @endforeach
                                        </x-slot>
                                    </x-forms.select>
                                </x-slot>
                                @error('formService.division.type')
                                    <x-slot name='error'>
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    </x-slot>
                                @enderror
                            </x-forms.form-group>

                            <x-forms.form-group>
                                <x-slot name='label'>
                                    <x-forms.label for='external_id' class='default-label'>
                                        {{ __('forms.externalId') }}
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name='input'>
                                    <x-forms.input
                                        class='default-input'
                                        wire:model='formService.division.external_id'
                                        type='text'
                                        id='external_id'
                                        x-bind:disabled="{{ $mode === 'update' ? 'true' : 'false' }}"
                                    />
                                </x-slot>
                                @error('formService.division.external_id')
                                    <x-slot name='error'>
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    </x-slot>
                                @enderror
                            </x-forms.form-group>

                            <x-forms.form-group>
                                <x-slot name='label'>
                                    <x-forms.label for='phone_type' class='default-label'>
                                        {{ __('forms.typeMobile') }}
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name='input'>
                                    <x-forms.select
                                        wire:model.defer='formService.division.phones.type'
                                        class='default-select'
                                        id="phone_type"
                                    >
                                        <x-slot name='option'>
                                            <option>{{ __('forms.typeMobile') }} *</option>
                                            @foreach ($dictionaries['PHONE_TYPE'] as $k => $phone_type)
                                                <option {{ isset($phone['type']) === $phone_type ? 'selected' : '' }}
                                                    value="{{ $k }}">{{ $phone_type }}
                                                </option>
                                            @endforeach
                                        </x-slot>
                                    </x-forms.select>
                                    @error('formService.division.phones.type')
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    @enderror
                                </x-slot>

                            </x-forms.form-group>

                            <x-forms.form-group>
                                <x-slot name='label'>
                                    <x-forms.label for='phone' class='default-label'>
                                        {{ __('forms.phone_number') }} *
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name='input'>
                                    <x-forms.input
                                        id='phone'
                                        class='default-input'
                                        x-mask='+380999999999'
                                        wire:model='formService.division.phones.number'
                                        type='text'
                                    />
                                    @error('formService.division.phones.number')
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    @enderror
                                </x-slot>
                            </x-forms.form-group>

                            <x-forms.form-group>
                                <x-slot name='label'>
                                    <x-forms.label for='longitude' class='default-label'>
                                        {{ __('forms.longitude') }}
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name='input'>
                                    <x-forms.input
                                        id='longitude'
                                        class='default-input'
                                        x-mask='99.999999'
                                        wire:model='formService.division.location.longitude'
                                        type='text'
                                    />
                                    @error('formService.division.location.longitude')
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    @enderror
                                </x-slot>
                            </x-forms.form-group>

                            <x-forms.form-group>
                                <x-slot name='label'>
                                    <x-forms.label for='latitude' class='default-label'>
                                        {{ __('forms.latitude') }}
                                    </x-forms.label>
                                </x-slot>
                                <x-slot name='input'>
                                    <x-forms.input
                                        id='latitude'
                                        class='default-input'
                                        x-mask='99.999999'
                                        wire:model='formService.division.location.latitude'
                                        type='text'
                                    />
                                    @error('formService.division.location.latitude')
                                        <x-forms.error>
                                            {{ $message }}
                                        </x-forms.error>
                                    @enderror
                                </x-slot>
                            </x-forms.form-group>
                        </div>
                    </div>

                    {{-- ADDRESS --}}
                    <div class='w-full mb-4 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700'>
                        <div class='py-4'>
                            <h3 class='font-medium text-2xl text-black dark:text-white'>
                                {{ __('Адреса') }}
                            </h3>
                        </div>

                        <x-forms.addresses-search
                            :address="$address"
                            :districts="$districts"
                            :settlements="$settlements"
                            :streets="$streets"
                            class='mb-4 flex justify-between wrap flex-col flex-wrap gap-6 xl:flex-row'
                        />
                    </div>

                    {{-- WORKING HOURS --}}
                    <div x-data="{ working: false }" class='w-full mb-4 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700'>
                        <div class='py-4'>
                            <h3 class='font-medium text-2xl text-black dark:text-white'>
                                {{ __('Графік роботи') }}
                                <button
                                    @click.prevent="working = !working"
                                    x-text="working ? 'Закрити' : 'Відкрити'"
                                    class='block flex items-center justify-center py-2 font-semibold text-xs text-gray-500 uppercase tracking-widest hover:text-gray-900 active:text-gray-900 transition ease-in-out duration-150'
                                >
                                    {{ __('Відкрити') }}
                                </button>
                            </h3>
                            @if($mode === 'store')
                                <p x-show='working' x-cloak class="pt-4 text-xs italic text-orange-500">{{ __("ВАЖЛИВО: Всі дні з незаповненими даними автоматично отримують статус 'Не працює'") }}</p>
                            @endif
                        </div>

                        @if ($weekdays)
                            <div x-show='working' class='grid grid-cols-2 gap-6 w-full'>
                                @foreach ($weekdays as $key => $day)
                                    <div :key={{ $key }} x-data="{
                                            shift: @json(count($formService->getDivisionParam('working_hours')[$key]) > 1),
                                            show_work: @json(!empty($formService->getDivisionParam('working_hours')[$key][0]) || $mode === 'store'),
                                            checkShift() {
                                                this.shift = !this.show_work;
                                            }
                                        }"
                                        class="col-6"
                                    >
                                        <label class='text-lg w-full text-black-2 mb-2 pb-2'>
                                            {{ $day }}
                                        </label>
                                        <div class="flex mt-4 mb-1 flex-col gap-x-6 gap-y-1 xl:flex-row align-center" x-data="{ '{{ $key }}': false }">
                                            <x-forms.form-group class='min-w-fit'>
                                                <x-slot name='input'>
                                                    <div class='flex items-center mb-4'>
                                                        <x-forms.checkbox
                                                            wire:click="notWorking('{{ $key }}', show_work); show_work = !show_work;"
                                                            type='checkbox'
                                                            x-bind:checked="!show_work"
                                                            x-on:click="checkShift()"
                                                        />
                                                        <label class='ms-2 text-sm font-medium text-gray-900 dark:text-gray-300'>
                                                            {{ __('forms.doesNotWork') }}
                                                     </label>
                                                    </div>

                                                    <template x-if='show_work'>
                                                        <div class='flex items-left flex-col flex-wrap mb-4'>
                                                            <div class='flex items-left flex-row wrap-nowrap mb-4'>
                                                                <x-forms.checkbox
                                                                    wire:click="noShift('{{ $key }}', shift); shift = !shift;"
                                                                    type='checkbox'
                                                                    x-bind:checked="shift"
                                                                />
                                                                <label class='ms-2 text-xs font-medium text-gray-900 dark:text-gray-300'>
                                                                    {{ __('Позмінно') }}
                                                                </label>
                                                            </div>

                                                            @if (count($formService->getDivisionParam('working_hours')[$key]) < 4)
                                                                <x-button
                                                                    x-show='shift'
                                                                    class='font-semibold text-[10px] text-left text-gray-500 uppercase hover:text-gray-900 active:text-gray-900'
                                                                    @click.prevent=''
                                                                    wire:click="addAvailableShift('{{ $key }}')"
                                                                >
                                                                    {{ __('Додати зміну') }}
                                                                </x-button>
                                                            @endif
                                                    </template>
                                                </x-slot>
                                            </x-forms.form-group>

                                            @if($mode === 'store' || !empty($formService->getDivisionParam('working_hours')[$key]))
                                            <div class='flex items-center flex-col flex-wrap gap-2 mb-4 w-full'>
                                            @foreach ($formService->getDivisionParam('working_hours')[$key] as $shift => $shift_hours)
                                                <div class='flex  justify-between gap-4 mb-4 w-full w-1/4'>
                                                    <x-forms.form-group
                                                        x-show="show_work"
                                                        class="w-1/2"
                                                    >
                                                        <x-slot name='label'>
                                                            <x-forms.label
                                                                x-text="shift ? '{{ __(':shift_number зміна: початок', ['shift_number' => $shift + 1]) }}'  : '{{ __('forms.openedBy') }}'"
                                                                for="opened_by-{{ $key }}-{{ $shift }}"
                                                                class='default-label'
                                                            />
                                                        </x-slot>

                                                        <x-slot name='input'>
                                                            <x-forms.input-time
                                                                id="opened_by-{{ $key }}-{{ $shift }}"
                                                                wire:model="formService.division.working_hours.{{ $key }}.{{ $shift }}.0"
                                                            />
                                                        </x-slot>
                                                        @error("formService.division.working_hours.{{ $key }}.{{ $shift }}.0")
                                                            <x-forms.error>
                                                                {{ $message }}
                                                            </x-forms.error>
                                                        @enderror
                                                    </x-forms.form-group>

                                                    <x-forms.form-group
                                                        x-show="show_work"
                                                        class="w-1/2"
                                                    >
                                                        <x-slot name='label'>
                                                            <x-forms.label
                                                                x-text="shift ? '{{ __(':shift_number зміна: кінець', ['shift_number' => $shift + 1]) }}' : '{{ __('forms.closedBy') }}'"
                                                                for="closed_by-{{ $key }}-{{ $shift }}"
                                                                class="default-label"
                                                            />
                                                        </x-slot>
                                                        <x-slot name='input'>
                                                            <x-forms.input-time
                                                                id="closed_by-{{ $key }}-{{ $shift }}"
                                                                wire:model="formService.division.working_hours.{{ $key }}.{{ $shift }}.1"
                                                            />
                                                        </x-slot>
                                                        @error("formService.division.working_hours.{{ $key }}.{{ $shift }}.1")
                                                            <x-forms.error>
                                                                {{ $message }}
                                                            </x-forms.error>
                                                        @enderror
                                                    </x-forms.form-group>

                                                    <div class="self-center pt-6" x-data="{ isShift: {{ $shift > 0 ? 'true' : 'false' }} }">
                                                        <x-button
                                                            x-show="shift && isShift && show_work"
                                                            @click.prevent=''
                                                            wire:click="deleteShift('{{ $key }}', '{{ $shift }}')"
                                                            class="px-2 py-1  text-red-500 text-2xl font-bold rounded"
                                                        >
                                                            ☒
                                                        </x-button>
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        @endif
                    </div>

                    <div class='mb-4.5 mt-6 flex flex-col gap-6 xl:flex-row justify-between items-center'>
                        <x-secondary-button>
                            <div class='xl:w-1/4 text-left'>
                                <a href="{{ route('division.index') }}">
                                    {{ __('forms.back') }}
                                </a>
                            </div>
                        </x-secondary-button>
                        <div class='xl:w-1/4 text-right'>
                            <x-button type='submit' class='text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'>
                                @if($mode === 'store')
                                    {{ __('Створити') }}
                                @else
                                    {{ __('Оновити') }}
                                @endif
                            </x-button>
                        </div>
                    </div>

                    <div
                        wire:loading
                        role='status'
                        class='absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2'
                    >
                        <svg
                            aria-hidden='true'
                            class='w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600'
                            viewBox='0 0 100 101'
                            fill='none'
                            xmlns='http://www.w3.org/2000/svg'
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
                </div>
            </x-slot>
        </x-forms.forms-section>
    </div>
</div>
