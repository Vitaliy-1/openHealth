<x-dialog-modal maxWidth="3xl" wire:model="showModal">
    <x-slot name="title">
        {{ __('patients.identity_document') }}
    </x-slot>

    <x-slot name="content">
        <x-forms.forms-section-modal
            submit="{!! $mode === 'edit' ? 'update(\'documents\', ' . $keyProperty . ')' : 'store(\'documents\')' !!}">
            <x-slot name="form">
                <x-forms.form-row class="flex-col">
                    <x-forms.form-group class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="documentsType" class="default-label">
                                {{ __('forms.document_type') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.select class="default-select"
                                            wire:model="form.documents.type"
                                            id="documentsType"
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

                        @error('form.documents.type')
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
                                           wire:model="form.documents.number"
                                           type="text"
                                           id="documentsNumber"
                            />
                        </x-slot>

                        @error('form.documents.number')
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
                            <x-forms.label for="documentsIssuedBy" class="default-label">
                                {{ __('forms.document_issued_by') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input class="default-input"
                                           wire:model="form.documents.issuedBy"
                                           type="text"
                                           id="documentsIssuedBy"
                                           placeholder="{{ __('forms.document_issued_by') }}"
                            />
                        </x-slot>

                        @error('form.documents.issuedBy')
                        <x-slot name="error">
                            <x-forms.error>
                                {{ $message }}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>

                    <x-forms.form-group class="xl:w-1/2">
                        <x-slot name="label">
                            <x-forms.label for="documentsIssuedAt" class="default-label">
                                {{ __('forms.document_issued_at') }} *
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input-date :maxDate="now()->format('Y-m-d')"
                                                wire:model="form.documents.issuedAt"
                                                id="documentsIssuedAt"
                            />
                        </x-slot>

                        @error('form.documents.issuedAt')
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
                                {{ __('forms.valid_until') }}
                            </x-forms.label>
                        </x-slot>

                        <x-slot name="input">
                            <x-forms.input-date :minDate="now()->format('Y-m-d')"
                                                wire:model="form.documents.expirationDate"
                                                id="expirationDate"
                            />
                        </x-slot>

                        @error('form.documents.expirationDate')
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
