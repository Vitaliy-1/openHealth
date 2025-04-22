<div>
    <x-section-navigation class="breadcrumb-form">
        <x-slot name="title">{{ $pageTitle }}</x-slot>
    </x-section-navigation>

    <section
        class="section-form"
        x-data="{
            employeeType: $wire.entangle('form.party.employeeType'),
            isDoctor() {
                return {{ Js::from(config('ehealth.doctors_type')) }}.includes(this.employeeType);
            }
        }"
    >
        <form wire:submit.prevent="save" class="form">
            @include('livewire.employee._parts._employee')
            @include('livewire.employee._parts._documents')

            <template x-if="isDoctor()">
                <div>
                    @include('livewire.employee._parts._education')
                    @include('livewire.employee._parts._specialities')
                    @include('livewire.employee._parts._science_degree')
                    @include('livewire.employee._parts._qualifications')
                </div>
            </template>

            @if (session()->has('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                     role="alert">
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                     role="alert">
                    <span class="font-medium">Помилка!</span> {{ session('error') }}
                </div>
            @endif

            <div x-transition.opacity.duration.500ms
                 class="mt-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">{{ __('forms.sign_with_KEP') }}</h3>
                <p class="mb-6 text-sm text-gray-600 dark:text-gray-300">{{ __('forms.complete_the_interaction_and_sign') }}</p>

                @include('livewire.employee._parts._signature_block')

            </div>

            <div class="form-button-group mt-6">
                <button type="button" class="button-minor">
                    {{__('forms.cancel')}}
                </button>
                <button type="submit" class="button-primary">
                    {{__('forms.save')}}
                </button>
                <button type="button" wire:click="signedComplete" class="button-primary">
                    {{ __('forms.send_for_approval') }}
                </button>
            </div>
        </form>
    </section>

    <x-forms.loading/>
</div>

{{--<x-forms.form-row :cols="'flex-col'" class="">--}}

{{--    <x-forms.form-group>--}}
{{--        <x-slot name="label">--}}
{{--            <x-forms.label class="default-label" for="knedp"--}}
{{--                           name="label">--}}
{{--                {{__('forms.knedp')}} *--}}
{{--            </x-forms.label>--}}
{{--        </x-slot>--}}
{{--        <x-slot name="input">--}}
{{--            <x-forms.select class="default-input"--}}
{{--                            wire:model="form.knedp"--}}
{{--                            id="knedp">--}}
{{--                <x-slot name="option">--}}
{{--                    <option value="">{{__('forms.select')}}</option>--}}
{{--                    @foreach($certificateAuthorities as $k => $certificate_type)--}}
{{--                        <option value="{{ $certificate_type['id'] }}">{{ $certificate_type['name'] }}</option>--}}
{{--                    @endforeach--}}
{{--                </x-slot>--}}
{{--            </x-forms.select>--}}
{{--        </x-slot>--}}
{{--        @error('form.knedp')--}}
{{--        <x-slot name="error">--}}
{{--            <x-forms.error>--}}
{{--                {{$message}}--}}
{{--            </x-forms.error>--}}
{{--        </x-slot>--}}
{{--        @enderror--}}
{{--    </x-forms.form-group>--}}

{{--    <x-forms.form-group class="">--}}
{{--        <x-slot name="label">--}}
{{--            <x-forms.label class="default-label" for="keyContainerUpload"--}}
{{--                           name="label">--}}
{{--                {{__('forms.key_container_upload')}} *--}}
{{--            </x-forms.label>--}}
{{--        </x-slot>--}}
{{--        <x-slot name="input">--}}
{{--            <x-forms.file wire:model="form.keyContainerUpload"--}}
{{--                          :id="'keyContainerUpload'"--}}
{{--                          :file="$this->form->keyContainerUpload?->getClientOriginalName()"--}}
{{--            />--}}
{{--        </x-slot>--}}
{{--        @error('form.keyContainerUpload')--}}
{{--        <x-slot name="error">--}}
{{--            <x-forms.error>--}}
{{--                {{$message}}--}}
{{--            </x-forms.error>--}}
{{--        </x-slot>--}}
{{--        @enderror--}}
{{--    </x-forms.form-group>--}}
{{--    <x-forms.form-group class="">--}}
{{--        <x-slot name="label">--}}
{{--            <x-forms.label class="default-label" for="password"--}}
{{--                           name="label">--}}
{{--                {{__('forms.password')}} *--}}
{{--            </x-forms.label>--}}
{{--        </x-slot>--}}
{{--        <x-slot name="input">--}}
{{--            <x-forms.input class="default-input" wire:model="form.password"--}}
{{--                           type="password" id="password"/>--}}
{{--        </x-slot>--}}
{{--        @error('form.password')--}}
{{--        <x-slot name="error">--}}
{{--            <x-forms.error>--}}
{{--                {{$message}}--}}
{{--            </x-forms.error>--}}
{{--        </x-slot>--}}
{{--        @enderror--}}
{{--    </x-forms.form-group>--}}
{{--</x-forms.form-row>--}}
