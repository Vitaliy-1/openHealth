<div>
    <x-section-title>
        <x-slot name="title">
            {{ __('patients.add_patient') }}
        </x-slot>
        <x-slot name="description">
            {{ __('patients.add_patient') }}
        </x-slot>
    </x-section-title>

    <div class="flex bg-white p-6 flex-col">
        @if($viewState === 'default')

            @include('livewire.patient._parts._patient')
            @include('livewire.patient._parts._documents')
            @include('livewire.patient._parts._identity')
            @include('livewire.patient._parts._contact_data')
            @include('livewire.patient._parts._emergency_contact')

            <div
                class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ __('forms.patientLegalRepresentative') }}
                </h5>

                <x-forms.form-row class="flex-col">
                    <x-forms.form-group class="xl:w-1/2 flex items-center gap-3">
                        <x-slot name="label">
                            <x-forms.label class="default-label" for="is_incapable">
                                {{ __('forms.incapable') }}
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-checkbox class="default-checkbox mb-2"
                                        wire:model.live="isIncapable"
                                        id="is_incapable"
                            />
                        </x-slot>
                    </x-forms.form-group>
                </x-forms.form-row>

                @if($isIncapable)
                    <livewire:patient.patients-filter/>
                    @include('livewire.patient._parts._confidant_person_documents_relationship')
                @endif
            </div>

            @include('livewire.patient._parts._authentication_methods')

            <x-forms.form-row class="flex-col justify-between items-center">
                <div class="xl:w-1/4 text-left"></div>
                <div class="xl:w-1/4 text-right">
                    <x-button wire:click="store('patient')" type="submit"
                              class="btn-primary d-flex max-w-[150px]">
                        {{ __('forms.save') }}
                    </x-button>
                </div>
            </x-forms.form-row>

            <x-forms.form-row class="flex-col justify-between items-center">
                <div class="xl:w-1/4 text-left">
                    <x-secondary-button wire:click="closeModal">
                        {{ __('Назад') }}
                    </x-secondary-button>
                </div>

                <div class="xl:w-1/4 text-right">
                    <button wire:click="createPerson" type="button"
                            class="btn-primary {{ $isPatientStored ? '' : 'cursor-not-allowed' }}"
                        {{ $isPatientStored ? '' : 'disabled' }}>
                        {{ __('Відправити на затвердження') }}
                    </button>
                </div>
            </x-forms.form-row>

        @elseif($viewState === 'new')
            @include('livewire.patient._parts._signature')
        @endif
    </div>

    @if($showModal === 'documents')
        @include('livewire.patient._parts.modals._modal_documents')
    @elseif($showModal === 'documentsRelationship')
        @include('livewire.patient._parts.modals._modal_confidant_person_documents_relationship')
    @endif
</div>
