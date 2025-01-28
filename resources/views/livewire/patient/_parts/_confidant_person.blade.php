<fieldset class="fieldset">
    <legend class="legend">
        {{ __('forms.confidantPersonDocumentsRelationship') }}
    </legend>

    <x-tables.table>
        <x-slot name="headers"
                :list="[__('forms.documentType'), __('forms.number'), __('forms.issuedBy'), __('forms.issuedAt'), __('forms.activeTo'), __('forms.action')]">
        </x-slot>

        <x-slot name="tbody">
            @isset($documentsRelationship)
                @foreach($documentsRelationship as $key => $documentRelationship)
                    @continue($key === 'personId')
                    <tr wire:key="{{ $key }}">
                        <td class="border-b border-[#eee] px-4 py-5 pl-9 dark:border-strokedark xl:pl-11">
                            {{ $documentRelationship['type'] ?? '' }}
                        </td>
                        <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                            {{ $documentRelationship['number'] ?? '' }}
                        </td>
                        <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                            {{ $documentRelationship['issuedBy'] ?? '' }}
                        </td>
                        <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                            {{ $documentRelationship['issuedAt'] ?? '' }}
                        </td>
                        <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                            {{ $documentRelationship['activeTo'] ?? '' }}
                        </td>

                        <td class="border-b border-[#eee] flex px-4 py-5 dark:border-strokedark">
                            <a wire:click.prevent="edit('documentsRelationship', {{ $key }})" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5"
                                     stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                            </a>
                            <a wire:click.prevent="remove('documentsRelationship', {{ $key }})" href="#">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                     viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endisset
        </x-slot>
    </x-tables.table>

    <div class="mb-6 mt-6 flex flex-wrap xl:gap-7.5">
        <a wire:click.prevent="create('documentsRelationship')"
           class="text-sm inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline"
           href="#">
            {{ __('forms.addDocument') }}
        </a>
    </div>
</fieldset>
