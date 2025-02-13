<x-dialog-modal maxWidth="3xl" wire:model="showModal">
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
                                {{ __('forms.documentType') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.select class="default-select"
                                            wire:model="patientRequest.documents.type"
                                            id="documents_type"
                            >
                                <x-slot name="option">
                                    <option>{{ __('forms.select') }} {{ __('forms.type') }}</option>
                                    @foreach($this->dictionaries['DOCUMENT_TYPE'] as $key => $document)
                                        @continue($key === 'COMPLEMENTARY_PROTECTION_CERTIFICATE')
                                        <option value="{{ $key }}" wire:key="{{ $key }}">{{ $document }}</option>
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
                                {{ __('forms.documentNumber') }} *
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
                                {{ __('forms.documentIssuedBy') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input class="default-input"
                                           wire:model="patientRequest.documents.issuedBy"
                                           type="text"
                                           id="documents_issued_by"
                                           placeholder="{{ __('forms.documentIssuedBy') }}"
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
                                {{ __('forms.documentIssuedAt') }} *
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
                                {{ __('forms.expirationDate') }}
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input-date :minDate="now()->format('Y-m-d')"
                                                wire:model="patientRequest.documents.expirationDate"
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
