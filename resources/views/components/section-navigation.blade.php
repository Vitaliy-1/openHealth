@props(['navigation', 'title' => null, 'description'])

<div {{ $attributes->merge(['class' => 'p-4 bg-white block sm:flex items-center justify-between border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700']) }}>
    <div class="w-full mb-1">
        @if($title)
            <x-section-title>
                <x-slot name="title">{{ $title }}</x-slot>
                <x-slot name="description">{{ $description ?? '' }}</x-slot>
            </x-section-title>
        @endif
        {{ $navigation ?? '' }}
    </div>
</div>
