<div >

    <x-section-title>
        <x-slot name="title">{{ __('Місця надання послуг') }}</x-slot>
        <x-slot name="description">{{ __('Місця надання послуг') }}</x-slot>
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
                                <p class="text-black dark:text-white">{{$item->category ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$item->type ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$item->speciality_type ?? ''}}</p>
                            </td>

                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$item->status ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <div class="flex items-center space-x-3.5">
                                    <button class="hover:text-primary" wire:click="edit({{ $k}})">
                                        <svg class="fill-current" width="18" height="18" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                    <button class="hover:text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9.563C9 9.252 9.252 9 9.563 9h4.874c.311 0 .563.252.563.563v4.874c0 .311-.252.563-.563.563H9.564A.562.562 0 0 1 9 14.437V9.564Z" />
                                        </svg>

                                    </button>

                                </div>
                            </td>

                        </tr>
                    @endforeach
                @endif
            </x-slot>
        </x-tables.table>
    </div>

    @include('livewire.division._parts._healthcare_service_form')
</div>


