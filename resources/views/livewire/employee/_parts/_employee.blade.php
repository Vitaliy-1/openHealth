<fieldset class="fieldset">
    <legend class="legend">
        <h2>{{__('forms.personalData')}}</h2>
    </legend>

    <div class="form-row-3">
        <div class="form-group group relative">
            <input
                wire:model="employeeRequest.party.lastName"
                type="text"
                name="lastName"
                id="lastName"
                class="input peer @error('employeeRequest.party.lastName') input-error @enderror"
                placeholder=" "
                required
            />

            <label for="lastName" class="label">
                {{__('forms.lastName')}}
            </label>

            @error('employeeRequest.party.lastName')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="employeeRequest.party.firstName"
                   type="text"
                   name="firstName"
                   id="firstName"
                   class="input peer @error('employeeRequest.party.firstName') input-error @enderror"
                   placeholder=" "
                   required
            />
            <label for="firstName" class="label">
                {{__('forms.firstName')}}
            </label>

            @error('employeeRequest.party.firstName')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>

        <div class="form-group group">
            <input
                wire:model="employeeRequest.party.secondName"
                type="text"
                name="secondName"
                id="secondName"
                class="input peer @error('employeeRequest.party.secondName') input-error @enderror"
                placeholder=" "
                required
            />
            <label for="secondName" class="label">
                {{__('forms.secondName')}}
            </label>

            @error('employeeRequest.party.secondName')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>
    </div>

    <div class="form-row-4">
        <div class="form-group group">
            <label for="employeeGender" class="sr-only">{{__('forms.select')}} {{__('forms.gender')}}</label>
            <select wire:model="employeeRequest.party.gender" id="employeeGender" class="input-select peer" required>
                <option selected>{{__('forms.gender')}} *</option>
                @foreach($this->dictionaries['GENDER'] as $k=>$gender )
                    <option value="{{$k}}">{{$gender}}</option>
                @endforeach
            </select>

            @error('employeeRequest.party.gender')
            <p class="text-error">
                {{$message}}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
            </svg>

            <input wire:model="employeeRequest.party.birthDate"
                   datepicker
                   type="text"
                   name="birthDate"
                   id="birthDate"
                   class="input default-datepicker peer @error('employeeRequest.party.birthDate') input-error @enderror"
                   placeholder=" "
                   required
            />

            <label for="birthDate" class="label">
                {{__('forms.birthDate')}}
            </label>

            @error('employeeRequest.party.birthDate')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>


        <div class="form-group group">
            <svg class="svg-input w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2.038 5.61A2.01 2.01 0 0 0 2 6v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6c0-.12-.01-.238-.03-.352l-.866.65-7.89 6.032a2 2 0 0 1-2.429 0L2.884 6.288l-.846-.677Z"/>
                <path d="M20.677 4.117A1.996 1.996 0 0 0 20 4H4c-.225 0-.44.037-.642.105l.758.607L12 10.742 19.9 4.7l.777-.583Z"/>
            </svg>

            <input wire:model="employeeRequest.party.email"
                   type="text"
                   name="email"
                   id="email"
                   class="input peer @error('employeeRequest.party.email') input-error @enderror"
                   placeholder=" "
                   required
            />
            <label for="email" class="label">
                {{__('forms.email')}}
            </label>

            @error('employeeRequest.party.email')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="employeeRequest.party.taxId"
                   type="text"
                   id="taxId"
                   name="taxId"
                   class="input peer @error('employeeRequest.party.taxId') input-error @enderror"
                   placeholder=" "
                   required
            />

            <label for="taxId" class="label">
                {{__('forms.number')}} {{__('forms.RNOCPP')}}
            </label>

            @error('employeeRequest.party.taxId')
            <p class="text-error">
                {{$message}}
            </p>
            @enderror
        </div>
    </div>

    <div class="form-row-4">

        <div class="form-group group">
            <label for="employeeType" class="sr-only">{{__('forms.roleChoose')}}</label>
            <select wire:model.live="employeeRequest.party.employeeType"
                    wire:update="getEmployeeDictionaryPosition()"
                    id="employeeType"
                    class="input-select peer @error('employeeRequest.party.employeeType') input-error @enderror"
                    required
            >
                <option selected>{{__('forms.roleChoose')}} *</option>
                @foreach($this->dictionaries['EMPLOYEE_TYPE'] as $k=>$employeeType)
                    <option value="{{$k}}">{{$employeeType}}</option>
                @endforeach
            </select>

            @error('employeeRequest.party.employeeType')
            <p class="text-error">
                {{$message}}
            </p>
            @enderror
        </div>

        <div class="form-group group">
            <label for="position" class="sr-only">{{__('forms.positionChoose')}}</label>
            <select wire:model="employeeRequest.party.position"
                    id="position"
                    class="input-select peer @error('employeeRequest.party.position') input-error @enderror"
                    required
            >
                <option selected>{{__('forms.positionChoose')}} *</option>
                @foreach($this->dictionaries['POSITION'] as $k => $position)
                    <option value="{{$k}}">{{$position}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group group">
            <svg class="svg-input" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
            </svg>

            <input wire:model="employeeRequest.party.startDate"
                   datepicker
                   type="text"
                   name="startDate"
                   id="startDate"
                   class="input default-datepicker peer @error('employeeRequest.party.startDate') input-error @enderror"
                   placeholder=" "
                   required
            />

            <label for="startDate" class="label">
                {{__('forms.startDateWork')}}
            </label>

            @error('employeeRequest.party.startDate')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>

        <div class="form-group group">
            <input wire:model="employeeRequest.party.workingExperience"
                   type="number"
                   id="workingExperience"
                   name="workingExperience"
                   class="input peer @error('employeeRequest.party.workingExperience') input-error @enderror"
                   placeholder=" "
                   data-input-counter
                   data-input-counter-min="0"
            />

            <label for="workingExperience" class="label">
                {{__('forms.workingExperience')}}
            </label>

            @error('employeeRequest.party.workingExperience')
                <p class="text-error">
                    {{$message}}
                </p>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group group">
            <label for="aboutMyself" class="label-secondary text-gray-500 dark:text-gray-400">
                {{__('forms.aboutMyself')}}
            </label>

            <textarea wire:model="employeeRequest.party.aboutMyself"
                      id="aboutMyself"
                      class="textarea"
                      rows="4"
            >

            </textarea>
        </div>
    </div>

    {{-- Using Alpine to dynamically add and remove phone input fields --}}
    <div class="mb-4" x-data="{ phones: $wire.entangle('employeeRequest.party.phones') }">

        <template x-for="(phone, index) in phones">
            <div class="form-row-3 md:mb-0">

                <div class="form-group group">
                    <label for="phoneType" class="sr-only">{{__('forms.typeMobile')}}</label>
                    <select x-model = "phone.type" id="phoneType" class="input-select peer" required>
                        <option selected>{{__('forms.typeMobile')}} *</option>
                        @foreach($this->dictionaries['PHONE_TYPE'] as $k => $phoneType )
                            <option value="{{$k}}">{{$phoneType}}</option>
                        @endforeach
                    </select>

                    @error('employeeRequest.party.phones.type')
                        <p class="text-error">
                            {{$message}}
                        </p>
                    @enderror
                </div>

                <div class="form-group group">
                    <svg class="svg-input w-5 top-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7.978 4a2.553 2.553 0 0 0-1.926.877C4.233 6.7 3.699 8.751 4.153 10.814c.44 1.995 1.778 3.893 3.456 5.572 1.68 1.679 3.577 3.018 5.57 3.459 2.062.456 4.115-.073 5.94-1.885a2.556 2.556 0 0 0 .001-3.861l-1.21-1.21a2.689 2.689 0 0 0-3.802 0l-.617.618a.806.806 0 0 1-1.14 0l-1.854-1.855a.807.807 0 0 1 0-1.14l.618-.62a2.692 2.692 0 0 0 0-3.803l-1.21-1.211A2.555 2.555 0 0 0 7.978 4Z"/>
                    </svg>

                    <input x-model="phone.number"
                           type="tel"
                           name="phone"
                           id="phone"
                           class="input peer @error('employeeRequest.party.phones.number') input-error @enderror"
                           placeholder=" "
                           required
                    />
                    <label for="phone" class="label">
                        {{__('forms.phone')}}
                    </label>

                    @error('employeeRequest.party.phones.number')
                    <p class="text-error">
                        {{$message}}
                    </p>
                    @enderror
                </div>

                <template x-if="index == phones.length - 1 & index != 0">
                    <button x-on:click="phones.pop(), index--" {{-- Remove a phone if button is clicked --}}
                            class="item-remove"
                    >
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
                        </svg>

                        {{__('forms.removePhone')}}
                    </button>
                </template>

                <template x-if="index == phones.length - 1">
                    <button x-on:click="phones.push({ type: '', number: '' })" {{-- Add new phone if button is clicked --}}
                            class="item-add lg:justify-self-start"
                            :class="{ 'lg:justify-self-start': index > 0 }" {{-- Apply this style only if it's not a first phone group --}}
                    >
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                        </svg>

                        {{__('forms.addPhone')}}
                    </button>
                </template>
            </div>
        </template>

    </div>
</fieldset>
