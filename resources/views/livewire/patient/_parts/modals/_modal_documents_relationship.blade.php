<x-dialog-modal maxWidth="3xl" class="w-3 h-full" wire:model="showModal">
    <x-slot name="title">
        {{ __('forms.documents') }}
    </x-slot>

    <x-slot name="content">
        <x-forms.forms-section-modal
            submit="{!! $mode === 'edit' ? 'update(\'documentsRelationship\', ' . $keyProperty . ')' : 'store(\'documentsRelationship\')' !!}">
            <x-slot name="form">
                <x-forms.form-row class="flex-col">
                    <x-forms.form-group class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="documents_type" class="default-label">
                                {{ __('forms.documentType') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.select class="default-select"
                                            wire:model="patientRequest.documentsRelationship.type"
                                            id="documents_type"
                            >
                                <x-slot name="option">
                                    <option>{{ __('forms.select') }} {{ __('forms.type') }}</option>
                                    @foreach($this->dictionaries['DOCUMENT_RELATIONSHIP_TYPE'] as $key => $document)
                                        <option value="{{ $key }}" wire:key="{{ $key }}">{{ $document }}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>
                        </x-slot>

                        @error('patientRequest.documentsRelationship.type')
                        <x-slot name="error">
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>

                    <x-forms.form-group class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="documents_number" class="default-label">
                                {{ __('forms.documentNumber') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input class="default-input"
                                           wire:model="patientRequest.documentsRelationship.number"
                                           type="text"
                                           id="documents_number"
                            />
                        </x-slot>

                        @error('patientRequest.documentsRelationship.number')
                        <x-slot name="error">
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                </x-forms.form-row>

                <x-forms.form-row class="flex-col">
                    <x-forms.form-group class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="documents_issued_by" class="default-label">
                                {{ __('forms.documentIssuedBy') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input class="default-input"
                                           wire:model="patientRequest.documentsRelationship.issuedBy"
                                           type="text"
                                           id="documents_issued_by"
                                           placeholder="{{ __('forms.documentIssuedBy') }}"
                            />
                        </x-slot>

                        @error('patientRequest.documentsRelationship.issuedBy')
                        <x-slot name="error">
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>

                    <x-forms.form-group class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="confidant_person_documents_issued_at" class="default-label">
                                {{ __('forms.documentIssuedAt') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input-date :maxDate="now()->format('Y-m-d')"
                                                wire:model="patientRequest.documentsRelationship.issuedAt"
                                                id="confidant_person_documents_issued_at"
                            />
                        </x-slot>

                        @error('patientRequest.documentsRelationship.issuedAt')
                        <x-slot name="error">
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                </x-forms.form-row>

                <x-forms.form-row class="flex-col">
                    <x-forms.form-group class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="active_to" class="default-label">
                                {{ __('forms.activeTo') }}
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input-date :minDate="now()->format('Y-m-d')"
                                                wire:model="patientRequest.documentsRelationship.activeTo"
                                                id="active_to"
                            />
                        </x-slot>

                        @error('patientRequest.documentsRelationship.activeTo')
                        <x-slot name="error">
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                </x-forms.form-row>

                <x-forms.form-row class="flex-col justify-between items-center">
                    <div class="xl:w-1/4 text-left">
                        <x-secondary-button wire:click="closeModalModel">
                            {{ __('forms.close') }}
                        </x-secondary-button>
                    </div>

                    <div class="xl:w-1/4 text-right">
                        <x-button type="submit" class="btn-primary">
                            {{ __('forms.save') }}
                        </x-button>
                    </div>
                </x-forms.form-row>
            </x-slot>
        </x-forms.forms-section-modal>
    </x-slot>
</x-dialog-modal>
