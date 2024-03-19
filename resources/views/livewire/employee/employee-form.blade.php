<div>

    <x-section-title>
        <x-slot name="title">
            {{$mode === 'edit' ? __('Редагувати співробітника') : __('Додати співробітника')}}
        </x-slot>
        <x-slot name="description">
            {{$mode === 'edit' ? __('Редагувати співробітника') : __('Додати співробітника')}}
        </x-slot>
    </x-section-title>

    <div class="flex bg-white  p-6 flex-col ">


                @include('livewire.employee._parts._employee')
                @include('livewire.employee._parts._documents')
                @include('livewire.employee._parts._education')
                @include('livewire.employee._parts._specialities')
{{--                @include('livewire.employee._parts._positions')--}}
                @include('livewire.employee._parts._role')
                @include('livewire.employee._parts._science_degree')
                @include('livewire.employee._parts._qualifications')

                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center ">
                    <div class="xl:w-1/4 text-left">
                        <x-secondary-button wire:click="closeModal()">
                            {{__('Назад')}}
                        </x-secondary-button>
                    </div>
                    <div class="xl:w-1/4 text-right">
                        <button wire:click="sendApiRequest()" type="button" class="btn-primary">
                            {{__('Відправити на затвердження ')}}
                        </button>
                    </div>
                </div>
                <div wire:loading role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                         viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"/>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"/>
                    </svg>
                </div>

 </div>
    @if($showModal == 'educations')
        @include('livewire.employee._parts.modals._modal_education')
    @elseif($showModal == 'documents')
        @include('livewire.employee._parts.modals._modal_documents')
    @elseif($showModal == 'specialities')
        @include('livewire.employee._parts.modals._modal_specialities')
    @elseif($showModal == 'positions')
        @include('livewire.employee._parts.modals._modal_positions')
    @elseif($showModal == 'role')
        @include('livewire.employee._parts.modals._modal_role')
    @elseif($showModal == 'science_degree')
        @include('livewire.employee._parts.modals._modal_science_degree')
    @elseif($showModal == 'qualifications')
        @include('livewire.employee._parts.modals._modal_qualifications')
    @endif
    @if($success['status'])
    <div class="flex fixed  bottom-[1.5rem] w-2/6 z-100 right-2  w-border-l-6 border-[#34D399] bg-[#34D399] bg-opacity-[15%] px-7 py-8 shadow-md dark:bg-[#1B1B24] dark:bg-opacity-30 md:p-3">
        <div class="mr-5 flex h-9 w-full max-w-[36px] items-center justify-center rounded-lg bg-[#34D399]">
            <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.2984 0.826822L15.2868 0.811827L15.2741 0.797751C14.9173 0.401867 14.3238 0.400754 13.9657 0.794406L5.91888 9.45376L2.05667 5.2868C1.69856 4.89287 1.10487 4.89389 0.747996 5.28987C0.417335 5.65675 0.417335 6.22337 0.747996 6.59026L0.747959 6.59029L0.752701 6.59541L4.86742 11.0348C5.14445 11.3405 5.52858 11.5 5.89581 11.5C6.29242 11.5 6.65178 11.3355 6.92401 11.035L15.2162 2.11161C15.5833 1.74452 15.576 1.18615 15.2984 0.826822Z" fill="white" stroke="white"></path>
            </svg>
        </div>
        <div class="w-full">
            <h5 class="mb-3 text-sm font-bold text-black dark:text-[#34D399]">
                Дані успішно збережені
            </h5>
        </div>
    </div>
    @endif
    @if($error['status'])
        <div id="errorMessage" x-data="{ isVisible: true }" x-show="isVisible" x-init="setTimeout(() => isVisible = false, 3000)" class="flex fixed  bottom-[1.5rem] w-2/6  right-2 border-l-6 border-[#F87171] bg-[#F87171] bg-opacity-[15%] px-7 py-8 shadow-md dark:bg-[#1B1B24] dark:bg-opacity-30 md:p-9">
            <div class="mr-5 flex h-9 w-full max-w-[36px] items-center justify-center rounded-lg bg-[#F87171]">
                <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.4917 7.65579L11.106 12.2645C11.2545 12.4128 11.4715 12.5 11.6738 12.5C11.8762 12.5 12.0931 12.4128 12.2416 12.2645C12.5621 11.9445 12.5623 11.4317 12.2423 11.1114C12.2422 11.1113 12.2422 11.1113 12.2422 11.1113C12.242 11.1111 12.2418 11.1109 12.2416 11.1107L7.64539 6.50351L12.2589 1.91221L12.2595 1.91158C12.5802 1.59132 12.5802 1.07805 12.2595 0.757793C11.9393 0.437994 11.4268 0.437869 11.1064 0.757418C11.1063 0.757543 11.1062 0.757668 11.106 0.757793L6.49234 5.34931L1.89459 0.740581L1.89396 0.739942C1.57364 0.420019 1.0608 0.420019 0.740487 0.739944C0.42005 1.05999 0.419837 1.57279 0.73985 1.89309L6.4917 7.65579ZM6.4917 7.65579L1.89459 12.2639L1.89395 12.2645C1.74546 12.4128 1.52854 12.5 1.32616 12.5C1.12377 12.5 0.906853 12.4128 0.758361 12.2645L1.1117 11.9108L0.758358 12.2645C0.437984 11.9445 0.437708 11.4319 0.757539 11.1116C0.757812 11.1113 0.758086 11.111 0.75836 11.1107L5.33864 6.50287L0.740487 1.89373L6.4917 7.65579Z" fill="#ffffff" stroke="#ffffff"></path>
                </svg>
            </div>
            <div class="w-full">

                <ul>
                    <li class="leading-relaxed text-[#CD5D5D]">
                        {{$error['message']}}
                    </li>
                </ul>
            </div>
        </div>
    @endif
    <script>


    </script>
</div>



