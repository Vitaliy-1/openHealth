<x-dialog-modal maxWidth="3xl" wire:model="showModal">
    <x-slot name="title">
        {{ __('patients.identity_document') }}
    </x-slot>

    <x-slot name="content">
        <x-forms.forms-section-modal
            submit="{!! $mode === 'edit' ? 'update(\'documentsRelationship\', ' . $keyProperty . ')' : 'store(\'documentsRelationship\')' !!}">
            <x-slot name="form">
                <x-forms.form-row class="flex-col">
                    <x-forms.form-group class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="documents_type" class="default-label">
                                {{ __('forms.document_type') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.select class="default-select"
                                            wire:model="form.documentsRelationship.type"
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

                        @error('form.documentsRelationship.type')
                        <x-slot name="error">
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>

                    <x-forms.form-group class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="documentsNumber" class="default-label">
                                {{ __('forms.document_number') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input class="default-input"
                                           wire:model="form.documentsRelationship.number"
                                           type="text"
                                           id="documentsNumber"
                            />
                        </x-slot>

                        @error('form.documentsRelationship.number')
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
                                {{ __('forms.document_issued_by') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input class="default-input"
                                           wire:model="form.documentsRelationship.issuedBy"
                                           type="text"
                                           id="documents_issued_by"
                                           placeholder="{{ __('forms.document_issued_by') }}"
                            />
                        </x-slot>

                        @error('form.documentsRelationship.issuedBy')
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
                                {{ __('forms.document_issued_at') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input-date :maxDate="now()->format('Y-m-d')"
                                                wire:model="form.documentsRelationship.issuedAt"
                                                id="confidant_person_documents_issued_at"
                            />
                        </x-slot>

                        @error('form.documentsRelationship.issuedAt')
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
                                {{ __('forms.active_to') }}
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input-date :minDate="now()->format('Y-m-d')"
                                                wire:model="form.documentsRelationship.activeTo"
                                                id="active_to"
                            />
                        </x-slot>

                        @error('form.documentsRelationship.activeTo')
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
