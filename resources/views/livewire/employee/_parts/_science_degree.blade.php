<div class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <h5 class="mb-5 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        {{__('forms.education')}}
    </h5>
    <x-tables.table>
        <x-slot name="headers" :list="[
    __('forms.degree'),
    __('forms.issuedDate'),
    __('forms.institutionName'),
    __('forms.speciality'),
    __('forms.diplomaNumber'),
    __('forms.actions'),
    ]">

        </x-slot>
        <x-slot name="tbody">
            @if(!empty($employeeRequest->scienceDegree))
                {{--            @foreach($employee->scienceDegree as $k=>$scienceDegree)--}}
                <tr>
                    <td class="border-b border-[#eee] px-4 py-5 pl-9 dark:border-strokedark xl:pl-11">
                        {{$employee->scienceDegree['degree'] ?? ''}}
                    </td>
                    <td class="border-b border-[#eee] px-4 py-5 pl-9 dark:border-strokedark xl:pl-11">
                        {{$employee->scienceDegree['issuedDate'] ?? ''}}
                    </td>
                    <td class="border-b border-[#eee] px-4 py-5 pl-9 dark:border-strokedark xl:pl-11">
                        {{$employee->scienceDegree['institutionName'] ?? ''}}
                    </td>
                    <td class="border-b border-[#eee] px-4 py-5 pl-9 dark:border-strokedark xl:pl-11">
                        {{$employee->scienceDegree['speciality'] ?? ''}}
                    </td>
                    <td class="border-b border-[#eee] px-4 py-5 pl-9 dark:border-strokedark xl:pl-11">
                        {{$employee->scienceDegree['diplomaNumber'] ?? ''}}
                    </td>
                    <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                        <a x-show="!employeeId"  wire:click.prevent="edit('scienceDegree')" href="">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                {{--            @endforeach--}}
            @endisset
        </x-slot>
    </x-tables.table>

    <div class="mb-6 mt-6 flex flex-wrap gap-5 xl:gap-7.5">
        @if(empty($employee->scienceDegree))
            <a  x-show="!employeeId" wire:click.prevent="openModal('scienceDegree')"
               class="text-sm inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline"
               href="">{{__('forms.addScienceDegree')}}</a>
        @endif
    </div>

</div>
