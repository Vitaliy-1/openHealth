<div>
    <x-section-navigation class="breadcrumb-form">
        <x-slot name="title">{{ $pageTitle  }}</x-slot>
    </x-section-navigation>

    <section class="section-form">
        <form
            wire:submit="save"
            class="form"
            x-data="{
                employeeType: $wire.entangle('form.party.employeeType'),
                isDoctor() {
                    return {{ Js::from(config('ehealth.doctors_type')) }}.includes(this.employeeType);
                }
            }"
        >
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

            <div class="form-button-group">
                <button type="button" class="button-minor">
                    {{__('forms.cancel')}}
                </button>
                <button type="submit" class="button-primary">
                    {{__('forms.save')}}
                </button>
{{--                <div class='form-row lg:w-1/2 sm:w-1/2'>--}}
{{--                    <div class="form-group group pb-4">--}}
{{--                        <select--}}
{{--                            required--}}
{{--                            id="publicOfferKnedp"--}}
{{--                            wire:model="knedp"--}}
{{--                            aria-describedby="{{ $hasPublicOfferKnedpError ? 'publicOfferKnedpErrorHelp' : '' }}"--}}
{{--                            class="input-select text-gray-800 {{ $hasPublicOfferKnedpError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"--}}
{{--                        >--}}
{{--                            <option value="_placeholder_" selected hidden>-- {{ __('forms.select') }} --</option>--}}

{{--                            @foreach($getCertificateAuthority as $k => $certificate_type)--}}
{{--                                <option value="{{ $certificate_type['id'] }}">{{ $certificate_type['name'] }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

{{--                        @if($hasPublicOfferKnedpError)--}}
{{--                            <p id="publicOfferKnedpErrorHelp" class="text-error">--}}
{{--                                {{ $errors->first('knedp') }}--}}
{{--                            </p>--}}
{{--                        @endif--}}

{{--                        <label for="publicOfferKnedp" class="label z-10">--}}
{{--                            {{ __('forms.KNEDP') }}--}}
{{--                        </label>--}}
{{--                    </div>--}}

{{--                    <div class="form-group group py-4">--}}
{{--                        <x-forms.file--}}
{{--                            required--}}
{{--                            wire:model="keyContainerUpload"--}}
{{--                            file="{{ $keyContainerUpload?->getClientOriginalName() }}"--}}
{{--                            aria-describedby="{{ $hasPublicOfferFileError ? 'publicOfferFileErrorHelp' : '' }}"--}}
{{--                            :id="'keyContainerUpload'"--}}
{{--                        />--}}

{{--                        @if($hasPublicOfferFileError)--}}
{{--                            <p id="publicOfferFileErrorHelp" class="text-error">--}}
{{--                                {{ $errors->first('keyContainerUpload') }}--}}
{{--                            </p>--}}
{{--                        @endif--}}

{{--                        <label for="keyContainerUpload" class="label z-10">--}}
{{--                            {{ __('forms.key_container_upload') }} *--}}
{{--                        </label>--}}
{{--                    </div>--}}

{{--                    <div class="form-group group">--}}
{{--                        <input--}}
{{--                            required--}}
{{--                            type="password"--}}
{{--                            placeholder=" "--}}
{{--                            id="publicOfferPassword"--}}
{{--                            wire:model="password"--}}
{{--                            aria-describedby="{{ $hasPublicOfferPasswordError ? 'publicOfferPasswordErrorHelp' : '' }}"--}}
{{--                            class="input {{ $hasPublicOfferPasswordError ? 'input-error border-red-500 focus:border-red-500' : ''}} peer"--}}
{{--                        />--}}

{{--                        @if($hasPublicOfferPasswordError)--}}
{{--                            <p id="publicOfferPasswordErrorHelp" class="text-error">--}}
{{--                                {{ $errors->first('password') }}--}}
{{--                            </p>--}}
{{--                        @endif--}}

{{--                        <label for="publicOfferPassword" class="label z-10">--}}
{{--                            {{ __('forms.password') }}--}}
{{--                        </label>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <button wire:click="signedComplete('signedContent')" type="button" class="button-primary">
                    {{ __('forms.send_for_approval') }}
                </button>
            </div>
        </form>
    </section>

    <x-forms.loading />
</div>
