@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="relative p-4 w-full max-w-lg h-full md:h-auto">
        <div class="relative p-4 bg-white rounded-lg dark:bg-gray-800 md:p-8">
            <div class="mb-4 text-sm font-light text-gray-500 dark:text-gray-400">
                <h3 class="mb-3 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $title ?? '' }}
                </h3>
                    {{ $text }}
            </div>

            {{ $button ?? '' }}
        </div>
    </div>
</x-modal>

<div modal-backdrop="" class="bg-gray bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40"></div>
