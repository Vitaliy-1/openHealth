<div id="info-popup" tabindex="-1" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center flex">
    <div class="relative p-4 w-full max-w-lg h-full md:h-auto">
        <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 md:p-8">
            <div class="mb-4 text-sm font-light text-gray-500 dark:text-gray-400">
                <h3 class="mb-3 text-2xl font-bold text-gray-900 dark:text-white">{{$title ?? ''}}</h3>
                    {{$text}}
            </div>

            {{$button ?? ''}}
        </div>
    </div>
</div>
<div modal-backdrop="" class="bg-gray bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40"></div>
