<div id="declaration-modal"  class="flex overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class=" font-semibold text-gray-900 dark:text-white">
                    {{$declaration_show->fullName}} <x-status-label :status="$declaration_show->status"></x-status-label>
                </h3>
                <button wire:click="closeDeclaration" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline  dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-3 relative">
                <div class="flex items-center">
                   <p class="text-sm text-gray-500 dark:text-gray-400 ">{{ __('Статус') }}: <x-status-label :status="$declaration_show->status"></x-status-label></p>
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
               </div>

                <p class="text-sm mt-0 mb-1  text-gray-500 dark:text-gray-400">
                    {{__('Лікар')}} :<span class=" text-black dark:text-white">  {{$declaration_show->doctorFullName}} </span>
                </p>
                <p class="text-sm mt-0 mb-1  text-gray-500 dark:text-gray-400">
                    {{__('Організація')}} : <span class=" text-black dark:text-white"> {{$declaration_show->legal_entity->name?? ''}}</span>
                </p>
                <p class="text-sm mt-0 mb-1  text-gray-500 dark:text-gray-400">
                    {{__('Номер декларації')}} : <span class=" text-black dark:text-white"> {{$declaration_show->declaration_number}}</span>
                </p>
                <p class="text-sm mt-0 mb-1  text-gray-500 dark:text-gray-400">
                    {{__('Дата подання декларації')}} : <span class=" text-black dark:text-white"> {{$declaration_show->startDateDeclaration}}</span>
                </p>
                @if($declaration_show->endDateDeclaration)
                    <p class="text-sm mt-0 mb-1  text-gray-500 dark:text-gray-400">
                        {{__('Дата кінцевої діїї декларації')}} : <span class=" text-black dark:text-white"> {{$declaration_show->endDateDeclaration}}</span>
                    </p>
                @endif
                    <p class="text-sm mt-0 mb-1  text-gray-500 dark:text-gray-400">
                        {{__('Пацієнт')}} : <span class=" text-black dark:text-white"> {{$declaration_show->fullName}}</span>
                    </p>
                <p class="text-sm mt-0 mb-1  text-gray-500 dark:text-gray-400">
                    {{__('Дата народження')}} : <span class=" text-black dark:text-white"> {{$declaration_show->birthDate}}</span>
                </p>
            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button wire:click="closeDeclaration" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Закрити
                </button>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40"></div>
