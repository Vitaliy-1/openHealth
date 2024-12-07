<x-forms.form-row class="">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="documentsType" class="default-label">
                {{__('forms.documentType')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select id="documentsType"
                            wire:model.defer="employeeRequest.documents.type"
                            class="default-select">
                <x-slot name="option">
                    <option>{{__('Обрати тип')}}</option>
                    @foreach($this->dictionaries['DOCUMENT_TYPE'] as $k=>$document )
                        <option value="{{$k}}">{{$document}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>
        </x-slot>
        @error('employeeRequest.documents.type')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="documentsNumber" class="default-label">
                {{__('forms.documentNumber')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="employeeRequest.documents.number"
                           type="text" id="documentsNumber"
            />
        </x-slot>
        @error('employeeRequest.documents.number')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>
<x-forms.form-row class="">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="documentsIssuedBy" class="default-label">
                {{__('forms.documentIssuedBy')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="employeeRequest.documents.issuedBy"
                           type="text" id="documentsIssuedBy"
                           placeholder="{{__('Орган яким виданий документ')}}"/>
        </x-slot>
        @error('employeeRequest.documents.issuedBy')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="documentsIssuedAt" class="default-label">
                {{__('forms.documentIssuedAt')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date id="documentIssuedAt" wire:model="employeeRequest.documents.issuedAt"
            />
        </x-slot>
        @error('employeeRequest.documents.issuedAt')
        <x-slot name="message">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>
<x-forms.form-row class="mb-4.5 mt-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center ">
    <div class="xl:w-1/4 text-left">
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




