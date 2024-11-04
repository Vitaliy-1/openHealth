<div wire:after="initializeFlatpickr" class="mb-4" x-data="{show_document: false}">
    <div class="border-b border-stroke py-4 dark:border-strokedark">
        <h3 class="font-medium text-2xl	text-black dark:text-white">
            {{ __('forms.confidant_person_documents_relationship') }}
        </h3>
    </div>
    <table class="w-full table-auto">
        <thead>
        <tr class="bg-gray-2 text-left dark:bg-meta-4">
            <th class="min-w-[220px] px-4 py-4 font-medium text-black dark:text-white xl:pl-11">
                {{ __('forms.document_type') }}
            </th>
            <th class="min-w-[150px] px-4 py-4 font-medium text-black dark:text-white">
                {{ __('forms.serial_and_number') }}
            </th>
            <th class="min-w-[120px] px-4 py-4 font-medium text-black dark:text-white">
                {{ __('forms.issued_by') }}
            </th>
            <th class="px-4 py-4 font-medium text-black dark:text-white">
                {{ __('forms.issued_at') }}
            </th>
            <th class="px-4 py-4 font-medium text-black dark:text-white"></th>
        </tr>
        </thead>
        <tbody>
        @isset($patient->confidant_person_documents_relationship)
            @foreach($patient->confidant_person_documents_relationship as $k => $confidant_person_documents_relationship)
                <tr>
                    <td class="border-b border-[#eee] px-4 py-5 pl-9 dark:border-strokedark xl:pl-11">
                        {{ $confidant_person_documents_relationship['type'] ?? '' }}
                    </td>
                    <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                        {{ $confidant_person_documents_relationship['number'] ?? '' }}
                    </td>
                    <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                        {{ $confidant_person_documents_relationship['issued_by'] ?? '' }}
                    </td>
                    <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                        {{ $confidant_person_documents_relationship['issued_at'] ?? '' }}
                    </td>

                    <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                        <a wire:click.prevent="edit('confidant_person_documents_relationship', {{ $k }})" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                            </svg>
                        </a>
                    </td>
                </tr>
            @endforeach
        @endisset
        </tbody>
    </table>
    <div class="mb-6 mt-6 flex flex-wrap xl:gap-7.5">
        <a wire:click.prevent="create('confidant_person_documents_relationship')" class="text-primary" href="#">{{ __('forms.add_document') }}</a>
    </div>
</div>
