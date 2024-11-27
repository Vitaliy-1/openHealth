@props(['message'])


<span {{$attributes->merge(['class' => 'text-xs pt-1 text-red-600 dark:text-red-400 error-message'])}}> {{ $message ?? $slot }}.</span>
