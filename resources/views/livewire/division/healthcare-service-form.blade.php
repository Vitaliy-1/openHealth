<div>
    <x-section-title>
        <x-slot name="title">{{ __('Послуги') }}</x-slot>
        <x-slot name="description">{{ __('Послуги') }}</x-slot>
    </x-section-title>
    <div class="mb-10 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b flex justify-end border-stroke px-7 py-4 dark:border-strokedark">
            <button type="button" class="btn-green" wire:click="create">
                {{__('Додати Послугу')}}
            </button>
        </div>
        <x-tables.table>
            <x-slot name="headers" :list="$tableHeaders"></x-slot>
            <x-slot name="tbody">
                @if($healthcare_services )
                    @foreach($healthcare_services as $k=>$item)
                        <tr>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$item->uuid ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white"> {{$item->category ?? ''}}</p>

                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$item['type'] ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{ $dictionaries['SPECIALITY_TYPE'][$item->speciality_type] ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                @if($item->status == 'DEACTIVATED')
                                    <span class="text-meta-1">{{__('Не активний')}}</span>
                                @else
                                    <span class="text-meta-3">{{__('Активний')}}</span>
                                @endif
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <div class="flex justify-center">
                                    <div
                                        x-data="{
            open: false,
            toggle() {
                if (this.open) {
                    return this.close()
                }

                this.$refs.button.focus()

                this.open = true
            },
            close(focusAfter) {
                if (! this.open) return

                this.open = false

                focusAfter && focusAfter.focus()
            }
        }"
                                        x-on:keydown.escape.prevent.stop="close($refs.button)"
                                        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                                        x-id="['dropdown-button']"
                                        class="relative">
                                        <button
                                            x-ref="button"
                                            x-on:click="toggle()"
                                            :aria-expanded="open"
                                            :aria-controls="$id('dropdown-button')"
                                            type="button"
                                            class="hover:text-primary">
                                            <svg class="fill-current" width="18" height="18"
                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                                            </svg>
                                        </button>
                                        <div
                                            x-ref="panel"
                                            x-show="open"
                                            x-transition.origin.top.left
                                            x-on:click.outside="close($refs.button)"
                                            :id="$id('dropdown-button')"
                                            style="display: none;"
                                            class="absolute right-0 mt-2 w-40 rounded-md bg-white shadow-md z-50">
                                            <a wire:click="edit({{ $item }})" href="#"
                                               class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                                {{__('forms.edit')}}
                                            </a>
                                            @if($item->status == 'ACTIVE')
                                                <a wire:click="deactivate({{ $item }})" href="#"
                                                   class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                                    {{__('forms.deactivate')}}
                                                </a>
                                            @else
                                                <a wire:click="activate({{ $item }})" href="#"
                                                   class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                                    {{__('forms.activate')}}
                                                </a>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                @endif
            </x-slot>
        </x-tables.table>

        <div class="footer border-b flex justify-start border-stroke px-7 py-2  flex justify-start">
            <a class="inline-flex items-center justify-center rounded-md border border-black  py-2 text-center font-medium text-black hover:bg-opacity-90 lg:px-8 xl:px-10"
               href="{{route('division.index')}}">{{__('Назад')}}</a>
        </div>
    </div>

    @include('livewire.division._parts._healthcare_service_form')
</div>


