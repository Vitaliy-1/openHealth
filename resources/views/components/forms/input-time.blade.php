@props(['disabled' => false, 'time' => ''])

<div class="relative">
    <input type="time" {{ $disabled ? 'disabled' : '' }} value="{{ $time }}" {!! $attributes->merge(['class' => 'default-input']) !!}>
    <div class="inset-y-0 flex items-center ps-3.5 pointer-events-none">
        <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 8a1 1 0 0 1 1 1v3h2a1 1 0 1 1 0 2h-3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1zm0-6a10 10 0 1 0 0 20 10 10 0 0 0 0-20zM4 12a8 8 0 1 1 16 0 8 8 0 0 1-16 0z"></path>
        </svg>
    </div>
</div>
