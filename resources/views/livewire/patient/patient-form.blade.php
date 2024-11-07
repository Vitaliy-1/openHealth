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

        @include('livewire.patient._parts._patient')
        @include('livewire.patient._parts._documents')
        @include('livewire.patient._parts._identity')
        @include('livewire.patient._parts._contact_data')
        @include('livewire.patient._parts._emergency_contact')
        @include('livewire.patient._parts._addresses')

        <div class="py-4">
            <h3 class="font-medium text-2xl	text-black dark:text-white">
                {{ __('forms.patient_legal_representative') }}
            </h3>
        </div>

        <div class="mb-4 flex flex-col gap-6 xl:flex-row">
            <x-forms.form-group class="xl:w-1/2 flex items-center gap-3">
                <x-slot name="label">
                    <x-forms.label class="default-label" for="is_incapable">
                        {{ __('forms.incapable') }}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-checkbox class="default-checkbox mb-2"
                                wire:model.live="is_incapable"
                                id="is_incapable" name="is_incapable"
                    />
                </x-slot>
                @error('is_incapable')
                <x-slot name="error">
                    <x-forms.error>
                        {{ $message }}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </div>

        @if($is_incapable)
            @include('livewire.patient._parts._confidant_person')
            @include('livewire.patient._parts._confidant_person_document')
            @include('livewire.patient._parts._confidant_person_documents_relationship')
            @include('livewire.patient._parts._legal_representation_contact')
        @endif

        @include('livewire.patient._parts._authentication_methods')

        <div class="mb-4 flex flex-col gap-6 xl:flex-row justify-between items-center">
            <div class="xl:w-1/4 text-left">
                <x-secondary-button wire:click="closeModal()">
                    {{ __('Назад') }}
                </x-secondary-button>
            </div>
            <div class="xl:w-1/4 text-right">
                <button wire:click="signedComplete" type="button" class="btn-primary">
                    {{ __('Відправити на затвердження') }}
                </button>
            </div>
        </div>
        <div wire:loading role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
            <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                 viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor"
                />
                <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentFill"
                />
            </svg>
        </div>
    </div>

    @if($showModal === 'documents')
        @include('livewire.patient._parts.modals._modal_documents')
    @elseif($showModal === 'confidant_person_documents')
        @include('livewire.patient._parts.modals._modal_person_documents')
    @elseif($showModal === 'confidant_person_documents_relationship')
        @include('livewire.patient._parts.modals._modal_confidant_person_documents_relationship')
    @endif
</div>
