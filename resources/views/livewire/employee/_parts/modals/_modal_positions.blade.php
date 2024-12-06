<x-forms.form-row class="pt-4 grid grid gap-4 grid-cols-2">
    <x-forms.form-group class="">
        <x-slot name="label">
            <x-forms.label for="position" class="default-label">
                {{__('forms.position')}}*
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="employeeRequest.positions.position" type="text"
                id="position"
            >
                <x-slot name="option">
                    <option>{{__('forms.position')}}</option>
                    @foreach($this->dictionaries['POSITION'] as $k=>$position )
                        <option value="{{$k}}">{{$position}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>

        </x-slot>
        @error('employeeRequest.positions.position')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="">
        <x-slot name="label">
            <x-forms.label for="startDate" class="default-label">
                {{__('forms.startDateWork')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="employeeRequest.positions.startDate" type="date"
                           id="startDate"/>
        </x-slot>
        @error('employeeRequest.positions.startDate')
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
            {{__('Закрити')}}
        </x-secondary-button>
    </div>
    <div class="xl:w-1/4 text-right">
        <x-button type="submit" class="btn-primary">
            {{__('Додати Посаду')}}
        </x-button>
    </div>
</x-forms.form-row>

