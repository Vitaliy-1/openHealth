<div>
    <x-section-navigation x-data="{ showFilter: false }" class="">
        <x-slot name='title'>{{ __('forms.contract') }}</x-slot>

        {{-- <x-slot name='description'>{{ __('forms.contract') }}</x-slot> --}}

        <x-slot name='navigation'>
            <div class='rounded-sm border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark'>
                <div class='items-center flex justify-end border-stroke px-7 py-4 dark:border-strokedark'>
                    <a
                        href=''
                        type='button'
                        class='text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'
                        wire:click.prevent="openModal('intialization_contract')"
                    >
                        {{ __('forms.addContract') }}
                    </a>
                </div>
            </div>
        </x-slot>
    </x-section-navigation>

    {{-- <div class='flex flex-col h-screen -mt-4 border-t'> --}}
        <div class='overflow-x-auto'>
            <div class='inline-block min-w-full align-middle'>
                <div class='shadow'>
                    <x-tables.table class="mb-20">
                        <x-slot name='headers' :list="$tableHeaders"></x-slot>

                        <x-slot name='tbody'>
                            @nonempty($contracts->items())
                                @foreach ($contracts as $contract)
                                    <tr>
                                        <td class='border-b border-[#eee] py-5 px-4'>
                                            <p class='text-black dark:text-white'>
                                                {{ $contract->uuid ?? '' }}
                                            </p>
                                        </td>

                                        <td class='border-b border-[#eee] py-5 px-4'>
                                            <p class='text-black dark:text-white'>
                                                {{ $contract->contract_number ?? '' }}
                                            </p>
                                        </td>

                                        <td class='border-b border-[#eee] py-5 px-4'>
                                            <p class='text-black dark:text-white'>
                                                {{ $contract->start_date ?? '' }}
                                            </p>
                                        </td>

                                        <td class='border-b border-[#eee] py-5 px-4'>
                                            <p class='text-black dark:text-white'>
                                                {{ $contract->end_date ?? '' }}
                                            </p>
                                        </td>

                                        <td class='border-b border-[#eee] py-5 px-4'>
                                            <p class='text-black dark:text-white'>
                                                {{ $contract->status ?? '' }}
                                            </p>
                                        </td>

                                        <td class='border-b border-[#eee] py-5 px-4'>
                                            <div class='flex justify-center'>
                                                <div x-data="{
                                                        open: false,
                                                        toggle() {
                                                            if (this.open) {
                                                                return this.close()
                                                            }

                                                            this.$refs.button.focus()

                                                            this.open = true
                                                        },
                                                        close(focusAfter) {
                                                            if (!this.open) return

                                                            this.open = false

                                                            focusAfter && focusAfter.focus()
                                                        }
                                                    }"
                                                    x-on:keydown.escape.prevent.stop="close($refs.button)"
                                                    x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                                                    x-id="['dropdown-button']"
                                                    class='relative'
                                                >
                                                    <button
                                                        x-ref='button'
                                                        x-on:click="toggle()"
                                                        :aria-expanded="open"
                                                        :aria-controls="$id('dropdown-button')"
                                                        type='button'
                                                        class='hover:text-primary'
                                                    >
                                                        <svg
                                                            class='fill-current'
                                                            width='18'
                                                            height='18'
                                                            xmlns='http://www.w3.org/2000/svg'
                                                            fill='none'
                                                            viewBox='0 0 24 24'
                                                            stroke-width='1.5'
                                                        >
                                                            <path
                                                                stroke-linecap='round'
                                                                stroke-linejoin='round'
                                                                d='m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125'
                                                            />
                                                        </svg>
                                                    </button>
                                                    <div
                                                        x-ref='panel'
                                                        x-show='open'
                                                        x-transition.origin.top.left
                                                        x-on:click.outside="close($refs.button)"
                                                        :id="$id('dropdown-button')"
                                                        style='display: none;'
                                                        class='absolute right-0 mt-2 w-40 rounded-md bg-white shadow-md z-50'
                                                    >
                                                        <a
                                                            href="{{ route('contract.form', $contract->uuid) }}"
                                                            class='flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500'
                                                        >
                                                            {{ __('forms.edit') }}
                                                        </a>

                                                        <a
                                                            href=''
                                                            wire:click.prevent="showContract({{ $contract->id }})"
                                                            class='flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500'
                                                        >
                                                            {{ __('Деталі') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @elsenonempty
                            <tr>
                                <td class="text-black w-full p-4 border-gray-200 text-center dark:bg-gray-800 dark:border-gray-700 dark:text-white" colspan="6">
                                    <p >
                                        {{ __('Нічого не знайдено') }}
                                    </p>
                                </td>
                            </tr>
                            @endnonempty
                        </x-slot>
                    </x-tables.table>
                    <x-pagination :pagination="$contracts" class="pagination" style="margin-block-start: -80px;"/>
                </div>
            </div>
        </div>
    {{-- </div> --}}

    @if ($showModal == 'intialization_contract')
        <x-alert-modal
            name='title'
            id='info-popup'
            wire:model.live='showModal'
            maxWidth='3xl'
            class='w-2 h-full'
        >
            <x-slot name='title'>
                {{ __('forms.initializationContract') }}
            </x-slot>

            <x-slot name='text'>
                @if ($hasInitContract)
                    <x-forms.select wire:model='contract_type' class='default-select'>
                        <x-slot name='option'>
                            <option value=''>{{ __('forms.contractType') }}</option>
                            <option value='capitation'>CAPITATION</option>

                            {{--                                @foreach ($this->dictionaries['CONTRACT_TYPE'] as $k => $contract_type) --}}
                            {{--                                    <option value="{{$k}}">{{$contract_type}}</option> --}}
                            {{--                                @endforeach --}}
                        </x-slot>
                    </x-forms.select>
                    @error('contract_type')
                        <x-forms.error>
                            {{ $message }}
                        </x-forms.error>
                    @enderror
                @else
                    <p> {{ __('forms.alertInitializationContract') }} </p>
                @endif
            </x-slot>

            <x-slot name='button'>
                <div class='justify-between items-center pt-0 space-y-4 sm:flex sm:space-y-0'>
                    <x-secondary-button wire:click="closeModal()">
                        {{ __('forms.cansel') }}
                    </x-secondary-button>

                    <button
                        type='button'
                        wire:click='createRequest'
                        class='text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'
                    >
                        {{ $hasInitContract ? __('forms.confirm') : __('forms.continue') }}
                    </button>
                </div>
            </x-slot>
        </x-alert-modal>
    @elseif($showModal == 'show_contract')
        <x-alert-modal name='title'>
            <x-slot name='title'>
                {{ __('forms.contract') }}
            </x-slot>

            <x-slot name='text'>
                <x-forms.form-group class='mb-4'>
                    <x-slot name='label'>
                        <x-forms.label for='inserted_at' class='default-label'>
                            {{ __('forms.insertedAtContract') }} *
                        </x-forms.label>
                    </x-slot>
                    <x-slot name='input'>
                        <x-forms.input
                            disabled
                            class='default-input'
                            value="{{ $contract->inserted_at ?? '' }}"
                            type='datetime'
                            id='inserted_at'
                        />
                    </x-slot>
                </x-forms.form-group>

                <x-forms.form-group class='mb-4'>
                    <x-slot name='label'>
                        <x-forms.label for='contractor_base' class='default-label'>
                            {{ __('forms.statusReason') }} *
                        </x-forms.label>
                    </x-slot>
                    <x-slot name='input'>
                        <x-forms.input
                            disabled class='default-input'
                            value="{{ $contract->status_reason ?? '' }}"
                            type='text'
                            id='status_reason'
                        />
                    </x-slot>
                </x-forms.form-group>

                <x-forms.form-group class='mb-4'>
                    <x-slot name='label'>
                        <x-forms.label for='contractor_base' class='default-label'>
                            {{ __('forms.contractor_rmsp_amount') }} *
                        </x-forms.label>
                    </x-slot>
                    <x-slot name='input'>
                        <x-forms.input
                            disabled class='default-input'
                            value="{{ $contract->contractor_rmsp_amount ?? '' }}"
                            type='text'
                            id='status_reason'
                        />
                    </x-slot>
                </x-forms.form-group>
            </x-slot>

            <x-slot name='button'>
                <div class='justify-between items-center pt-0 space-y-4 sm:flex sm:space-y-0'>
                    <button
                        type='button'
                        wire:click='closeModal'
                        class='py-2 px-4 w-full text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 sm:w-auto hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600'
                    >
                        {{ __('forms.close') }}
                    </button>
                </div>
            </x-slot>
        </x-alert-modal>
    @endif
    {{--    @include('livewire.employee._parts._employee_form') --}}
</div>
