<div id="declaration-modal"  class="flex overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">

        <div class="bg-white overflow-hidden shadow rounded-lg border">
            {{-- HEADER --}}
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{$declaration_show->fullName}}
                    {{-- <x-status-label :status="$declaration_show->status"></x-status-label> --}}
                </h3>
                <button wire:click="closeDeclaration" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline  dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                    <svg class="ms-2.5 w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            {{-- BODY --}}
            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-gray-200">
                    <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            {{ __('Статус') }}:
                        </dt>
                        <dd class="mt-1 flex text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <x-status-label :status="$declaration_show->status"></x-status-label>
                            @if(!empty($declaration_show->reason_description))
                                <div class="relative" x-data="{ open: false }">
                                    <a x-on:click="open = !open"  x-on:keydown.escape="showDropdown = false"
                                       x-on:click.away="showDropdown = false" type="button" class=" inline ml-3" aria-describedby="tooltipExample">
                                        <svg
                                            data-tooltip-target="tooltip-reason"
                                            class="w-3 h-3 inline cursor-pointer fill-blue-500"
                                            aria-hidden="true"
                                            width="15px"
                                            height="15px"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            data-tooltip-placement="top"> <!-- Укажите, где вы хотите, чтобы tooltip появлялся -->
                                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                        </svg>
                                        <span class="text-xs">{{ __('Причинна зміни статусу') }}</span>
                                    </a>
                                    <div x-show="open"  class="absolute -top-9 left-1/2 -translate-x-1/2 z-10 whitespace-nowrap rounded border-2 border-gray-200 bg-white px-2 py-1 text-center text-sm text-black  transition-all ease-out peer-hover:opacity-100 peer-focus:opacity-100 dark:bg-white dark:text-neutral-900" role="tooltip">
                                        {{$declaration_show->reason_description ?? ''}}
                                    </div>
                                </div>
                            @endif
                        </dd>
                    </div>
                    <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            {{__('Лікар')}}
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{$declaration_show->doctorFullName}}
                        </dd>
                    </div>
                    <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            {{__('Організація')}}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{$declaration_show->legal_entity->name?? ''}}
                        </dd>
                    </div>
                    <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            {{__('Номер декларації')}}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class=" text-bold dark:text-white"> {{$declaration_show->declaration_number}}</span>
                        </dd>
                    </div>
                    <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            {{__('Дата подання декларації')}}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class=" text-bold dark:text-white"> {{$declaration_show->startDateDeclaration}}</span>
                        </dd>
                    </div>
                    @if($declaration_show->endDateDeclaration)

                    <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            {{__('Дата кінцевої діїї декларації')}}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class=" text-bold dark:text-white"> {{$declaration_show->endDateDeclaration}}</span>
                        </dd>
                    </div>
                    @endif
                    <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            {{__('Пацієнт')}}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class=" text-bold dark:text-white"> {{$declaration_show->fullName}}</span>
                        </dd>
                    </div>
                    <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            {{__('Дата народження')}}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class=" text-bold dark:text-white"> {{$declaration_show->birthDate}}</span>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <div class='xl:w-1/4 text-left'>
                    <x-secondary-button wire:click="closeDeclaration">
                        {{ __('Закрити') }}
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40"></div>
