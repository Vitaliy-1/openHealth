<x-dialog-modal maxWidth="3xl" class="w-3 h-full z-999999" wire:model.live="showModal">
    <x-slot name="title">
        {{ __('forms.documents') }}
    </x-slot>

    <x-slot name="content">
        <x-forms.forms-section-modal
            submit="{!! $mode === 'edit' ? 'update(\'documents\', ' . $keyProperty . ')' : 'store(\'documents\')' !!}">
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
                                            wire:model.defer="patientRequest.documents.type"
                                            id="documents_type"
                            >
                                <x-slot name="option">
                                    <option>{{ __('forms.select') }} {{ __('forms.type') }}</option>
                                    @foreach($this->dictionaries['DOCUMENT_TYPE'] as $key => $document)
                                        <option value="{{ $key }}">{{ $document }}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>
                        </x-slot>

                        @error('patientRequest.documents.type')
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
                                {{ __('forms.document_number') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input class="default-input"
                                           wire:model="patientRequest.documents.number"
                                           type="text"
                                           id="documents_number"
                            />
                        </x-slot>

                        @error('patientRequest.documents.number')
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
                                           wire:model="patientRequest.documents.issuedBy"
                                           type="text"
                                           id="documents_issued_by"
                                           placeholder="{{ __('forms.document_issued_by') }}"
                            />
                        </x-slot>

                        @error('patientRequest.documents.issuedBy')
                        <x-slot name="error">
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>

                    <x-forms.form-group class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="documents_issued_at" class="default-label">
                                {{ __('forms.document_issued_at') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input-date :maxDate="now()->format('Y-m-d')"
                                                wire:model="patientRequest.documents.issuedAt"
                                                id="documents_issued_at"
                            />
                        </x-slot>

                        @error('patientRequest.documents.issuedAt')
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
                            <x-forms.label for="expiration_date" class="default-label">
                                {{ in_array($patientRequest->documents['type'] ?? '', ['NATIONAL_ID', 'COMPLEMENTARY_PROTECTION_CERTIFICATE', 'PERMANENT_RESIDENCE_PERMIT', 'REFUGEE_CERTIFICATE', 'TEMPORARY_CERTIFICATE', 'TEMPORARY_PASSPORT'])
                                    ? __('forms.expiration_date_required')
                                    : __('forms.expiration_date') }}
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input-date :minDate="now()->format('Y-m-d')"
                                                wire:model.blur="patientRequest.documents.expirationDate"
                                                id="expiration_date"
                            />
                        </x-slot>

                        @error('patientRequest.documents.expirationDate')
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
