@php
    $tableHeaders = [
        __('patients.code_and_name'),
        __('forms.type'),
        __('patients.priority'),
        __('patients.clinical_status'),
        __('patients.verification_status'),
        __('forms.comment'),
        __('forms.action')
    ];
@endphp
<div class="table-container">
    <div class="overflow-x-auto">
        <table class="table-base">
            <thead class="table-header">
            <tr>
                @foreach($tableHeaders as $tableHeader)
                    <th wire:key="{{ $loop->index }}" scope="col" class="px-4 py-3">
                        {{ $tableHeader }}
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($allEpisodes as $index => $episode)
                <tr class="border-b dark:border-gray-700">
                    <td class="px-4 py-3">
                        <span>
                            {{ $episode['conditions']['code']['coding'][0]['code'] }} / {{ $this->dictionaries['eHealth/ICPC2/condition_codes'][$episode['conditions']['code']['coding'][0]['code']] }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span>
                            {{ $this->dictionaries['eHealth/diagnosis_roles'][$episode['diagnoses']['role']['coding'][0]['code']] }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span>
                            {{ $episode['diagnoses']['rank'] ?? '' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span>
                            {{ $this->dictionaries['eHealth/condition_clinical_statuses'][$episode['conditions']['clinical_status']] }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span>
                            {{ $this->dictionaries['eHealth/condition_verification_statuses'][$episode['conditions']['verification_status']] }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <input wire:model="form.conditions.report_origin.text"
                               type="text"
                               name="comment"
                               id="comment_{{ $index }}"
                               class="input peer @error('form.conditions.report_origin.text') input-error @enderror"
                               placeholder=" "
                               autocomplete="off"
                        />

                        @error('form.conditions.report_origin.text')
                        <p class="text-error">
                            {{ $message }}
                        </p>
                        @enderror
                    </td>
                    <td class="px-4 py-3">
                        <div x-data="{ open: false }">
                            <button @click.prevent="open = !open">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                     viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                </svg>
                            </button>
                            <div x-show="open"
                                 @click.away="open = false"
                                 class="dropdown-menu absolute right-0 mt-3"
                            >
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                    <li>
                                        <a wire:click.prevent="editDiagnose({{ $index }})"
                                           href="#"
                                           class="dropdown-item-with-icon"
                                        >
                                            <svg width="18" height="19">
                                                <use xlink:href="#svg-edit"></use>
                                            </svg>
                                            {{ __('forms.edit') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a wire:click.prevent="destroyDiagnose({{ $index }})"
                                           href="#"
                                           class="dropdown-item-with-icon"
                                        >
                                            <svg width="18" height="19">
                                                <use xlink:href="#svg-edit"></use>
                                            </svg>
                                            {{ __('forms.delete') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
