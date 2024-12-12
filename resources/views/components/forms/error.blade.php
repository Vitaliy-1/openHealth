@props(['message'])


<span {{$attributes->merge(['class' => 'text-sm text-red-600 dark:text-red-400 error-message'])}}> {{ $message ?? $slot }}.</span>
