

<div class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <h5 class="mb-5 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        {{__('forms.specialities')}}
    </h5>
    <x-tables.table>
        <x-slot name="headers"
                :list="[__('Спеціальність'),__('Орган що видав'),__(' Рівень спеціальності'),__('Номер свідоцтва'),__('forms.actions')]"></x-slot>
        <x-slot name="tbody">
            @isset($employeeRequest->specialities)
                @foreach($employeeRequest->specialities as $k => $speciality)
                    <tr>
                        <td class="border-b border-[#eee] px-4 py-5 pl-9 dark:border-strokedark xl:pl-11">
                            {{$speciality['speciality'] ?? ''}}
                        </td>
                        <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                            {{$speciality['attestationName'] ?? '' }}
                        </td>
                        <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                            {{$speciality['level'] ?? ''}}
                        </td>
                        <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                            {{$speciality['certificateNumber'] ?? ''}}
                        </td>
                        <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                            <a  x-show="!employeeId" wire:click.prevent="edit('specialities',{{$k}},'speciality')" href="">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endisset
        </x-slot>
    </x-tables.table>
    <div class="mb-6 mt-6 flex flex-wrap gap-5 xl:gap-7.5">
        <a  x-show="!employeeId" wire:click.prevent="create('specialities','speciality')"
           class="text-sm inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline"
           href="">{{__('forms.addEducation')}}</a>
    </div>
</div>

