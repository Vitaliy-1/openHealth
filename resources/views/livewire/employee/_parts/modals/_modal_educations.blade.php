<x-forms.form-row :gap="'gap-2'">
    <x-forms.form-group class="w-1/2">
        <x-slot name="label">
            <x-forms.label for="degree" class="default-label">
                {{__('forms.degree')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="employeeRequest.education.degree"
                id="educationCountry"
            >
                <x-slot name="option">
                    <option>{{__('forms.select')}}</option>
                    @foreach($this->dictionaries['EDUCATION_DEGREE'] as $k=>$country)
                        <option value="{{$k}}">{{$country}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>

        </x-slot>
        @error('employeeRequest.education.degree')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="w-1/2">
        <x-slot name="label">
            <x-forms.label for="institutionName" class="default-label">
                {{__('forms.institutionName')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="employeeRequest.education.institutionName" type="text"
                           id="institutionName"/>
        </x-slot>
        @error('employeeRequest.education.institutionName')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>
<x-forms.form-row :gap="'gap-2'" :cols="'xl:flex-wrap'">

    <x-forms.form-group class="w-1/2">
        <x-slot name="label">
            <x-forms.label for="speciality" class="default-label">
                {{__('forms.speciality')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="employeeRequest.education.speciality"
                           type="text"
                           id="speciality"/>
        </x-slot>
        @error('employeeRequest.education.speciality')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="w-1/2">
        <x-slot name="label">
            <x-forms.label for="educationCountry" class="default-label">
                {{__('forms.country')}}*
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.dynamic-select :options="$dictionaries['COUNTRY']"
                                    property="employeeRequest.education.country"/>
        </x-slot>
        @error('employeeRequest.education.country')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>
<x-forms.form-row :gap="'gap-2'" :cols="'xl:flex-wrap'">

    <x-forms.form-group class="w-1/2">
        <x-slot name="label">
            <x-forms.label for="city" class="default-label">
                {{__('forms.city')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="employeeRequest.education.city"
                           type="text"
                           id="city"/>
        </x-slot>
        @error('employeeRequest.education.city')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="w-1/2">
        <x-slot name="label">
            <x-forms.label for="speciality" class="default-label">
                {{__('forms.diplomaNumber')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="employeeRequest.education.diplomaNumber"
                           type="text"
                           id="diplomaNumber"/>
        </x-slot>
        @error('employeeRequest.education.diplomaNumber')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>
<x-forms.form-row :gap="'gap-2'" :cols="'xl:flex-wrap'">

    <x-forms.form-group class="w-1/2">
        <x-slot name="label">
            <x-forms.label for="speciality" class="default-label">
                {{__('forms.issuedDate')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date wire:model="employeeRequest.education.issuedDate"
                                id="issuedDate"/>
        </x-slot>
        @error('employeeRequest.education.issuedDate')
        <x-slot name="error">
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



