<x-forms.form-row class="">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="degree" class="default-label">
                {{__('forms.institutionName')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="employeeRequest.scienceDegree.institutionName" type="text"
                           id="institutionName"/>
        </x-slot>
        @error('employeeRequest.scienceDegree.institutionName')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="speciality" class="default-label">
                {{__('forms.speciality')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="employeeRequest.scienceDegree.speciality"
                           type="text"
                           id="speciality"/>
        </x-slot>
        @error('employeeRequest.scienceDegree.speciality')
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
            <x-forms.label for="speciality" class="default-label">
                {{__('forms.diplomaNumber')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="employeeRequest.scienceDegree.diplomaNumber" type="text"
                           id="speciality"/>
        </x-slot>
        @error('employeeRequest.scienceDegree.diplomaNumber')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="issuedDate" class="default-label">
                {{__('forms.issuedDate')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date id="issuedDate" wire:model="employeeRequest.scienceDegree.issuedDate"/>


        </x-slot>
        @error('employeeRequest.scienceDegree.issuedDate')
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
            <x-forms.label for="educationCountry" class="default-label">
                {{__('forms.degree')}}*
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="employeeRequest.scienceDegree.degree" type="text"
                id="educationCountry"
            >
                <x-slot name="option">
                    <option>{{__('forms.degree')}}</option>
                    @foreach($this->dictionaries['SCIENCE_DEGREE'] as $k=>$scienceDegree)
                        <option value="{{$k}}">{{$scienceDegree}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>

        </x-slot>
        @error('employeeRequest.scienceDegree.degree')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="educationCountry" class="default-label">
                {{__('forms.country')}}*
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="employeeRequest.scienceDegree.country" type="text"
                id="educationCountry"
            >
                <x-slot name="option">
                    <option>{{__('forms.country')}}</option>
                    @foreach($this->dictionaries['COUNTRY'] as $k=>$country)
                        <option value="{{$k}}">{{$country}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>

        </x-slot>
        @error('employeeRequest.scienceDegree.country')
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
            <x-forms.label for="city" class="default-label">
                {{__('forms.city')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="employeeRequest.scienceDegree.city"
                           type="text"
                           id="city"/>
        </x-slot>
        @error('employeeRequest.scienceDegree.city')
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



