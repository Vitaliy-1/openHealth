<div>


    <x-section-navigation x-data="{ showFilter: false }" class="">
        <x-slot name="title">{{ __('Зарееструвати заклад ') }}</x-slot>
    </x-section-navigation>
    <div class="p-4 mb-4 bg-white border border-gray-200  shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800" >

    <div class="steps">
        <ol class="flex items-center cursor-pointer w-full text-sm font-medium text-center text-gray-500 dark:text-gray-400 sm:text-base">
            @foreach($steps as $key => $step)
                @if($currentStep == $step['step'])
                    <li class="flex md:w-full items-center text-blue-600 dark:text-blue-500 sm:after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                    </svg>
                    {{$step['title']}}
                </span>
                    </li>
                @else
                    <li wire:click="changeStep({{$step['step']}}, '{{$step['property']}}')"
                        class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                    {{$step['title']}}
                </span>
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
    <x-forms.forms-section submit="">
        <x-slot name="title">
            {{ $this->getTitleByStep($currentStep)}}
        </x-slot>
        <x-slot name="description">
            {{  __('forms.step', ['currentSteep' => $currentStep,'totalSteps' => $totalSteps]) }}
        </x-slot>
        <x-slot name="form">
            <div class="grid-cols-1">
                <div class="p-6.5">
                   @foreach($steps as $key=>$view)
                       @if($view['step'] == $currentStep)
                          @include('livewire.legal-entity.step.'.$view['view'])
                        @endif
                   @endforeach
                    <div class="mt-6 flex flex-col gap-6 xl:flex-row justify-between	items-center ">
                        <div class="xl:w-1/4 flex justify-start">
                            @if ($currentStep != 1 )
                                <x-button class="alternative-button" type="button" wire:click="decreaseStep()">
                                    {{__('Назад')}}
                                </x-button>
                            @endif
                        </div>
                        <div class="xl:w-1/4  flex justify-end">
                            @if($currentStep == $totalSteps)
                                <x-button type="click" class="default-button" wire:click="stepPublicOffer">
                                    {{__('forms.send_request')}}
                                </x-button>
                            @elseif($currentStep == 4 )
                                <x-button type="button" class="default-button" wire:click="stepAddress()">
                                    {{__('forms.next')}}
                                </x-button>
                                @else
                                <x-button type="button" class="default-button" wire:click="increaseStep()">
                                    {{__('forms.next')}}
                                </x-button>
                            @endif
                        </div>
                    </div>


                </div>
            </div>
        </x-slot>
    </x-forms.forms-section>
    </div>
</div>
