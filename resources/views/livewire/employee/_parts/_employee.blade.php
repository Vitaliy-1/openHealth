<div>
    <div class="w-full mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{__('forms.personalData')}}
        </h5>
        <x-forms.form-row class=" ">
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="lastName" class="default-label">
                        {{__('forms.lastName')}} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="employeeRequest.party.lastName" type="text"
                                   id="lastName"/>
                </x-slot>
                @error('employeeRequest.party.lastName')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="firstName" class="default-label">
                        {{__('forms.firstName')}} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="employeeRequest.party.firstName" type="text"
                                   id="firstName"/>
                </x-slot>
                @error('employeeRequest.party.firstName')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="secondName" class="default-label">
                        {{__('forms.secondName')}} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="employeeRequest.party.secondName" type="text"
                                   id="secondName"/>
                </x-slot>
                @error('employeeRequest.party.secondName')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>

        <x-forms.form-row class=" ">
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="birthDate" class="default-label">
                        {{__('forms.birthDate')}} *
                    </x-forms.label>
                </x-slot>

                <x-slot name="input">
                    <x-forms.input-date :maxDate="now()->subYears(18)->format('Y-m-d')" id="birthDate"
                                   wire:model="employeeRequest.party.birthDate"/>
                </x-slot>
                @error('employeeRequest.party.birthDate')
                <x-slot name="error">

                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="email" class="default-label">
                        {{__('forms.email')}} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="employeeRequest.party.email" type="text"
                                   id="email" placeholder="{{__('E-mail')}}"/>
                </x-slot>
                @error('employeeRequest.party.email')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label class="default-label" for="taxId">
                        {{__('forms.number')}} {{__('forms.RNOCPP')}} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input maxlength="10" class="default-input" checked
                                   wire:model="employeeRequest.party.taxId" type="text" id="taxId" name="taxId"/>
                </x-slot>
                @error('employeeRequest.party.taxId')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>
        <x-forms.form-row class="">
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="position" class="default-label">
                        {{__('forms.position')}} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.dynamic-select :options="$dictionaries['POSITION']" property="employeeRequest.party.position" />
                </x-slot>
                @error('employeeRequest.party.position')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="employeeType" class="default-label">
                        {{__('forms.role')}}*
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.select
                        class="default-input" wire:model="employeeRequest.party.employeeType" type="text"
                        id="employeeType"
                    >
                        <x-slot name="option">
                            <option> {{__('forms.select')}} {{__('forms.role')}}</option>
                            @foreach($this->dictionaries['EMPLOYEE_TYPE'] as $k=>$employeeType)
                                <option value="{{$k}}">{{$employeeType}}</option>
                            @endforeach
                        </x-slot>
                    </x-forms.select>

                </x-slot>
                @error('employeeRequest.party.employeeType')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="startDate" class="default-label">
                        {{__('forms.startDateWork')}}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">

                    <x-forms.input-date   id="startDate"
                                   wire:model="employeeRequest.party.startDate"
                    />
                </x-slot>
                @error('employeeRequest.party.startDate')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label for="workingExperience" class="default-label">
                        {{__('forms.workingExperience')}}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.input class="default-input" wire:model="employeeRequest.party.workingExperience"
                                   type="text"
                                   id="workingExperience"/>
                </x-slot>
                @error('employeeRequest.party.workingExperience')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>
        <x-forms.form-row class=" ">
            <x-forms.form-group class="w-full">
                <x-slot name="label">
                    <x-forms.label class="default-label" for="aboutMyself">
                        {{__('forms.aboutMyself')}}
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.textarea
                        class="default-input" checked wire:model="employeeRequest.party.aboutMyself" type="text"
                        id="aboutMyself" name="taxId"/>
                </x-slot>
                @error('employeeRequest.party.aboutMyself')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>
        <x-forms.form-row :cols="'flex-col'" :gap="'gap-1'">
            <x-forms.form-group class="xl:w-1/3">
                <x-slot name="label">
                    <x-forms.label name="employeeGender">
                        {{__('forms.gender')}} *
                    </x-forms.label>
                </x-slot>
                <x-slot name="input">
                    <x-forms.select
                        class="default-input" wire:model="employeeRequest.party.gender"
                        id="employeeGender"
                    >
                        <x-slot name="option">
                            <option>{{__('forms.select')}} {{__('forms.gender')}}</option>
                            @foreach($this->dictionaries['GENDER'] as $k=>$gender )
                                <option value="{{$k}}">{{$gender}}</option>
                            @endforeach
                        </x-slot>
                    </x-forms.select>
                </x-slot>
                @error('employeeRequest.party.gender')
                <x-slot name="error">
                    <x-forms.error>
                        {{$message}}
                    </x-forms.error>
                </x-slot>
                @enderror
            </x-forms.form-group>
        </x-forms.form-row>
        <x-forms.form-row :cols="'flex-col'" :gap="'gap-0'">
            <x-forms.label name="label" class="default-label">
                {{__('forms.phones')}} *
            </x-forms.label>
            <x-forms.form-phone :phones="$employeeRequest->party['phones'] ?? []" :property="'employeeRequest.party'"/>
        </x-forms.form-row>
        <x-forms.form-row class="justify-end">
            <div class="xl:w-1/4 text-right">
                <x-button  x-show="!employeeId" wire:click="store('party')" type="submit" class="default-button max-w-[150px]">
                    {{__('forms.save')}}
                </x-button>
            </div>
        </x-forms.form-row>
    </div>

</div>
