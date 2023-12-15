<div>
    <x-section-title>
        <x-slot name="title">{{ __('Зарееструвати заклад ') }}</x-slot>
        <x-slot name="description">{{ __('Зарееструвати заклад ') }}</x-slot>
    </x-section-title>
    <x-forms.forms-section class="max-w-4xl m-auto" submit="register">
        <x-slot name="form">
            <div class="p-6.5">
                @if ($currentStep == 1)
                    @include('livewire.registration.steep._steep_one')
                @endif
                @if ($currentStep == 2)
                    @include('livewire.registration.steep._steep_two')
                @endif
                @if ($currentStep == 3)
                    @include('livewire.registration.steep._steep_three')
                @endif
                @if ($currentStep == 4)
                    @include('livewire.registration.steep._steep_four')
                @endif
                @if ($currentStep == 5)
                    @include('livewire.registration.steep._steep_five')
                @endif
                @if ($currentStep == 6)
                    @include('livewire.registration.steep._steep_six')
                @endif
                @if ($currentStep == 7)
                    @include('livewire.registration.steep._steep_seven')
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
                            {{__('Далі')}}
                        </x-button>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-forms.forms-section>
</div>

<script>

</script>
