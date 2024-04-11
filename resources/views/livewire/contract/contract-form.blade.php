<div>
    <x-section-title>
        <x-slot name="title">
            {{$contract_request->previous_request_id === '' ? __('forms.add_contract') :  __('forms.edit_contract',['contract' => $contract_request->previous_request_id]) }}
        </x-slot>
        <x-slot name="description">
            {{$contract_request->previous_request_id === '' ? __('forms.add_contract') :  __('forms.edit_contract',['contract' => $contract_request->previous_request_id]) }}
        </x-slot>
    </x-section-title>

    <div class="flex bg-white  pb-10  p-6 flex-col ">
        <div class="grid grid-cols-1   gap-9 sm:grid-cols-2">
            <div class="flex flex-col gap-9">
                <div class="dark:bg-boxdark">
                    <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
                        <h3 class="font-medium text-black dark:text-white">
                            {{__('forms.legal_entity_info')}}
                        </h3>
                    </div>
                    <div class="flex flex-col gap-5.5 p-6.5">
                        <x-forms.form-group class="">
                            <x-slot name="label">
                                <x-forms.label for="legal_entity_name" class="default-label">
                                    {{__('forms.legal_entity_name')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input disabled class="default-input" value="{{$legalEntity->name}}" type="text"
                                               id="legal_entity_name"/>
                            </x-slot>

                        </x-forms.form-group>
                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="legal_entity_owner" class="default-label">
                                    {{__('forms.legal_entity_owner')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input disabled class="default-input" value="{{$legalEntity->beneficiary}}"
                                               type="text"
                                               id="legal_entity_owner"/>
                            </x-slot>

                        </x-forms.form-group>

                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1   gap-9 sm:grid-cols-2">
            <div class="flex flex-col gap-9">
                <div class="dark:bg-boxdark">
                    <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
                    </div>

                    <div class="flex flex-col gap-5.5 p-6.5">
                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="contractor_base" class="default-label">
                                    {{__('forms.contractor_base')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input disabled="" class="default-input" wire:model="contract_request.contractor_base"
                                               type="text"
                                               id="contractor_base"/>
                            </x-slot>
                            @error('contract_request.contractor_base')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="contractor_rmsp_amount" class="default-label">
                                    {{__('forms.contractor_rmsp_amount')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input disabled="" class="default-input" wire:model="contract_request.contractor_rmsp_amount"
                                               type="number"
                                               id="contractor_rmsp_amount"/>
                            </x-slot>
                            @error('contract_request.contractor_rmsp_amount')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1   gap-9 sm:grid-cols-2">
            <div class="flex flex-col gap-9">
                <div class="dark:bg-boxdark">
                    <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
                        <h3 class="font-medium text-black dark:text-white">
                            {{__('forms.documents_medical_organization')}}
                        </h3>
                    </div>
                    <div class="flex flex-col gap-5.5 p-6.5">

                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="contractor_base" class="default-label">
                                    {{__('forms.statute_md5')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.file wire:model="contract_request.statute_md5" type="file"
                                              id="statute_md5"/>
                            </x-slot>
                            @error('contract_request.statute_md5')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="additional_document_md5" class="default-label">
                                    {{__('forms.additional_document_md5')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.file wire:model="contract_request.additional_document_md5" type="file"
                                              id="additional_document_md5"/>
                            </x-slot>
                            @error('contract_request.additional_document_md5')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>


                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-9">
            <div class="dark:bg-boxdark">
                <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        {{__('forms.сontract_term')}}

                    </h3>
                </div>

                <div class="grid grid-cols-3 gap-5.5 p-6.5">
                    <x-forms.form-group>
                        <x-slot name="label">
                            <x-forms.label for="contractor_base" class="default-label">
                                {{__('forms.contract_type')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.select
                                class="default-input" disabled wire:model="contract_request.id_form" type="text"
                                id="position"
                            >
                                <x-slot name="option">
                                    @foreach($this->dictionaries['CONTRACT_TYPE'] as $k=>$contract_type )
                                        <option selected  value="{{$k}}">{{$contract_type}}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>

                        </x-slot>

                    </x-forms.form-group>
                    <x-forms.form-group>
                        <x-slot name="label">
                            <x-forms.label for="start_date" class="default-label">
                                {{__('forms.start_date_contract')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.datapicker   wire:model="contract_request.start_date"
                                                id="start_date"/>
                        </x-slot>
                        @error('contract_request.start_date')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                    <x-forms.form-group >
                        <x-slot name="label">
                            <x-forms.label for="end_date" class="default-label">
                                {{__('forms.end_date_contract')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.datapicker wire:model="contract_request.end_date"
                                                id="end_date"/>
                        </x-slot>
                        @error('contract_request.end_date')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>

                </div>
            </div>
        </div>
        <div class="grid grid-cols-1   gap-9 sm:grid-cols-2">
            <div class="flex flex-col gap-9">
                <div class="dark:bg-boxdark">
                    <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
                        <h3 class="font-medium text-black dark:text-white">
                            {{__('forms.payment_details')}}
                        </h3>
                    </div>
                    <div class="flex flex-col gap-5.5 p-6.5">
                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="bank_name" class="default-label">
                                    {{__('forms.bank_name')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input"
                                               wire:model="contract_request.contractor_payment_details.bank_name"
                                               type="text"
                                               id="bank_name"/>
                            </x-slot>
                            @error('contract_request.contractor_payment_details.bank_name')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>

                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="MFO" class="default-label">
                                    {{__('forms.MFO')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input"
                                               wire:model="contract_request.contractor_payment_details.MFO" type="text"
                                               id="MFO"/>
                            </x-slot>
                            @error('contract_request.contractor_payment_details.MFO')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>

                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="payer_account" class="default-label">
                                    {{__('forms.payer_account')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input"
                                               wire:model="contract_request.contractor_payment_details.payer_account"
                                               type="text"
                                               id="payer_account"/>
                            </x-slot>
                            @error('contract_request.contractor_payment_details.payer_account')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>

                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1   gap-9 sm:grid-cols-2">
            <div class="flex flex-col gap-9">
                <div class="dark:bg-boxdark">
                    <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
                        <h3 class="font-medium text-black dark:text-white">
                            {{__('forms.places_of_service')}}
                        </h3>
                    </div>
                    <div class="flex flex-col gap-5.5 p-6.5">
                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="contractor_divisions" class="default-label">
                                    {{__('forms.division')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.multi-select id="contractor_divisions" class="default-input"  wire:model="contract_request.contractor_divisions">
                                    <x-slot name="option">
                                        @foreach($divisions as $k=>$division )
                                            <option value="{{$division->id}}">{{$division->name}}</option>
                                        @endforeach
                                    </x-slot>
                                </x-forms.multi-select>
                            </x-slot>
                            @error('contract_request.contractor_divisions')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-9">
            <div class="dark:bg-boxdark">
                <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        {{__('forms.involved_persons')}}
                    </h3>
                </div>
                <div class="flex flex-col gap-5.5 p-6.5">
                    @if($external_contractors)

                    <table class="w-full table-auto">
                        <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="px-4 py-4 font-medium text-black dark:text-white">
                                {{__('forms.legal_entity')}}

                            </th>
                            <th class="min-w-[220px] px-4 py-4 font-medium text-black dark:text-white xl:pl-11">
                                {{__('forms.external_contractor_number')}}
                            </th>
                            <th class="min-w-[150px] px-4 py-4 font-medium text-black dark:text-white">
                                {{__('forms.external_contractor_issued_at')}}
                            </th>

                            <th class="px-4 py-4 font-medium text-black dark:text-white">
                                {{__('forms.external_contractor_expires_at')}}
                            </th>
                            <th class="px-4 py-4 font-medium text-black dark:text-white">

                            </th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($external_contractors as $key => $external_contractor)
                                <tr>
                                    <td class="border-b border-[#eee] px-4 py-5 pl-9 dark:border-strokedark xl:pl-11">
                                        {{$external_contractor['legal_entity']['name'] ?? ''}}
                                    </td>
                                    <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                                        {{$external_contractor['contract']['number'] ?? ''}}
                                    </td>
                                    <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                                        {{$external_contractor['contract']['issued_at'] ?? ''}}

                                    </td>
                                    <td class="border-b border-[#eee] px-4 py-5 dark:border-strokedark">
                                        {{$external_contractor['contract']['expires_at'] ?? ''}}

                                    </td>

                                    <td class="border-b border-[#eee] flex px-4 py-5 dark:border-strokedark">
                                        <a wire:click.prevent="editExternalContractors({{$key}})" href="">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </a>
                                        <a wire:click.prevent="deleteExternalContractors({{$key}})" href="">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                    <a class="text-primary" wire:click.prevent="openModal()" href="">+ {{__('forms.add_involved_person')}}</a>
                </div>

            </div>
        </div>
        <div class="flex flex-col gap-9">
                <div class="dark:bg-boxdark">
                    <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
                        <h3 class="font-medium text-black dark:text-white">
                        </h3>
                    </div>
                    <div class="flex flex-col gap-5.5 p-6.5">
                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="consent_text" class="default-label text-sm">
                                   {{$dictionaries['CAPITATION_CONTRACT_CONSENT_TEXT']['APPROVED']}}
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-checkbox"
                                               wire:model="contract_request.consent_text"
                                               type="checkbox"
                                               id="consent_text"/>
                            </x-slot>
                            @error('contract_request.consent_text')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                    </div>
                </div>
            </div>


        <div class="mb-4.5 pt-10 flex flex-col gap-6 xl:flex-row justify-between items-center ">
            <div class="xl:w-1/4 text-left">
                <a class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150" href="{{route('contract.index')}}">
                    {{__('forms.back')}}
                </a>

            </div>
            <div class="xl:w-1/4 text-right">
                <button wire:click="sendApiRequest()" type="button" class="btn-primary">
                    {{__('forms.send_for_approval')}}
                </button>
            </div>
        </div>
        <div wire:loading role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
            <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                 viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor"/>
                <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentFill"/>
            </svg>
        </div>

        @include('livewire.contract._parts.modals._external_contractors')

    </div>


</div>
