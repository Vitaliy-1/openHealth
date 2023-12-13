@props(['message'])

<span class="flex items-center font-medium tracking-wide text-danger text-xs mt-1 ml-1">
        {{ $message ?? $slot }}
</span>
