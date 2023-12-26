<div>
    <x-section-title>
        <x-slot name="title">{{ __('Зарееструвати заклад ') }}</x-slot>
        <x-slot name="description">{{ __('Зарееструвати заклад ') }}</x-slot>
    </x-section-title>
    <x-forms.forms-section class="max-w-4xl m-auto" submit="register">
        <x-slot name="form">
            <div class="p-6.5">
                @if ($currentStep == 1)
                    @include('livewire.registration.step._step_edrpou')
                @endif
                @if ($currentStep == 2)
                    @include('livewire.registration.step._step_owner')
                @endif
                @if ($currentStep == 3)
                    @include('livewire.registration.step._step_contact')
                @endif
                @if ($currentStep == 4)
                    @include('livewire.registration.step._step_residence_address')
                @endif
                @if ($currentStep == 5)
                    @include('livewire.registration.step._step_accreditation')
                @endif
                @if ($currentStep == 6)
                    @include('livewire.registration.step._step_license')
                @endif
                @if ($currentStep == 7)
                    @include('livewire.registration.step._step_additional_information')
                @endif
                @if ($currentStep == 8)
                    @include('livewire.registration.step._step_public_offer')
                @endif
                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row justify-between	items-center ">
                    <div class="xl:w-1/4">
                        @if ($currentStep != 1 )
                            <x-button wire:click="decreaseStep()">
                                {{__('Назад')}}
                            </x-button>
                        @endif
                    </div>
                    <div class="xl:w-1/4">
                        <x-button class="btn-primary" wire:click="increaseStep()">
                            @if($currentStep == $totalSteps)
                            {{__('Зарееструвати заклад')}}
                               @else
                               {{__('Далі')}}
                            @endif
                        </x-button>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-forms.forms-section>
</div>

<script>

</script>
