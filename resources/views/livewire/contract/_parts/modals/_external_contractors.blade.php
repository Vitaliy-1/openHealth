<x-dialog-modal maxWidth="3xl" class="w-1 h-full" wire:model.live="showModal">
    <x-slot name="title">
        {{__('forms.external_contractors')}}
    </x-slot>
    <x-slot name="content">
        <x-forms.forms-section-modal submit="addExternalContractors({{$external_contractor_key}})">
            <x-slot name="form">
                <div class="mb-4.5 flex flex-col gap-6 ">
                    <div class="grid grid-cols-1	  gap-9 sm:grid-cols-2">
                        <x-forms.form-group class="relative" x-data="{ open: false }">
                            <x-slot name="label">
                                <x-forms.label for="documents_issued_by" class="default-label">
                                    {{__('forms.edrpou')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <div >
                                    <x-forms.input class="default-input"
                                                   type="text"
                                                   wire:model="legalEntity_search"
                                                   wire:keyup.debounce.500ms="getLegalEntityApi; open = true"
                                                   id=""/>

                                    <div x-show="open" x-ref="dropdown" wire:target="getLegalEntityApi">
                                        @if($legalEntityApi)
                                            <div class="z-10 max-h-96 overflow-auto w-full	 absolute  bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                                    aria-labelledby="dropdownHoverButton">
                                                    @foreach($legalEntityApi as $legalEntity)
                                                        <li>
                                                            <a x-on:click.prevent="
      $wire.set('legalEntity_search', '{{ $legalEntity['edr']['name'] }}');

    $wire.set('contract_request.external_contractors.legal_entity.name', '{{ $legalEntity['edr']['name'] }}');
$wire.set('contract_request.external_contractors.legal_entity.id', '{{ $legalEntity['id'] }}');
    open = false;"
                                                               href="#"
                                                               class="pointer block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                                                {{ $legalEntity['edr']['name'] }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>

                                    </div>
                                        @endif
                                    </div>
                                </div>
                            </x-slot>
                            @error('contract_request.external_contractors.legal_entity.name')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                    </div>
                    <div class="grid grid-cols-2 mb-4.5		  gap-9 sm:grid-cols-2">
                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="contract_number" class="default-label">
                                    {{__('forms.external_contractors_number')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input class="default-input"
                                               wire:model="contract_request.external_contractors.contract.number"
                                               type="text"
                                               id="contract_number"/>
                            </x-slot>
                            @error('contract_request.external_contractors.contract.number')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                    </div>
                    <div class="grid grid-cols-2 mb-4.5		  gap-9 sm:grid-cols-2">

                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="contract_issued_at" class="default-label">
                                    {{__('forms.external_contractors_issued_at')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input type="date"
                                    class="default-input"

                                    wire:model="contract_request.external_contractors.contract.issued_at"
                                    id="contract_issued_at"/>
                            </x-slot>
                            @error('contract_request.external_contractors.contract.issued_at')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>

                        <x-forms.form-group>
                            <x-slot name="label">
                                <x-forms.label for="contract_expires_att" class="default-label">
                                    {{__('forms.external_contractors_expires_at')}} *
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.input type="date"
                                     class="default-input"
                                    wire:model="contract_request.external_contractors.contract.expires_at"
                                    id="contract_expires_at"/>
                            </x-slot>
                            @error('contract_request.external_contractors.contract.expires_at')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                    </div>
                    <div class="grid grid-cols-2 	mb-4.5	  gap-9 sm:grid-cols-2">

                        <x-forms.form-group class="">
                            <x-slot name="label">
                                <x-forms.label for="division" class="default-label">
                                    {{__('forms.division')}}*
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.select
                                    class="default-input"  type="text"
                                    id="division"
                                    wire:change="getHealthcareServices($event.target.value,)"
                                >
                                    <x-slot name="option">
                                        <option value="">{{__('forms.select')}}</option>
                                        @foreach($divisions as $k=>$division )
                                            <option value="{{$division->id}}">{{$division->name}}</option>
                                        @endforeach
                                    </x-slot>
                                </x-forms.select>

                            </x-slot>
                            @error('contract_request.external_contractors.divisions.name')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>
                        <x-forms.form-group class="">
                            <x-slot name="label">
                                <x-forms.label for="division_external_contractors" class="default-label">
                                    {{__('forms.medical_service')}}*
                                </x-forms.label>
                            </x-slot>
                            <x-slot name="input">
                                <x-forms.select
                                    class="default-input" wire:model="contract_request.external_contractors.divisions.medical_service" type="text"
                                    id="division_external_contractors"
                                >
                                    <x-slot name="option">
                                        @if($healthcareServices)
                                            @foreach($healthcareServices as $k=>$healthcareService )
                                                <option
                                                    value="{{$healthcareService->id}}">{{$healthcareService->speciality_type}}</option>
                                            @endforeach
                                        @endif
                                    </x-slot>
                                </x-forms.select>

                            </x-slot>
                            @error('contract_request.external_contractors.divisions.medical_service')
                            <x-slot name="error">
                                <x-forms.error>
                                    {{$message}}
                                </x-forms.error>
                            </x-slot>
                            @enderror
                        </x-forms.form-group>

                    </div>
                </div>
                <div class="mb-4.5 mt-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center ">
                    <div class="xl:w-1/4 text-left">
                        <x-secondary-button wire:click="closeModal()">
                            {{__('forms.close')}}
                        </x-secondary-button>
                    </div>
                    <div class="xl:w-1/4 text-right">
                        <x-button type="submit" class="btn-primary">
                            {{__('forms.add')}}
                        </x-button>
                    </div>
                </div>

            </x-slot>

        </x-forms.forms-section-modal>
    </x-slot>
</x-dialog-modal>





