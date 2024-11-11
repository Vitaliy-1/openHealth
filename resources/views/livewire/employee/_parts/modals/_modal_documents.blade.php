<x-dialog-modal  maxWidth="3xl" class="w-3 h-full z-999999" wire:model.live="showModal">
    <x-slot name="title">
        {{__('forms.documents')}}
    </x-slot>
    <x-slot name="content">
        <x-forms.forms-section-modal submit="{!! $mode === 'edit' ? 'update(\'documents\',' . $key_property . ')' : 'store(\'documents\')' !!}">
                        <x-slot name="form">
                            <x-forms.form-row class="mb-4.5 flex flex-col gap-6   xl:flex-row">
                                <x-forms.form-group class="xl:w-1/2">
                                    <x-slot name="label">
                                        <x-forms.label for="documents_type" class="default-label">
                                            {{__('forms.document_type')}} *
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.select id="documents_type"
                                                        wire:model.defer="employee_request.documents.type"
                                                        class="default-select">
                                            <x-slot name="option">
                                                <option>{{__('Обрати тип')}}</option>
                                                @foreach($this->dictionaries['DOCUMENT_TYPE'] as $k=>$document )
                                                    <option value="{{$k}}">{{$document}}</option>
                                                @endforeach
                                            </x-slot>
                                        </x-forms.select>
                                    </x-slot>
                                    @error('employee_request.documents.type')
                                    <x-slot name="error">
                                        <x-forms.error>
                                            {{$message}}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>
                                <x-forms.form-group class="xl:w-1/2">
                                    <x-slot name="label">
                                        <x-forms.label for="documents_number" class="default-label">
                                            {{__('forms.document_number')}} *
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input class="default-input"
                                                       wire:model="employee_request.documents.number"
                                                       type="text" id="documents_number"
                                        />
                                    </x-slot>
                                    @error('employee_request.documents.number')
                                    <x-slot name="error">
                                        <x-forms.error>
                                            {{$message}}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>
                            </x-forms.form-row>
                            <x-forms.form-row class="mb-4.5 flex flex-col gap-6   xl:flex-row">
                                <x-forms.form-group class="xl:w-1/2">
                                    <x-slot name="label">
                                        <x-forms.label for="documents_issued_by" class="default-label">
                                            {{__('forms.document_issued_by')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input class="default-input"
                                                       wire:model="employee_request.documents.issued_by"
                                                       type="text" id="documents_issued_by"
                                                       placeholder="{{__('Орган яким виданий документ')}}"/>
                                    </x-slot>
                                    @error('employee_request.documents.issued_by')
                                    <x-slot name="error">
                                        <x-forms.error>
                                            {{$message}}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>
                                <x-forms.form-group class="xl:w-1/2">
                                    <x-slot name="label">
                                        <x-forms.label for="documents_issued_at" class="default-label">
                                            {{__('forms.document_issued_at')}}
                                        </x-forms.label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <x-forms.input-date  id="document_issued_at"  wire:model="employee_request.documents.issued_at"
                                                       />
                                    </x-slot>
                                    @error('employee_request.documents.issued_at')
                                    <x-slot name="message">
                                        <x-forms.error>
                                            {{$message}}
                                        </x-forms.error>
                                    </x-slot>
                                    @enderror
                                </x-forms.form-group>
                            </x-forms.form-row>
                            <x-forms.form-row class="mb-4.5 mt-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center ">
                                <div  class="xl:w-1/4 text-left">
                                    <x-secondary-button wire:click="closeModalModel()">
                                        {{__('forms.close')}}
                                    </x-secondary-button>
                                </div>
                                <div class="xl:w-1/4 text-right">
                                    <x-button type="submit" class="default-button">
                                        {{__('forms.save')}}
                                    </x-button>
                                </div>
                            </x-forms.form-row>
                        </x-slot>
       </x-forms.forms-section-modal>
    </x-slot>
</x-dialog-modal>




